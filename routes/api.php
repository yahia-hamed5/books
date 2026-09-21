<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
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
