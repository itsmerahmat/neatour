<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Destination;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Collection;

class HomeController extends Controller
{
    /**
     * Earth's radius in kilometers for Haversine formula
     */
    const EARTH_RADIUS_KM = 6371;

    /**
     * Display the home page with nearby destinations
     *
     * @param Request $request
     * @return \Inertia\Response
     */
    public function home(Request $request)
    {
        // Check if user provided their location
        $userLatitude = $request->input('latitude');
        $userLongitude = $request->input('longitude');
        
        // Fetch all published destinations with categories and testimonials to calculate distances and ratings
        $destinations = Destination::where('published', true)
            ->with(['categories', 'testimonials'])
            ->get();
        
        // If user location is available, calculate distances and sort by proximity
        if ($userLatitude && $userLongitude) {
            $destinations = $this->calculateDistancesAndSort($destinations, $userLatitude, $userLongitude);
        }
        
        // Take only the 3 closest destinations
        $nearbyDestinations = $destinations->take(3);
        
        return Inertia::render('landing/Home', [
            'nearbyDestinations' => $nearbyDestinations,
            'images' => $nearbyDestinations->pluck('thumb_image')->flatten(),
        ]);
    }

    /**
     * Calculate distances for each destination using Haversine formula and sort by proximity
     *
     * @param Collection $destinations
     * @param float $userLatitude
     * @param float $userLongitude
     * @return Collection
     */
    private function calculateDistancesAndSort($destinations, $userLatitude, $userLongitude)
    {
        return $destinations->map(function ($destination) use ($userLatitude, $userLongitude) {
            // Calculate distance using Haversine formula
            $distance = $this->haversineDistance(
                $userLatitude,
                $userLongitude,
                $destination->lat,
                $destination->lon
            );
            
            // Add distance to the destination object
            $destination->distance = round($distance, 1);
            
            return $destination;
        })->sortBy('distance'); // Sort by distance ascending
    }

    /**
     * Calculate the great-circle distance between two points using Haversine formula
     *
     * @param float $lat1 Latitude of point 1 in degrees
     * @param float $lon1 Longitude of point 1 in degrees
     * @param float $lat2 Latitude of point 2 in degrees
     * @param float $lon2 Longitude of point 2 in degrees
     * @return float Distance in kilometers
     */
    private function haversineDistance($lat1, $lon1, $lat2, $lon2)
    {
        // Convert latitude and longitude from degrees to radians
        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);
        
        // Haversine formula
        $latDelta = $lat2 - $lat1;
        $lonDelta = $lon2 - $lon1;
        
        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos($lat1) * cos($lat2) * sin($lonDelta / 2) * sin($lonDelta / 2);
             
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        // Distance in kilometers
        $distance = self::EARTH_RADIUS_KM * $c;
        
        return $distance;
    }

    public function katalog(Request $request)
    {
        // Check if user provided their location
        $userLatitude = $request->input('latitude');
        $userLongitude = $request->input('longitude');
        
        // Get filter parameters from request
        $searchQuery = $request->input('searchQuery');
        $selectedCategory = $request->input('categoryId');
        $selectedRating = $request->input('rating');
        $page = $request->input('page', 1);
        $perPage = $request->input('perPage', 9);
        
        // Start building the query for destinations
        $query = Destination::where('published', true)
            ->with(['categories', 'testimonials']);
            
        // Apply search query filter if provided
        if ($searchQuery) {
            $query->where('name', 'like', "%{$searchQuery}%");
        }
        
        // Apply category filter if provided
        if ($selectedCategory) {
            $query->whereHas('categories', function($q) use ($selectedCategory) {
                $q->where('categories.id', $selectedCategory);
            });
        }
        
        // Apply rating filter if provided - fixed to prevent GROUP BY SQL error
        if ($selectedRating) {
            $destinationIds = Testimonial::selectRaw('destination_id, AVG(rating) as avg_rating')
                ->groupBy('destination_id')
                ->havingRaw('AVG(rating) >= ?', [$selectedRating])
                ->pluck('destination_id');
                
            $query->whereIn('id', $destinationIds);
        }
        
        // Get all matching destinations to calculate distances
        $allDestinations = $query->get();
        
        // If user location is available, calculate distances
        if ($userLatitude && $userLongitude) {
            $allDestinations = $this->calculateDistancesAndSort($allDestinations, $userLatitude, $userLongitude);
        }
        
        // Calculate total destinations count after all filters are applied
        $totalCount = $allDestinations->count();
        
        // Calculate last page
        $lastPage = ceil($totalCount / $perPage);
        
        // Get destinations for the current page
        // When loading more (page > 1), we need to return all destinations up to the current page
        // This allows the frontend to append new destinations to the existing ones
        if ($page > 1) {
            $paginatedDestinations = $allDestinations->take($perPage * $page)->values();
        } else {
            $paginatedDestinations = $allDestinations->take($perPage)->values();
        }
        
        // Fetch all categories for filter
        $categories = Category::all();
        
        return Inertia::render('landing/Katalog', [
            'nearbyDestinations' => $paginatedDestinations,
            'categories' => $categories,
            'testimonials' => Testimonial::all(),
            'filters' => [
                'searchQuery' => $searchQuery,
                'categoryId' => $selectedCategory,
                'rating' => $selectedRating,
            ],
            'pagination' => [
                'page' => $page,
                'perPage' => $perPage,
                'totalCount' => $totalCount,
                'lastPage' => $lastPage,
            ],
        ]);
    }
    
    /**
     * Display detailed information about a specific destination.
     *
     * @param Request $request
     * @param string $id
     * @return \Inertia\Response
     */
    public function show(Request $request, $id)
    {
        // Check if user provided their location
        $userLatitude = $request->input('latitude');
        $userLongitude = $request->input('longitude');
        
        // Find the published destination with related data
        $destination = Destination::where('published', true)
            ->with(['categories', 'testimonials'])
            ->findOrFail($id);
            
        // Calculate distance for the main destination if user location is available
        if ($userLatitude && $userLongitude) {
            $distance = $this->haversineDistance(
                $userLatitude,
                $userLongitude,
                $destination->lat,
                $destination->lon
            );
            $destination->distance = round($distance, 1);
        }
        
        // Get testimonials for this destination
        $testimonials = Testimonial::where('destination_id', $id)->get();

        // Fetch other published destinations (limit 3) with testimonials for rating calculation
        $nearbyDestinations = Destination::where('published', true)
            ->where('id', '!=', $id) // Exclude the current destination
            ->with(['categories', 'testimonials'])
            ->limit(3)
            ->get();
        
        // If user location is available, calculate distances and sort by proximity
        if ($userLatitude && $userLongitude) {
            $nearbyDestinations = $this->calculateDistancesAndSort($nearbyDestinations, $userLatitude, $userLongitude);
        }
        
        return Inertia::render('landing/DetailKatalog', [
            'destination' => $destination,
            'nearbyDestinations' => $nearbyDestinations,
            'testimonials' => $testimonials,
        ]);
    }
}
