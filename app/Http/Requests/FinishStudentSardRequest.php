<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FinishStudentSardRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'group_id' => 'required|exists:groups,id',
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date|date_format:Y-m-d',
            'duration_hours' => 'required|integer|min:0|max:24',
            'duration_minutes' => 'required|integer|min:0|max:59',
            'grade' => 'required|numeric|min:0|max:5',
            'comment' => 'nullable|string|max:255',
        ];

        return $rules;
    }

}