<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = Category::with(['parent', 'children'])->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Categories fetched successfully',
            'categories' => $categories,
        ], 200);
    }

    public function store(CreateCategoryRequest $request): JsonResponse
    {
        $category = Category::create($request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Category created successfully',
            'category' => $category,
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $category = Category::with(['parent', 'children', 'products'])->find($id);

        if (!$category) {
            return response()->json([
                'status' => 'error',
                'message' => 'Category not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Category fetched successfully',
            'category' => $category,
        ], 200);
    }

    public function update(UpdateCategoryRequest $request, string $id): JsonResponse
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'status' => 'error',
                'message' => 'Category not found',
            ], 404);
        }

        // Prevent setting category as its own parent
        if ($request->has('parent_id') && $request->parent_id === $id) {
            return response()->json([
                'status' => 'error',
                'message' => 'A category cannot be its own parent',
            ], 422);
        }

        $category->update($request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Category updated successfully',
            'category' => $category,
        ], 200);
    }

    public function destroy(string $id): JsonResponse
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'status' => 'error',
                'message' => 'Category not found',
            ], 404);
        }

        $category->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Category deleted successfully',
        ], 200);
    }
}
