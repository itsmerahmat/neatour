<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get query parameters
        $search = $request->input('search', '');
        $perPage = $request->input('perPage', 10);
        $sortField = $request->input('sortField', 'id');
        $sortDirection = $request->input('sortDirection', 'desc');
        
        // Query builder with search
        $query = Category::query();
        
        // Apply search if provided
        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }
        
        // Apply sorting
        $query->orderBy($sortField, $sortDirection);
        
        // Get paginated results
        $categories = $query->paginate($perPage)->withQueryString();
        
        return Inertia::render('category/Index', [
            'categories' => $categories,
            'filters' => [
                'search' => $search,
                'perPage' => $perPage,
                'sortField' => $sortField,
                'sortDirection' => $sortDirection,
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryRequest $request)
    {
        $validated = $request->validated();

        // Handle file upload if image is present
        if ($request->hasFile('img')) {
            $path = $request->file('img')->store('categories', 'public');
            $validated['img'] = Storage::url($path);
        }

        Category::create($validated);

        return redirect()->route('category.index')
            ->with('success', 'Kategori berhasil ditambahkan');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryRequest $request, Category $category)
    {
        $validated = $request->validated();

        // Handle file upload if image is present
        if ($request->hasFile('img')) {
            // Delete old image if exists
            if ($category->img && Storage::exists(str_replace('/storage', 'public', $category->img))) {
                Storage::delete(str_replace('/storage', 'public', $category->img));
            }

            // Save new image
            $path = $request->file('img')->store('categories', 'public');
            $validated['img'] = Storage::url($path);
        }

        $category->update($validated);

        return redirect()->route('category.index')
            ->with('success', 'Kategori berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $category = Category::findOrFail($id);

        // Check if the category has an image and delete it
        if ($category->img) {
            // Convert the URL path to storage path
            $path = str_replace('/storage/', 'public/', $category->img);
            
            // Check if file exists and delete it
            if (Storage::exists($path)) {
                Storage::delete($path);
            } else {
                Log::warning("Could not find image to delete: {$category->img}");
            }
        }

        // Delete the category
        $category->delete();

        return redirect()->route('category.index')
            ->with('success', 'Kategori berhasil dihapus');
    }
}
