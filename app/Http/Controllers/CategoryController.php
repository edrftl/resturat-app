<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use App\Http\Requests\CategoryStoreRequest;
use App\Http\Requests\CategoryUpdateRequest;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): \Illuminate\Http\JsonResponse
    {
        $category = Category::all();
            return response()->json($category);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Example validation rules
        ]);

        $imageName = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time().'.'.$image->getClientOriginalExtension();
            $image->move(public_path('upload'), $imageName);
        }

        $category = new Category();
        $category->name = $request->input('name');
        $category->image = $imageName; // Save the image file name to the database

        $category->save();

        return response()->json($category, 201);
    }


    /**
     * Display the specified resource.
     */

    public function show($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['error' => 'Category not found'], 404);
        }

        return response()->json(['category' => $category]);
    }

    // Other methods (index, store, update, destroy) go here...


    /**
     * Show the form for editing the specified resource.
     */
//    public function edit(Category $category):View
//    {
//        return view('categories.edit',compact('category'));
//    }




    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        // Delete associated image file if needed
        // Storage::delete($category->image);

        $category->delete();

        return response()->json(['message' => 'Category deleted successfully']);
    }

}
