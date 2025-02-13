<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FinishGroupHalaqahRequest extends FormRequest
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
            'date' => 'nullable|date|date_format:Y-m-d',
            'duration_hours' => 'required|integer|min:1|max:24',
            'duration_minutes' => 'required|integer|min:0|max:59',
        ];

        foreach ($this->request->all() as $key => $value) {
            if (preg_match('/^user_(\d+)$/', $key, $matches)) {
                $rules[$key] = 'required|integer|min:0|max:10';
            }
        }

        return $rules;
    }

}