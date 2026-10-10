<?php

namespace App\Http\Controllers;

use App\Models\PhotoAlbum;
use Illuminate\Contracts\View\View;

class PhotoAlbumController extends Controller
{
    public function index(): View
    {
        return view('photo-albums.index', [
            'albums' => PhotoAlbum::query()->published()->with('media')->latest('taken_at')->simplePaginate(12),
        ]);
    }

    public function show(PhotoAlbum $photoAlbum): View
    {
        abort_unless($photoAlbum->is_published, 404);

        return view('photo-albums.show', [
            'album' => $photoAlbum,
            'photos' => $photoAlbum->getMedia(PhotoAlbum::PHOTOS),
        ]);
    }
}
