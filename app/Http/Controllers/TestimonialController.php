<?php

namespace App\Http\Controllers;

use App\Models\Testimonial;
use App\Models\Destination;
use App\Traits\DataTableTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class TestimonialController extends Controller
{
    use DataTableTrait;
    
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Define searchable columns
        $searchableColumns = ['comment', 'rating'];
        
        // Define allowed sort fields
        $allowedSortFields = ['id', 'destination_id', 'comment', 'rating', 'created_at', 'updated_at'];
        
        // Process DataTable request with eager loading
        $result = $this->processDataTable(
            $request,
            Testimonial::with('destination'),
            $searchableColumns,
            $allowedSortFields,
            'id',
            'desc'
        );
        
        return Inertia::render('testimonial/Index', [
            'testimonials' => $result['data'],
            'filters' => $result['filters'],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $destinations = Destination::all(['id', 'name']);
        
        return Inertia::render('testimonial/Create', [
            'destinations' => $destinations
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'destination_id' => 'required|exists:destinations,id',
            'comment' => 'required|string|max:255',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        Testimonial::create($validated);

        return Redirect::route('testimonial.index')->with('success', 'Testimonial created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $testimonial = Testimonial::with('destination')->findOrFail($id);
        
        return Inertia::render('testimonial/Show', [
            'testimonial' => $testimonial
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $testimonial = Testimonial::findOrFail($id);
        $destinations = Destination::all(['id', 'name']);
        
        return Inertia::render('testimonial/Edit', [
            'testimonial' => $testimonial,
            'destinations' => $destinations
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $testimonial = Testimonial::findOrFail($id);
        
        $validated = $request->validate([
            'destination_id' => 'required|exists:destinations,id',
            'comment' => 'required|string|max:255',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $testimonial->update($validated);

        return Redirect::route('testimonial.index')->with('success', 'Testimonial updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $testimonial = Testimonial::findOrFail($id);
        $testimonial->delete();
        
        return Redirect::route('testimonial.index')->with('success', 'Testimonial deleted successfully.');
    }
}
