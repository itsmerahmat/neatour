<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Destination;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        // Fetch nearby destinations (limit 3)
        $nearbyDestinations = Destination::with('categories')
            ->limit(3)
            ->get();
        
        // Fetch featured categories
        $categories = Category::limit(4)->get();
        
        // Fetch testimonials
        $testimonials = Testimonial::all();
        
        return Inertia::render('Home', [
            'nearbyDestinations' => $nearbyDestinations,
            'categories' => $categories,
            'testimonials' => $testimonials,
        ]);
    }
}
