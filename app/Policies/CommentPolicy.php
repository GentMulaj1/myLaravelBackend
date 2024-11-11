<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;

class CommentPolicy
{
    /**
     * Determine if the user can view any comments.
     */
    public function viewAny(User $user)
    {
        return true; // Allow all users to view comments
    }

    /**
     * Determine if the user can view a specific comment.
     */
    public function view(User $user, Comment $comment)
    {
        return true; // Allow all users to view a comment
    }

    /**
     * Determine if the user can create a comment.
     */
    public function create(User $user)
    {
        return $user->role === 'user' || $user->role === 'admin';
    }

    /**
     * Determine if the user can update the comment.
     */
    public function update(User $user, Comment $comment)
    {
        return $user->id === $comment->user_id;
    }

    /**
     * Determine if the user can delete the comment.
     */
    public function delete(User $user, Comment $comment)
    {
        return $user->id === $comment->user_id || $user->role === 'admin';
    }
}
