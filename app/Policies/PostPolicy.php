<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    /**
     * Grant all abilities to administrator.
     *
     * @param  \App\Models\User  $user
     * @param  string  $ability
     * @return bool
     */
	public function before(User $user, $ability)
	{
	    if (session('statut') === 'admin') {
	        return true;
	    }
	}

    /**
     * Determine if the given post can be changed by the user.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Post  $post
     * @return bool
     */
    public function change(User $user, Post $post)
    {
        return $user->id === $post->user_id;
    }

}
