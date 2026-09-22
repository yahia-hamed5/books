<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Auth Routes
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

// Users
Route::get('users', [UserController::class, 'index'])->middleware('auth:sanctum');

// Categories CRUD
Route::apiResource('categories', CategoryController::class);

// Brands CRUD
Route::apiResource('brands', BrandController::class);

// Products CRUD
Route::apiResource('products', ProductController::class);

// Author
Route::get('authors', [AuthorController::class, 'index']);
Route::post('authors', [AuthorController::class, 'store']);

// Cart
Route::middleware('auth:sanctum')->prefix('cart')->group(function () {
    Route::get('/', [CartController::class, 'index']);
    Route::post('add-item', [CartController::class, 'store']);
    Route::put('items/{id}', [CartController::class, 'update']);
    Route::delete('items/{id}', [CartController::class, 'destroy']);
    Route::delete('clear', [CartController::class, 'clear']);
});


// CHECKOUT
Route::post('checkout', [OrderController::class,'checkout'])->middleware('auth:sanctum');
