<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Post; // Add the Post model import
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // If validation fails, return error response
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Create the user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Return success response
        return response()->json(['message' => 'User registered successfully', 'user' => $user], 201);
    }

    public function login(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        // If validation fails, return error response
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Check if the user exists
        $user = User::where('email', $request->email)->first();

        // If the user doesn't exist, return an error response
        if (!$user) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Check if the password is correct
        if (!Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Generate JWT token
        try {
            $token = JWTAuth::fromUser($user);
        } catch (\Exception $e) {
            Log::error('JWT token creation failed: ' . $e->getMessage());
            return response()->json(['message' => 'Error generating token'], 500);
        }

        // Create a default post for the user after successful login
        $post = new Post();
        $post->title = 'Welcome Post';  // Default title
        $post->description = 'This is your first post after logging in.'; // Default description
        $post->user_id = $user->id;
        $post->save();

        // Return success response with the token and post
        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user,
            'post' => $post, // Include the created post in the response
        ], 200);
    }
}
