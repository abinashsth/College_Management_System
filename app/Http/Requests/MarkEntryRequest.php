<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class MarkEntryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user()->can('create marks') || Auth::user()->hasRole('admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'exam_id' => 'required|exists:exams,id',
            'subject_id' => 'required|exists:subjects,id',
            'total_marks' => 'required|numeric|min:0',
            'marks' => 'required|array',
            'marks.*.marks_obtained' => 'nullable|numeric|min:0|max:' . $this->total_marks,
            'marks.*.is_absent' => 'nullable|boolean',
            'marks.*.remarks' => 'nullable|string|max:255',
            'action' => 'required|in:save,submit',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'marks.*.marks_obtained.max' => 'Marks cannot exceed total marks (:max)',
            'marks.*.marks_obtained.min' => 'Marks cannot be negative',
            'marks.*.marks_obtained.numeric' => 'Marks must be a number',
        ];
    }
} 