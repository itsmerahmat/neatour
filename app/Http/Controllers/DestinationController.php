<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Destination;
use App\Models\User;
use App\Traits\DataTableTrait;
use App\Services\ImageKitService;
use App\Http\Requests\DestinationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class DestinationController extends Controller
{
    use DataTableTrait;
    
    protected ImageKitService $imageKitService;

    public function __construct(ImageKitService $imageKitService)
    {
        $this->imageKitService = $imageKitService;
    }
    
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
            'thumb_image' => 'nullable|image|max:10240', // Increased to 10MB for better quality
            'categories' => 'array',
            'categories.*' => 'exists:categories,id'
        ]);

        // Handle image upload
        if ($request->hasFile('thumb_image')) {
            $file = $request->file('thumb_image');
            $fileName = 'destination_' . time() . '_' . $file->getClientOriginalName();
            
            Log::info('Attempting ImageKit upload', [
                'fileName' => $fileName,
                'fileSize' => $file->getSize(),
                'mimeType' => $file->getMimeType()
            ]);
            
            // Upload to ImageKit (ImageKit preserves original quality by default)
            $uploadResult = $this->imageKitService->uploadFile($file, $fileName, [
                'folder' => '/destinations',
                'useUniqueFileName' => true,
                'tags' => ['destination', 'thumbnail'],
                'isPrivateFile' => false,
            ]);

            if ($uploadResult) {
                Log::info('ImageKit upload successful', ['fileId' => $uploadResult['fileId']]);
                $validated['thumb_image'] = $uploadResult['url'];
                $validated['imagekit_file_id'] = $uploadResult['fileId'];
            } else {
                Log::error('ImageKit upload failed for destination');
                return redirect()->back()
                    ->withErrors(['thumb_image' => 'Gagal mengunggah gambar ke ImageKit'])
                    ->withInput();
            }
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
    public function update(Request $request, Destination $destination)
    {
        // Make sure user can only update their own destinations unless superadmin
        $user = auth()->user();
        if ($user->role !== 'superadmin' && $destination->pic_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required|string',
            'facility' => 'required|string',
            'lat' => 'required|numeric',
            'lon' => 'required|numeric',
            'pic_id' => 'required|exists:users,id',
            'published' => 'boolean',
            'thumb_image' => 'nullable|image|max:10240', // Increased to 10MB for better quality
            'categories' => 'array',
            'categories.*' => 'exists:categories,id'
        ]);

        // For non-superadmin, ensure they can't change pic_id
        if ($user->role !== 'superadmin') {
            $validated['pic_id'] = $user->id;
        }

        // Handle image upload
        if ($request->hasFile('thumb_image')) {
            $file = $request->file('thumb_image');
            $fileName = 'destination_' . time() . '_' . $file->getClientOriginalName();
            
            Log::info('Attempting ImageKit upload for update', [
                'fileName' => $fileName,
                'fileSize' => $file->getSize(),
                'mimeType' => $file->getMimeType(),
                'destinationId' => $destination->id
            ]);
            
            // Upload to ImageKit (ImageKit preserves original quality by default)
            $uploadResult = $this->imageKitService->uploadFile($file, $fileName, [
                'folder' => '/destinations',
                'useUniqueFileName' => true,
                'tags' => ['destination', 'thumbnail'],
                'isPrivateFile' => false,
            ]);

            if ($uploadResult) {
                Log::info('ImageKit upload successful for update', ['fileId' => $uploadResult['fileId']]);
                
                // Delete old image from ImageKit if it exists
                if ($destination->imagekit_file_id) {
                    Log::info('Deleting old ImageKit file', ['oldFileId' => $destination->imagekit_file_id]);
                    $this->imageKitService->deleteFile($destination->imagekit_file_id);
                }

                $validated['thumb_image'] = $uploadResult['url'];
                $validated['imagekit_file_id'] = $uploadResult['fileId'];
            } else {
                Log::error('ImageKit upload failed for update', ['destinationId' => $destination->id]);
                return redirect()->back()
                    ->withErrors(['thumb_image' => 'Gagal mengunggah gambar ke ImageKit'])
                    ->withInput();
            }
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
        
        // Delete the image from ImageKit if it exists
        if ($destination->imagekit_file_id) {
            $this->imageKitService->deleteFile($destination->imagekit_file_id);
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
