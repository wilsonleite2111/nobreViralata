<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\VideoCategory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    public function index(Request $request): View
    {
        $category = VideoCategory::tryFrom($request->string('categoria')->toString());

        $videos = Video::query()
            ->published()
            ->when($category, fn ($query) => $query->where('category', $category))
            ->latest('published_at')
            ->latest('id')
            ->simplePaginate(12)
            ->withQueryString();

        return view('videos.index', [
            'videos' => $videos,
            'category' => $category,
        ]);
    }
}
