<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JustificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'absence_id' => 'required|exists:absences,id',

            'justified_by' => 'required|exists:users,id',

            'type' => 'required|string|in:medical,family,mission,other',

            'reason' => 'required|string|max:500',

            'proof_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ];
    }
}