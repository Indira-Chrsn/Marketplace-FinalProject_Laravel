<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Traits\HasImage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    use HasImage;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:20',
            'image' => 'required|mimes:jpg,jpeg,png|max:2048'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->getMessages();

            return response()->json([
                'success' => 'false',
                'errors' => $errors
            ], 422);
        }

        $validated = $validator->safe()->only('name');

        $category = Category::create($validated);
        $image = $this->uploadImage($request, 'categories', $category);

        return response()->json([
            'category' => $category,
            'image' => $image
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:20',
            'image' => 'nullable|mimes:jpg,jpeg,png|max:2048'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->getMessages();

            return response()->json([
                'success' => 'false',
                'errors' => $errors
            ], 422);
        }

        $category = Category::find($id);
        if (!$category) {
            return response()->json(['message' => 'Kategori tidak ditemukan.'], 404);
        }

        if ($request->hasFile('image')) {
            if ($category->image) {
                $existingImage = $category->image;
                Storage::delete($existingImage->url);
                $existingImage->delete();
            }

            $image = $this->uploadImage($request, 'categories', $category);
        }

        $validated = $validator->safe()->only('name');

        $category->update($validated);
        
        return response()->json([
            'message' => 'Kategori berhasil diperbarui',
            'category' => $category,
            'image' => $image
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['message' => 'Kategori tidak ditemukan'], 404);
        }

        $category->image->delete();
        $category->delete();

        return response()->json([
            'message' => 'Kategori berhasil dihapus.'
        ]);
    }

    public function restore(string $id)
    {
        $category = Category::withTrashed()->find($id);

        if (!$category) {
            return response()->json(['message' => 'kategori tidak ditemukan']);
        }

        $category->restore();
        $category->image()->withTrashed()->restore();

        return response()->json([
            'restored data' => $category
        ]);
    }
}
