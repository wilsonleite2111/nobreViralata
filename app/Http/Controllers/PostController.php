<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Contracts\View\View;

class PostController extends Controller
{
    public function index(): View
    {
        return view('posts.index', [
            'posts' => Post::query()->published()->with('media')->latest('published_at')->latest('id')->simplePaginate(9),
        ]);
    }

    public function show(Post $post): View
    {
        abort_unless($post->isVisible(), 404);

        return view('posts.show', ['post' => $post]);
    }
}
