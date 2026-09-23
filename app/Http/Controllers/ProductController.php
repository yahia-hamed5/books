<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Product::with(['author', 'categories', 'brands']);

        // Optional filtering by search keyword
        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('bref', 'like', "%{$search}%");
            });
        }

        // Optional filter by category
        if ($request->filled('category_id')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories.id', $request->query('category_id'));
            });
        }

        // Optional filter by brand
        if ($request->filled('brand_id')) {
            $query->whereHas('brands', function ($q) use ($request) {
                $q->where('brands.id', $request->query('brand_id'));
            });
        }

        // Optional filter by author
        if ($request->filled('author_id')) {
            $query->where('author_id', $request->query('author_id'));
        }

        $perPage = (int) $request->query('per_page', 15);
        $products = $query->latest()->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'message' => 'Products fetched successfully',
            'products' => $products,
        ], 200);
    }

    public function store(CreateProductRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Generate slug if not passed
        if (empty($data['slug'])) {
            $baseSlug = Str::slug($data['name']);
            $slug = $baseSlug;
            $count = 1;
            while (Product::where('slug', $slug)->exists()) {
                $slug = "{$baseSlug}-{$count}";
                $count++;
            }
            $data['slug'] = $slug;
        }

        $product = Product::create($data);

        // Sync relationships
        if ($request->has('category_ids')) {
            $product->categories()->sync($request->category_ids);
        }

        if ($request->has('brand_ids')) {
            $product->brands()->sync($request->brand_ids);
        }

        $product->load(['author', 'categories', 'brands']);

        return response()->json([
            'status' => 'success',
            'message' => 'Product created successfully',
            'product' => $product,
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $product = Product::with(['author', 'categories', 'brands'])->find($id);

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Product fetched successfully',
            'product' => $product,
        ], 200);
    }

    public function update(UpdateProductRequest $request, string $id): JsonResponse
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found',
            ], 404);
        }

        $data = $request->validated();

        // Update slug if name is changed and no slug provided
        if (isset($data['name']) && empty($data['slug']) && $data['name'] !== $product->name) {
            $baseSlug = Str::slug($data['name']);
            $slug = $baseSlug;
            $count = 1;
            while (Product::where('slug', $slug)->where('id', '!=', $id)->exists()) {
                $slug = "{$baseSlug}-{$count}";
                $count++;
            }
            $data['slug'] = $slug;
        }

        $product->update($data);

        if ($request->has('category_ids')) {
            $product->categories()->sync($request->category_ids);
        }

        if ($request->has('brand_ids')) {
            $product->brands()->sync($request->brand_ids);
        }

        $product->load(['author', 'categories', 'brands']);

        return response()->json([
            'status' => 'success',
            'message' => 'Product updated successfully',
            'product' => $product,
        ], 200);
    }

    public function destroy(string $id): JsonResponse
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found',
            ], 404);
        }

        $product->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Product deleted successfully',
        ], 200);
    }
}
