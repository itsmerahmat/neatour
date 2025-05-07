<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Destination;
use App\Models\User;
use App\Traits\DataTableTrait;
use App\Http\Requests\DestinationRequest;
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
        
        // Build query with eager loading
        $query = Destination::with(['pic', 'categories']);
        
        // Filter based on user role
        $user = auth()->user();
        if ($user && $user->role !== 'superadmin') {
            // Regular admin can only see their own destinations
            $query = $query->where('pic_id', $user->id);
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

        // Only superadmin can select any user as PIC, regular admin must use themselves
        $user = auth()->user();
        $users = $user->role === 'superadmin' ? User::all() : User::where('id', $user->id)->get();
        
        return Inertia::render('destination/Create', [
            'categories' => $categories,
            'users' => $users
        ]);
    }

    /**
     * Store a newly created destination in storage.
     */
    public function store(DestinationRequest $request)
    {
        $validated = $request->validated();

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
        // Verify user has permission to view this destination
        $user = auth()->user();
        if ($user->role !== 'superadmin' && $destination->pic_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

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
        // Make sure user can only edit their own destinations unless superadmin
        $user = auth()->user();
        if ($user->role !== 'superadmin' && $destination->pic_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $destination->load('categories');
        $categories = Category::all();
        
        // Only superadmin can change the PIC
        $users = $user->role === 'superadmin' ? User::all() : User::where('id', $user->id)->get();
        
        return Inertia::render('destination/Edit', [
            'destination' => $destination,
            'categories' => $categories,
            'users' => $users
        ]);
    }

    /**
     * Update the specified destination in storage.
     */
    public function update(DestinationRequest $request, Destination $destination)
    {
        // Make sure user can only update their own destinations unless superadmin
        $user = auth()->user();
        if ($user->role !== 'superadmin' && $destination->pic_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validated();

        // For non-superadmin, ensure they can't change pic_id
        if ($user->role !== 'superadmin') {
            $validated['pic_id'] = $user->id;
        }

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
        // Make sure user can only delete their own destinations unless superadmin
        $user = auth()->user();
        if ($user->role !== 'superadmin' && $destination->pic_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }
        
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
