<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, $postId)
    {
        // Validate the incoming request
        $request->validate([
            'content' => 'required|string|max:255',
        ]);

        // Find the post by ID
        $post = Post::find($postId);

        // If the post doesn't exist, return an error response
        if (!$post) {
            return response()->json(['error' => 'Post not found'], 404);
        }

        // Create a new comment and associate it with the post
        $comment = new Comment();
        $comment->content = $request->content;
        $comment->user_id = Auth::id();  // The ID of the currently authenticated user
        $comment->post_id = $postId;     // The ID of the post being commented on
        $comment->save();

        // Return the created comment
        return response()->json(['comment' => $comment], 201);
    }

    public function index($postId)
    {
        // Get the comments for the specified post
        $comments = Comment::where('post_id', $postId)->get();

        return response()->json(['comments' => $comments]);
    }

    public function update(Request $request, $commentId)
    {
        // Validate the incoming request
        $request->validate([
            'content' => 'required|string|max:255',
        ]);

        // Find the comment by ID
        $comment = Comment::find($commentId);

        // If the comment doesn't exist, return an error response
        if (!$comment) {
            return response()->json(['error' => 'Comment not found'], 404);
        }

        // Check if the authenticated user is the owner of the comment
        if (Auth::id() !== $comment->user_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Update the comment
        $comment->content = $request->content;
        $comment->save();

        // Return the updated comment
        return response()->json(['comment' => $comment]);
    }

    public function destroy($commentId)
    {
        // Find the comment by ID
        $comment = Comment::find($commentId);

        // If the comment doesn't exist, return an error response
        if (!$comment) {
            return response()->json(['error' => 'Comment not found'], 404);
        }

        // Check if the authenticated user is the owner of the comment
        if (Auth::id() !== $comment->user_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Delete the comment
        $comment->delete();

        return response()->json(['message' => 'Comment deleted']);
    }
}
