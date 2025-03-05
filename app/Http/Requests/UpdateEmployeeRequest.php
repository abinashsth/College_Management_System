<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|string|in:male,female,other',
            'address' => 'nullable|string',
            'contact_number' => 'nullable|string|max:20',
            'email' => [
                'required',
                'email',
                Rule::unique('employees', 'email')->ignore($this->employee)
            ],
            'designation' => 'required|string|max:100',
            'department' => 'required|string|max:100',
            'joining_date' => 'required|date',
            'employee_type' => 'required|string|in:Permanent,Contractual',
            'profile_picture' => 'nullable|image|max:2048',
            'salary_amount' => 'nullable|numeric|min:0',
            'salary_effective_date' => 'nullable|date',
            'payment_type' => 'nullable|string|in:monthly,bi-weekly,weekly',
        ];
    }
}