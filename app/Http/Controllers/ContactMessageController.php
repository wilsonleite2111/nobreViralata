<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactMessageRequest;
use App\Mail\ContactMessageReceived;
use App\Models\ContactMessage;
use App\Models\Setting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;

class ContactMessageController extends Controller
{
    public function create(): View
    {
        return view('contact');
    }

    public function store(StoreContactMessageRequest $request): RedirectResponse
    {
        // Bots fill every field, including the hidden one; they get the usual answer and nothing is stored.
        if (! $request->filled('website')) {
            $contactMessage = ContactMessage::query()->create($request->validated());
            $recipient = Setting::read(Setting::CONTACT_EMAIL);

            if (filled($recipient)) {
                Mail::to($recipient)->send(new ContactMessageReceived($contactMessage));
            }
        }

        return to_route('contact')->with('contact_status', 'Mensagem enviada! Em breve a gente responde.');
    }
}
