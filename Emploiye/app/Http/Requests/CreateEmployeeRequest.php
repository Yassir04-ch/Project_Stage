<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateEmployeeRequest extends FormRequest
{
    public function authorize(): bool {
         return true; 
    }

    public function rules(): array
    {
        return [
            'firstname'      => 'required|string|max:100',
            'lastname'       => 'required|string|max:100',
            'cin'            => 'required|string|unique:users,cin|max:20',
            'telephone'      => 'nullable|string|max:20',
            'adresse'        => 'nullable|string',
            'genre'          => 'required|in:homme,femme',
            'date_naissance' => 'nullable|date|before:today',
            'date_embauche'  => 'nullable|date',
            'type_contrat'   => 'required|in:CDI,CDD,Stage,Freelance,Interim',
            'salaire'        => 'nullable|numeric|min:0',
            'photo'          => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'email'          => 'required|email|unique:users,email',
            'password'       => 'required|string|min:8',
            'role_id'        => 'nullable|exists:roles,id',
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
