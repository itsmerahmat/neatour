<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Destination;
use App\Models\User;
use App\Traits\DataTableTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class DestinationController extends Controller
{
    use DataTableTrait;
    
    /**
     * Display a listing of the destinations.
     */
    public function index(Request $request)
    {
        // Define searchable columns
        $searchableColumns = ['name', 'content', 'facility'];
        
        // Define allowed sort fields
        $allowedSortFields = ['id', 'name', 'pic_id', 'published', 'created_at', 'updated_at'];
        
        // Process DataTable request with eager loading
        $result = $this->processDataTable(
            $request,
            Destination::with(['pic', 'categories']),
            $searchableColumns,
            $allowedSortFields,
            'id',
            'desc'
        );
        
        return Inertia::render('destination/Index', [
            'destinations' => $result['data'],
            'filters' => $result['filters'],
        ]);
    }

    /**
     * Show the form for creating a new destination.
     */
    public function create()
    {
        $categories = Category::all();
        $users = User::all();
        
        return Inertia::render('destination/Create', [
            'categories' => $categories,
            'users' => $users
        ]);
    }

    /**
     * Store a newly created destination in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required|string',
            'facility' => 'required|string',
            'lat' => 'required|numeric',
            'lon' => 'required|numeric',
            'pic_id' => 'required|exists:users,id',
            'published' => 'boolean',
            'thumb_image' => 'nullable|image|max:2048',
            'categories' => 'array',
            'categories.*' => 'exists:categories,id'
        ]);

        // Handle image upload
        if ($request->hasFile('thumb_image')) {
            $path = $request->file('thumb_image')->store('destinations', 'public');
            $validated['thumb_image'] = Storage::url($path);
        }

        // Create the destination
        $destination = Destination::create($validated);
        
        // Sync categories
        if (isset($validated['categories'])) {
            $destination->categories()->sync($validated['categories']);
        }

        return redirect()->route('destination.index')
            ->with('success', 'Destinasi berhasil ditambahkan.');
    }

    /**
     * Display the specified destination.
     */
    public function show(Destination $destination)
    {
        $destination->load(['pic', 'categories']);
        
        return Inertia::render('destination/Show', [
            'destination' => $destination
        ]);
    }

    /**
     * Show the form for editing the specified destination.
     */
    public function edit(Destination $destination)
    {
        $destination->load('categories');
        $categories = Category::all();
        $users = User::all();
        
        return Inertia::render('destination/Edit', [
            'destination' => $destination,
            'categories' => $categories,
            'users' => $users
        ]);
    }

    /**
     * Update the specified destination in storage.
     */
    public function update(Request $request, Destination $destination)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required|string',
            'facility' => 'required|string',
            'lat' => 'required|numeric',
            'lon' => 'required|numeric',
            'pic_id' => 'required|exists:users,id',
            'published' => 'boolean',
            'thumb_image' => 'nullable|image|max:2048',
            'categories' => 'array',
            'categories.*' => 'exists:categories,id'
        ]);

        // Handle image upload
        if ($request->hasFile('thumb_image')) {
            // Delete old image if it exists
            if ($destination->thumb_image) {
                $oldPath = str_replace('/storage/', '', $destination->thumb_image);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }

            $path = $request->file('thumb_image')->store('destinations', 'public');
            $validated['thumb_image'] = Storage::url($path);
        } else {
            // Don't update the image if no new one was provided
            unset($validated['thumb_image']);
        }

        // Update the destination
        $destination->update($validated);
        
        // Sync categories
        if (isset($validated['categories'])) {
            $destination->categories()->sync($validated['categories']);
        }

        return redirect()->route('destination.index')
            ->with('success', 'Destinasi berhasil diperbarui.');
    }

    /**
     * Remove the specified destination from storage.
     */
    public function destroy(Destination $destination)
    {
        // Delete the image if it exists
        if ($destination->thumb_image) {
            $path = str_replace('/storage/', '', $destination->thumb_image);
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }

        // Delete related testimonials first to avoid foreign key constraint violations
        $destination->testimonials()->delete();
        
        // Then detach categories and delete the destination
        $destination->categories()->detach();
        $destination->delete();

        return redirect()->route('destination.index')
            ->with('success', 'Destinasi berhasil dihapus.');
    }
}
