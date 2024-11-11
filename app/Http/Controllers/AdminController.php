<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * Delete a user and all their posts and comments.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyUser($id)
    {
        // Ensure the logged-in user is an admin (optional if already handled in middleware)
        if (Auth::user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Find the user by ID
        $user = User::find($id);

        // If the user doesn't exist, return a 404 error
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Log the user's information for debugging purposes
        Log::info('Deleting user and related posts/comments', ['user_id' => $id]);

        // Delete all posts and comments associated with the user
        $user->posts()->each(function ($post) {
            // Delete all comments associated with the post
            $post->comments()->delete();
            // Delete the post itself
            $post->delete();
        });

        // Delete any comments the user has made on other users' posts
        $user->comments()->delete();

        // Finally, delete the user
        $user->delete();

        // Log the deletion process completion
        Log::info('User and all related data deleted successfully', ['user_id' => $id]);

        // Return a success message
        return response()->json(['message' => 'User and all related posts and comments deleted successfully']);
    }

    /**
     * Delete a specific post by admin.
     *
     * @param int $postId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyPost($postId)
    {
        // Ensure the logged-in user is an admin (optional if already handled in middleware)
        if (Auth::user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Log the incoming request to delete the post
        Log::info('Attempting to delete post', ['post_id' => $postId]);

        // Find the post by ID
        $post = Post::find($postId);

        // If the post doesn't exist, return a 404 error
        if (!$post) {
            Log::error('Post not found', ['post_id' => $postId]);
            return response()->json(['error' => 'Post not found'], 404);
        }

        // Log the post details for debugging
        Log::info('Post found, deleting comments and post', ['post' => $post]);

        // Delete all comments associated with the post
        $post->comments()->delete();

        // Delete the post itself
        $post->delete();

        // Log the successful deletion
        Log::info('Post deleted successfully', ['post_id' => $postId]);

        // Return success message
        return response()->json(['message' => 'Post deleted successfully']);
    }
}