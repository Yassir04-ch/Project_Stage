<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class JobOfferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');
        $sometimes = $isUpdate ? 'sometimes|' : 'required|';

        return [
            'title'         => $sometimes . 'string|min:3|max:255',
            'description'   => $sometimes . 'string',
            'requirements'  => 'nullable|string',
            'location'      => 'nullable|string|max:255',
            'contract_type' => $sometimes . 'in:CDI,CDD,Stage,Freelance',
            'salary_min'    => 'nullable|numeric|min:0',
            'salary_max'    => 'nullable|numeric|min:0|gte:salary_min',
            'status'        => 'nullable|in:open,closed',
            'deadline'      => 'nullable|date|after_or_equal:today',
        ];
    }
}