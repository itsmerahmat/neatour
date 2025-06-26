<?php

namespace App\Http\Controllers;

use App\Models\Testimonial;
use App\Models\Destination;
use App\Traits\DataTableTrait;
use App\Http\Requests\TestimonialRequest;
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
        
        // Build query with eager loading
        $query = Testimonial::with('destination');
        
        // Filter based on user role
        $user = auth()->user();
        if ($user && $user->role !== 'superadmin') {
            // Regular admin can only see testimonials for their own destinations
            // Use whereHas to filter by relationship
            $query->whereHas('destination', function ($q) use ($user) {
                $q->where('pic_id', $user->id);
            });
        }
        
        // Process DataTable request
        $result = $this->processDataTable(
            $request,
            $query,
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
        // Filter destinations based on user role
        $user = auth()->user();
        
        if ($user->role === 'superadmin') {
            $destinations = Destination::all(['id', 'name']);
        } else {
            $destinations = Destination::where('pic_id', $user->id)->get(['id', 'name']);
        }
        
        return Inertia::render('testimonial/Create', [
            'destinations' => $destinations
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TestimonialRequest $request)
    {
        // Validation and authorization are handled by TestimonialRequest
        $validated = $request->validated();
        
        Testimonial::create($validated);

        // cek jika user login maka redirect ke halaman testimonial
        // jika tidak maka redirect ke halaman detail katalog
        if (auth()->user()) {
            return Redirect::route('testimonial.index')->with('success', 'Testimonial created successfully.');
        }

        return Redirect::route('katalog.detail', ['id' => $request->destination_id])->with('success', 'Testimonial created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $testimonial = Testimonial::with('destination')->findOrFail($id);
        
        // Verify user has permission to view this testimonial
        $user = auth()->user();
        if ($user->role !== 'superadmin' && $testimonial->destination->pic_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }
        
        return Inertia::render('testimonial/Show', [
            'testimonial' => $testimonial
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $testimonial = Testimonial::with('destination')->findOrFail($id);
        
        // Verify user has permission to edit this testimonial
        $user = auth()->user();
        if ($user->role !== 'superadmin' && $testimonial->destination->pic_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }
        
        // Filter destinations based on user role
        if ($user->role === 'superadmin') {
            $destinations = Destination::all(['id', 'name']);
        } else {
            $destinations = Destination::where('pic_id', $user->id)->get(['id', 'name']);
        }
        
        return Inertia::render('testimonial/Edit', [
            'testimonial' => $testimonial,
            'destinations' => $destinations
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TestimonialRequest $request, string $id)
    {
        // Validation and authorization are handled by TestimonialRequest
        $testimonial = Testimonial::findOrFail($id);
        
        $validated = $request->validated();
        $testimonial->update($validated);

        return Redirect::route('testimonial.index')->with('success', 'Testimonial updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $testimonial = Testimonial::with('destination')->findOrFail($id);
        
        // Verify user has permission to delete this testimonial
        $user = auth()->user();
        if ($user->role !== 'superadmin' && $testimonial->destination->pic_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }
        
        $testimonial->delete();
        
        return Redirect::route('testimonial.index')->with('success', 'Testimonial deleted successfully.');
    }
}
