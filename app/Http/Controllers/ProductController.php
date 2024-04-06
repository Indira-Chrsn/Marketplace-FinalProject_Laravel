<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Http\Resources\ProductResource;
use App\Rules\newProductMaxImages;
use App\Rules\NoNegativeValue;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Traits\HasImage;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ProductPostRequest;
use Illuminate\Auth\Events\Validated;

class ProductController extends Controller
{
    use HasImage;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth('sanctum')->user();
        $product_query = Product::with(['category', 'brand', 'user']);

        if ($user) {
            $product_query->where('user_id', $user->id);
        }

        // search by product name keyword
        if ($request->keyword) {
            $product_query->where('name', 'LIKE', '%' . $request->keyword . '%');
        }

        // search by price range
        if ($request->min_price && $request->max_price) {
            $minPrice = $request->get('min_price', null);
            $maxPrice = $request->get('max_price', null);

            $product_query->where(function ($query) use ($minPrice, $maxPrice) {
                if (!is_null($minPrice)) {
                    $query->where('price', '>=', $minPrice);
                }
                if (!is_null($maxPrice)) {
                    $query->where('price', '<=', $maxPrice);
                }
            });
        }

        // filter by category
        if ($request->category) {
            $product_query->whereHas('category', function ($query) use ($request) {
                $query->where('name', $request->category);
            });
        }

        // filter by brand
        if ($request->brand) {
            $product_query->whereHas('brand', function ($query) use ($request) {
                $query->where('name', $request->brand);
            });
        }

        // sort by
        if ($request->sortBy && in_array($request->sortBy, ['id', 'name', 'price'])) {
            $sortBy = $request->sortBy;
        } else {
            $sortBy = 'id';
        }

        // sort order
        if ($request->sortOrder && in_array($request->sortOrder, ['asc', 'desc'])) {
            $sortOrder = $request->sortOrder;
        } else {
            $sortOrder = 'asc';
        }

        $products = $product_query->orderBy($sortBy, $sortOrder)->get();

        return ProductResource::collection($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductPostRequest $request)
    {
        $user = auth('sanctum')->user();

        $validated = $request->validated();

        $image = $validated['image'];
        unset($validated['image']);
        $validated['user_id'] = $user->id;

        $product = Product::create($validated);

        $image = $this->uploadImage($request, 'products', $product);

        if ($product) {
            return response()->json([
                'message' => 'Produk berhasil ditambahkan',
                'product' => $product,
                'image' => $image
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::find($id);

        return ProductResource::make($product)->withDetail();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required|max:500',
            'price' => ['required', 'numeric', new NoNegativeValue],
            'quantity' => ['required', 'integer', new NoNegativeValue],
            'category_id' => ['required', 'integer', new NoNegativeValue],
            'brand_id' => ['required', 'integer', new NoNegativeValue],
            'images' => ['nullable', 'mimes:jpg,jpeg,png', 'max:2048']
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->getMessages();

            return response()->json([
                'success' => 'false',
                'errors' => $errors
            ], 422);
        }

        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Produk tidak ditemukan.'], 404);
        }

        if ($request->hasFile('image')) {
            if ($product->image) {
                $existingImage = $product->image;
                Storage::delete($existingImage->url);
                $existingImage->delete();
            }

            $image = $this->uploadImage($request, 'products', $product);
        }

        $validated = $validator->safe()->except('image');

        $product->update($validated);


        return response()->json([
            'message' => 'Produk berhasil diperbarui.',
            'product' => $product,
            'image' => $image
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Produk tidak ditemukan'], 404);
        }

        if ($product->image) {
            $product->image->delete();
        }

        $product->delete();

        return response()->json(['message' => 'produk berhasil dihapus.']);
    }

    public function restore(string $id)
    {
        $product = Product::withTrashed()->find($id);

        if (!$product) {
            return response()->json(['message' => 'Produk tidak ditemukan'], 404);
        }

        $product->restore();

        if ($product->image) {
            $image = $product->image()->withTrashed();
        }

        $image->restore();

        return response()->json([
            'restored data' => $product
        ]);
    }
}
