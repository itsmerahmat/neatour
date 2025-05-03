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

    public function katalog()
    {
        // Fetch published destinations (limit 9) with testimonials for rating calculation
        $nearbyDestinations = Destination::where('published', true)
            ->with(['categories', 'testimonials'])
            ->limit(9)
            ->get();
        
        // Fetch featured categories
        $categories = Category::limit(4)->get();
        
        // Fetch testimonials
        $testimonials = Testimonial::all();
        
        return Inertia::render('landing/Katalog', [
            'nearbyDestinations' => $nearbyDestinations,
            'categories' => $categories,
            'testimonials' => $testimonials,
        ]);
    }
    
    /**
     * Display detailed information about a specific destination.
     *
     * @param string $id
     * @return \Inertia\Response
     */
    public function show($id)
    {
        // Find the published destination with related data
        $destination = Destination::where('published', true)
            ->with(['categories', 'testimonials'])
            ->findOrFail($id);
        
        // Get testimonials for this destination
        $testimonials = Testimonial::where('destination_id', $id)->get();

        // Fetch nearby published destinations (limit 3) with testimonials for rating calculation
        $nearbyDestinations = Destination::where('published', true)
            ->with(['categories', 'testimonials'])
            ->limit(3)
            ->get();
        
        // Get related published destinations based on categories (limit 3)
        $categoryIds = $destination->categories->pluck('id');
        $relatedDestinations = Destination::where('published', true)
            ->with('testimonials')
            ->whereHas('categories', function($query) use ($categoryIds) {
                $query->whereIn('category_id', $categoryIds);
            })
            ->where('id', '!=', $id) // Exclude the current destination
            ->limit(3)
            ->get();
        
        return Inertia::render('landing/DetailKatalog', [
            'nearbyDestinations' => $nearbyDestinations,
            'destination' => $destination,
            'testimonials' => $testimonials,
            'relatedDestinations' => $relatedDestinations,
        ]);
    }
}
