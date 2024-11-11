<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public route for getting authenticated user details
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

// Routes requiring no authentication
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Routes requiring authentication
Route::middleware(['auth:api'])->group(function () {
    // Post routes
    Route::post('/posts', [PostController::class, 'store']);
    Route::put('/posts/{id}', [PostController::class, 'update']);
    Route::delete('/posts/{id}', [PostController::class, 'destroy']);

    // Comment routes
    Route::post('/posts/{post}/comments', [CommentController::class, 'store']);  // Add comment to a specific post
    Route::get('/posts/{post}/comments', [CommentController::class, 'index']);   // Get comments for a specific post
    Route::put('/comments/{comment}', [CommentController::class, 'update']);     // Update a comment
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy']); // Delete a comment
});

// Admin routes
Route::middleware(['auth:api', \App\Http\Middleware\AdminMiddleware::class])->group(function () {
    Route::delete('/admin/users/{id}', [AdminController::class, 'destroyUser']);
    Route::delete('/admin/posts/{postId}', [AdminController::class, 'destroyPost']);
});
