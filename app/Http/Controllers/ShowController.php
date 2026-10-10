<?php

namespace App\Http\Controllers;

use App\Models\Show;
use Illuminate\Contracts\View\View;

class ShowController extends Controller
{
    public function index(): View
    {
        return view('shows.index', [
            'shows' => Show::query()->published()->upcoming()->oldest('starts_at')->get(),
        ]);
    }

    public function show(Show $show): View
    {
        abort_unless($show->is_published, 404);

        return view('shows.show', ['show' => $show]);
    }
}
