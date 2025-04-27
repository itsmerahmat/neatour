<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use App\Traits\DataTableTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class CategoryController extends Controller
{
    use DataTableTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Define searchable columns
        $searchableColumns = ['name'];
        
        // Define allowed sort fields
        $allowedSortFields = ['id', 'name', 'created_at', 'updated_at'];
        
        // Process DataTable request
        $result = $this->processDataTable(
            $request,
            Category::query(),
            $searchableColumns,
            $allowedSortFields,
            'id',
            'desc'
        );
        
        return Inertia::render('category/Index', [
            'categories' => $result['data'],
            'filters' => $result['filters'],
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
