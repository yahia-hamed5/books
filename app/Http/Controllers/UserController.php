<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $activeUser = auth()->user();
        if ($activeUser->role !== "admin") {
            return response()->json([
                "message" => "Unauthorized",
                "users" => null,
                "status" => "error"
            ], 403);
        }

        $users = User::all();
        return response()->json([
            "message" => "Users fetched successfully",
            "users" => $users,
            "status" => "success"
        ], 200);
    }
}
