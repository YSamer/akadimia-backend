<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MakeUserDoneWirdsRequest extends FormRequest
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
            'user_id' => 'nullable|exists:users,id',
            'date' => 'nullable|date|date_format:Y-m-d',
            // Validate boolean fields
            'tilawah_juz_done' => 'nullable|boolean',
            'sama_hizb_done' => 'nullable|boolean',
            'weekly_tahder_done' => 'nullable|boolean',
            'hifz_night_tahder_done' => 'nullable|boolean',
            'hifz_before_tahder_done' => 'nullable|boolean',
            'hifz_tafser_done' => 'nullable|boolean',
            'hifz_tadabor_done' => 'nullable|boolean',
            'hifz_waqfa_text' => 'nullable|string',
            'hifz_dabt_tilawah_done' => 'nullable|boolean',
            'salat_hifz_done' => 'nullable|boolean',
            'halaqah_grade' => 'nullable|integer|min:1|max:10',
            'sard_shikh_grade' => 'nullable|integer|min:1|max:5',
            'sard_rafiq_grade' => 'nullable|integer|min:1|max:5',
            'tafseer_dars_done' => 'nullable|boolean',
            'tajweed_dars_done' => 'nullable|boolean',
            'fwaed_text' => 'nullable|string',
        ];
    }
}
