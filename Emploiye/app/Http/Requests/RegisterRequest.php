<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool 
    {
         return true; 
    }

    public function rules(): array
    {
        return [
            'firstname' => 'required|string|max:100',
            'lastname'  => 'required|string|max:100',
            'cin'       => 'required|string|max:100',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|string|min:8|confirmed',
            'role_id'   => 'nullable|exists:roles,id',
            'telephone' => 'nullable|string|max:20',
            'adresse'   => 'nullable|string',
            'genre'     => 'nullable|string|in:homme,femme',
            'date_naissance' => 'nullable|date',
            'date_embauche'  => 'nullable|date',
            'type_contrat' => 'nullable|string|in:CDI,CDD,Stage,Freelance,Interim',
            'salaire' => 'nullable|numeric|min:0',
            'photo' => 'nullable|image|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'firstname.required' => 'Le prénom est obligatoire.',
            'lastname.required'  => 'Le nom est obligatoire.',
            'email.required'     => 'email est obligatoire.',
            'email.unique'       => 'Cet email est déjà utilisé.',
            'password.required'  => 'Le mot de passe est obligatoire.',
            'password.min'       => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
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
