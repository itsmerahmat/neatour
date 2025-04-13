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
    public function index()
    {
        $categories = Category::all();

        return Inertia::render('category/Index', [
            'categories' => $categories
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    // public function create()
    // {
    //     return Inertia::render('category/Form');
    // }

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
     * Display the specified resource.
     */
    // public function show(Category $category)
    // {
    //     return Inertia::render('category/Show', [
    //         'category' => $category
    //     ]);
    // }

    /**
     * Show the form for editing the specified resource.
     */
    // public function edit(Category $category)
    // {
    //     return Inertia::render('category/Form', [
    //         'category' => $category,
    //         'isEditing' => true
    //     ]);
    // }

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

        // Check if the category has an image and delete it (TODO: not working)
        if ($category->img && Storage::exists(str_replace('/storage', 'public', $category->img))) {
            Storage::delete(str_replace('/storage', 'public', $category->img));
        }

        // Delete the category
        $category->delete();

        return redirect()->route('category.index')
            ->with('success', 'Kategori berhasil dihapus');
    }
}
