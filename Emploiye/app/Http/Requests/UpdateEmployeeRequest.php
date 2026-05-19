<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool {
         return true; 
    }

    public function rules(): array
    {
        $userId = $this->route('user')?->id;

        return [
            'firstname'      => 'sometimes|string|max:100',
            'lastname'       => 'sometimes|string|max:100',
            'cin'            => "sometimes|string|unique:users,cin,{$userId}|max:20",
            'telephone'      => 'nullable|string|max:20',
            'adresse'        => 'nullable|string',
            'genre'          => 'sometimes|in:homme,femme',
            'date_naissance' => 'nullable|date|before:today',
            'date_embauche'  => 'nullable|date',
            'type_contrat'   => 'sometimes|in:CDI,CDD,Stage,Freelance,Interim',
            'salaire'        => 'nullable|numeric|min:0',
            'photo'          => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'email'          => "sometimes|email|unique:users,email,{$userId}",
            'password'       => 'nullable|string|min:8',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Erreur de validation.',
            'errors'  => $validator->errors(),
        ], 422));
    }
}