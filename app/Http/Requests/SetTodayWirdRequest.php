<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SetTodayWirdRequest extends FormRequest
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
        return [
            'group_id' => 'required|exists:groups,id',
            'hifz_page' => 'nullable|integer|min:1|max:604',
            'tilawah_juz' => 'nullable|integer|min:1|max:30',
            'sama_hizb' => 'nullable|integer|min:1|max:60',
            'weekly_tahder_from' => 'nullable|integer|min:1|max:600',
            'tajweed_dars' => 'nullable|integer|min:1',
            'tafseer_dars' => 'nullable|integer|min:1',
        ];
    }
}
