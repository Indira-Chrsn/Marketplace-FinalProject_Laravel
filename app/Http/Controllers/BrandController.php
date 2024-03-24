<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Brand;
use Illuminate\Support\Facades\Validator;
use App\Traits\HasImage;
use Illuminate\Support\Facades\Storage;

class BrandController extends Controller
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
            'name' => 'required|string|max:50',
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

        $brand = Brand::create($validated);
        $image = $this->uploadImage($request, 'brands', $brand);

        return response()->json([
            'brand' => $brand,
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

        $brand = Brand::find($id);
        if (!$brand) {
            return response()->json(['message' => 'Brand tidak ditemukan.'], 404);
        }

        if ($request->hasFile('image')) {
            if ($brand->image) {
                $existingImage = $brand->image;
                Storage::delete($existingImage->url);
                $existingImage->delete();
            }

            $image = $this->uploadImage($request, 'brands', $brand);
        }

        $validated = $validator->safe()->only('name');

        $brand->update($validated);
        
        return response()->json([
            'message' => 'Brand berhasil diperbarui',
            'brand' => $brand,
            'image' => $image
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $brand = Brand::find($id);
        if (!$brand) {
            return response()->json(['message' => 'Brand tidak ditemukan'], 404);
        }

        $brand->image->delete();
        $brand->delete();

        return response()->json([
            'message' => 'Brand berhasil dihapus.'
        ]);
    }

    public function restore(string $id)
    {
        $brand = Brand::withTrashed()->find($id);

        if (!$brand) {
            return response()->json(['message' => 'Brand tidak ditemukan']);
        }

        $brand->restore();
        $brand->image()->withTrashed()->restore();

        return response()->json([
            'restored data' => $brand
        ]);
    }
}
