<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Destination;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function home()
    {
        // Fetch nearby destinations (limit 3)
        $nearbyDestinations = Destination::with('categories')
            ->limit(3)
            ->get();
        
        // Fetch featured categories
        $categories = Category::limit(4)->get();
        
        // Fetch testimonials
        $testimonials = Testimonial::all();
        
        return Inertia::render('landing/Home', [
            'nearbyDestinations' => $nearbyDestinations,
            'categories' => $categories,
            'testimonials' => $testimonials,
        ]);
    }

    public function katalog()
    {
        // Fetch nearby destinations (limit 3)
        $nearbyDestinations = Destination::with('categories')
            ->limit(3)
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
        // Find the destination with related data
        $destination = Destination::with(['categories', 'testimonials'])
            ->findOrFail($id);
        
        // Get testimonials for this destination
        $testimonials = Testimonial::where('destination_id', $id)->get();

        // Fetch nearby destinations (limit 3)
        $nearbyDestinations = Destination::with('categories')
            ->limit(3)
            ->get();
        
        // Get related destinations based on categories (limit 3)
        $categoryIds = $destination->categories->pluck('id');
        $relatedDestinations = Destination::whereHas('categories', function($query) use ($categoryIds) {
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
