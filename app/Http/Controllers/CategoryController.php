<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
        // $validated = $request->validate([
        //     'name' => 'required|string|max:255|unique:categories',
        //     'img' => 'nullable|string',
        // ]);

        Category::create($request->validated());

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
        // $validated = $request->validate([
        //     'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
        //     'img' => 'nullable|string',
        // ]);

        $category->update($request->validated());

        return redirect()->route('category.index')
            ->with('success', 'Kategori berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->route('category.index')
            ->with('success', 'Kategori berhasil dihapus');
    }
}
