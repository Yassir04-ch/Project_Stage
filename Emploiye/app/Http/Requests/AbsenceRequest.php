<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AbsenceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id'      => 'required|exists:users,id',
            'date'         => 'nullable|date|before_or_equal:today',
            'status'       => 'required|in:present,absent,late,half_day',
            'check_in'     => 'nullable|date_format:H:i',
            'check_out'    => 'nullable|date_format:H:i|after:check_in',
            'note'         => 'nullable|string|max:500',
            'is_justified' => 'boolean',
        ];
    }
    public function messages(): array
    {
        return [
            'user_id.required'      => 'L\'employé est obligatoire',
            'user_id.exists'        => 'Employé introuvable',
            'date.required'         => 'La date est obligatoire',
            'date.before_or_equal'  => 'La date ne peut pas être dans le futur',
            'status.required'       => 'Le statut est obligatoire',
            'status.in'             => 'Statut invalide (present, absent, late)',
            'check_out.after'       => 'L\'heure de sortie doit être après l\'entrée',
        ];
    }
}
