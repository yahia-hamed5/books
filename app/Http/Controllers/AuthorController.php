<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAuthorRequest;
use App\Models\Author;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    public function index()
    {
        $authors = Author::with('products')->get();
        return response()->json([
            "message" => "Authors fetched successfully",
            "authors" => $authors,
            "status" => "success"
        ], 200);
    }
    public function store(CreateAuthorRequest $request)
    {
        $author = Author::create($request->validated());
        return response()->json([
            "message" => "Author created successfully",
            "author" => $author,
            "status" => "success"
        ], 201);
    }
}
