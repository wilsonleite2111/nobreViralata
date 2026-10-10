<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Setting;
use Illuminate\Contracts\View\View;

class BandController extends Controller
{
    public function __invoke(): View
    {
        return view('band', [
            'history' => Setting::read(Setting::BAND_HISTORY),
            'members' => Member::query()->with('media')->orderBy('sort_order')->orderBy('id')->get(),
        ]);
    }
}
