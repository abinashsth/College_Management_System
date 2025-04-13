<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreStudentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Set to true for now, assuming authorization is handled elsewhere (e.g., middleware)
        // Replace with actual authorization logic if needed: return $this->user()->can('create students');
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // User details (for creating the associated User record)
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', Password::defaults(), 'confirmed'],

            // Student details (from the students table migration)
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'gender' => ['nullable', 'string', 'in:male,female,other'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'], // Example image validation
            'date_of_birth' => ['nullable', 'date'],
            'phone_number' => ['nullable', 'string', 'max:255'], // Add specific phone validation if needed
            'emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_number' => ['nullable', 'string', 'max:255'],
            'emergency_contact_relationship' => ['nullable', 'string', 'max:255'],
            
            // Academic details
            'program_id' => ['required', 'integer', 'exists:programs,id'],
            'department_id' => ['required', 'integer', 'exists:departments,id'],
            // Ensure 'classes' table and model exist for this rule
            'class_id' => ['required', 'integer', 'exists:classes,id'],
            'batch_year' => ['nullable', 'integer', 'digits:4'], // E.g., 2024
            'admission_number' => ['nullable', 'string', 'max:255', 'unique:students,admission_number'],
            'admission_date' => ['nullable', 'date'],
            'current_semester' => ['nullable', 'string', 'max:255'], // Consider validation rules if structure is known
            'academic_session_id' => ['nullable', 'integer', 'exists:academic_sessions,id'],

            // Guardian details
            'guardian_name' => ['nullable', 'string', 'max:255'],
            'guardian_relation' => ['nullable', 'string', 'max:255'],
            'guardian_contact' => ['nullable', 'string', 'max:255'],
            'student_address' => ['nullable', 'string'], // Student's own address
            'guardian_address' => ['nullable', 'string', 'max:255'],
            'guardian_occupation' => ['nullable', 'string', 'max:255'],

            // Additional details
            'previous_education' => ['nullable', 'string'],
            'medical_information' => ['nullable', 'string'],
            'remarks' => ['nullable', 'string'],
            // 'documents' JSON field validation might require custom rules or be handled separately
        ];
    }
}
