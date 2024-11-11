<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Add the Auth facade import

class PostController extends Controller
{
    public function store(Request $request) {
        $post = new Post();
        $post->title = $request->title;
        $post->description = $request->description;
        $post->user_id = Auth::id(); // Use Auth::id() instead of auth()->id()
        $post->save();
    
        return response()->json(['post' => $post], 201);
    }
    
    public function destroy($id) {
        $post = Post::find($id);
        if (Auth::id() === $post->user_id || Auth::user()->role === 'admin') { // Use Auth::id() and Auth::user()
            $post->delete();
            return response()->json(['message' => 'Post deleted']);
        }
        return response()->json(['error' => 'Unauthorized'], 403);
    }
    
    public function update(Request $request, $id) {
        $post = Post::find($id);
        if (Auth::id() === $post->user_id) { // Use Auth::id()
            $post->update($request->only('title', 'description'));
            return response()->json(['post' => $post]);
        }
        return response()->json(['error' => 'Unauthorized'], 403);
    }
}
