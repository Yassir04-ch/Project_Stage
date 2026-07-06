<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContactRequest;
use App\Mail\ContactMail;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function send(ContactRequest $request)
    {
        Mail::to('yassirch967@gmail.com')
            ->send(new ContactMail($request->validated()));

        return response()->json([
            'success' => true,
            'message' => 'Votre message a été envoyé avec succès.'
        ]);
    }
}