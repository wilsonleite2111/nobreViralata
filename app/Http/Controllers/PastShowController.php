<?php

namespace App\Http\Controllers;

use App\Models\Show;
use Illuminate\Contracts\View\View;

class PastShowController extends Controller
{
    public function index(): View
    {
        return view('shows.past', [
            'shows' => Show::query()->published()->past()->latest('starts_at')->simplePaginate(20),
        ]);
    }
}
