<?php

namespace App\Http\Controllers;

use App\Models\PhotoAlbum;
use App\Models\Post;
use App\Models\Product;
use App\Models\Show;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Lists every public page, so search engines find content the moment it is published.
     */
    public function __invoke(): Response
    {
        $urls = collect(['home', 'shows.index', 'shows.past', 'news.index', 'photos.index', 'videos.index', 'band', 'store.index', 'contact'])
            ->map(fn (string $route): array => ['loc' => route($route), 'lastmod' => null]);

        $records = [
            'shows.show' => Show::query()->published()->get(),
            'news.show' => Post::query()->published()->get(),
            'photos.show' => PhotoAlbum::query()->published()->get(),
            'store.show' => Product::query()->active()->get(),
        ];

        foreach ($records as $route => $models) {
            $urls = $urls->concat($models->map(fn (Model $model): array => [
                'loc' => route($route, $model),
                'lastmod' => $model->updated_at?->toAtomString(),
            ]));
        }

        return response()
            ->view('sitemap', ['urls' => $urls])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
