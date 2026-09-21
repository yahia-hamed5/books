<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateBrandRequest;
use App\Http\Requests\UpdateBrandRequest;
use App\Models\Brand;
use Illuminate\Http\JsonResponse;

class BrandController extends Controller
{
    public function index(): JsonResponse
    {
        $brands = Brand::withCount('products')->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Brands fetched successfully',
            'brands' => $brands,
        ], 200);
    }

    public function store(CreateBrandRequest $request): JsonResponse
    {
        $brand = Brand::create($request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Brand created successfully',
            'brand' => $brand,
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $brand = Brand::with('products')->find($id);

        if (!$brand) {
            return response()->json([
                'status' => 'error',
                'message' => 'Brand not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Brand fetched successfully',
            'brand' => $brand,
        ], 200);
    }

    public function update(UpdateBrandRequest $request, string $id): JsonResponse
    {
        $brand = Brand::find($id);

        if (!$brand) {
            return response()->json([
                'status' => 'error',
                'message' => 'Brand not found',
            ], 404);
        }

        $brand->update($request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Brand updated successfully',
            'brand' => $brand,
        ], 200);
    }

    public function destroy(string $id): JsonResponse
    {
        $brand = Brand::find($id);

        if (!$brand) {
            return response()->json([
                'status' => 'error',
                'message' => 'Brand not found',
            ], 404);
        }

        $brand->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Brand deleted successfully',
        ], 200);
    }
}
