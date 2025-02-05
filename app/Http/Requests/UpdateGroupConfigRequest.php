<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGroupConfigRequest extends FormRequest
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
            'group_id' => 'nullable|integer|between:1,20',
            'tilawah_juz_grade' => 'nullable|integer|between:1,20',
            'tilawah_juz_sanction' => 'nullable|integer|between:1,20',
            'sama_hizb_grade' => 'nullable|integer|between:1,20',
            'sama_hizb_sanction' => 'nullable|integer|between:1,20',
            'weekly_tahder_grade' => 'nullable|integer|between:1,20',
            'weekly_tahder_sanction' => 'nullable|integer|between:1,20',
            'hifz_night_tahder_grade' => 'nullable|integer|between:1,20',
            'hifz_night_tahder_sanction' => 'nullable|integer|between:1,20',
            'hifz_before_tahder_grade' => 'nullable|integer|between:1,20',
            'hifz_before_tahder_sanction' => 'nullable|integer|between:1,20',
            'hifz_tafser_grade' => 'nullable|integer|between:1,20',
            'hifz_tafser_sanction' => 'nullable|integer|between:1,20',
            'hifz_tadabor_grade' => 'nullable|integer|between:1,20',
            'hifz_tadabor_sanction' => 'nullable|integer|between:1,20',
            'hifz_waqfa_grade' => 'nullable|integer|between:1,20',
            'hifz_waqfa_sanction' => 'nullable|integer|between:1,20',
            'hifz_dabt_tilawah_grade' => 'nullable|integer|between:1,20',
            'hifz_dabt_tilawah_sanction' => 'nullable|integer|between:1,20',
            'salat_hifz_grade' => 'nullable|integer|between:1,20',
            'salat_hifz_sanction' => 'nullable|integer|between:1,20',
            'halaqah_grade' => 'nullable|integer|between:1,20',
            'halaqah_sanction' => 'nullable|integer|between:1,20',
            'sard_shikh_grade' => 'nullable|integer|between:1,20',
            'sard_shikh_sanction' => 'nullable|integer|between:1,20',
            'sard_rafiq_grade' => 'nullable|integer|between:1,20',
            'sard_rafiq_sanction' => 'nullable|integer|between:1,20',
            'tafseer_dars_grade' => 'nullable|integer|between:1,20',
            'tafseer_dars_sanction' => 'nullable|integer|between:1,20',
            'tajweed_dars_grade' => 'nullable|integer|between:1,20',
            'tajweed_dars_sanction' => 'nullable|integer|between:1,20',
            'fwaed_grade' => 'nullable|integer|between:1,20',
            'fwaed_sanction' => 'nullable|integer|between:1,20',
            'saturday' => 'nullable|string|in:hifz,tafseer,tajweed,morajaa,ajaza',
            'sunday' => 'nullable|string|in:hifz,tafseer,tajweed,morajaa,ajaza',
            'monday' => 'nullable|string|in:hifz,tafseer,tajweed,morajaa,ajaza',
            'tuesday' => 'nullable|string|in:hifz,tafseer,tajweed,morajaa,ajaza',
            'wednesday' => 'nullable|string|in:hifz,tafseer,tajweed,morajaa,ajaza',
            'thursday' => 'nullable|string|in:hifz,tafseer,tajweed,morajaa,ajaza',
            'friday' => 'nullable|string|in:hifz,tafseer,tajweed,morajaa,ajaza',
        ];
    }
}
