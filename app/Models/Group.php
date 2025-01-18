<?php

namespace App\Models;

use App\Enums\WeekDays;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'batch_id',
        'name',
        'image',
        'rate_type',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'batch_id' => 'integer',
    ];

    /**
     * Get the batch that the group belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    /**
     * Get the group configurations associated with the group.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function groupWirdConfigs()
    {
        return $this->hasMany(GroupWirdConfig::class);
    }

    /**
     * Get the wirds associated with the group.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function wirds()
    {
        return $this->hasMany(Wird::class);
    }

    /**
     * Get the members associated with the group.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function members()
    {
        return $this->hasMany(GroupMember::class);
    }

    /**
     * Get the users associated with the group.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function users()
    {
        return $this->morphedByMany(User::class, 'member', 'group_members', 'group_id', 'member_id')
            ->withTimestamps();
    }

    /**
     * Get the admins associated with the group.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function admins()
    {
        return $this->morphedByMany(Admin::class, 'member', 'group_members', 'group_id', 'member_id')
            ->withTimestamps();
    }

    /**
     * Get the teachers associated with the group.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function teachers()
    {
        return $this->morphedByMany(Teacher::class, 'member', 'group_members', 'group_id', 'member_id')
            ->withTimestamps();
    }

    public function exams()
    {
        return $this->morphMany(Exam::class, 'forwardable');
    }

    public function createDefaultWirds()
    {
        $configWirds = [
            // حفظ وجه
            [
                'group_id' => $this->id,
                'wird_type' => 'hifz',
                'section_type' => 'page',
                'under_wird' => null,
                'grade' => 0,
                'sanction' => 0,
                'is_repeated' => 1,
                'is_changed' => 1,
                'is_weekly_changed' => 0,
                'from' => 1,
                'to' => 604,
                'start_from' => 1,
                'end_to' => null,
                'change_value' => 1,
                'repeated_from_list' => null,
                'days' => WeekDays::excludeDays(['thursday', 'friday']),
            ],
            // تلاوة جزء
            [
                'group_id' => $this->id,
                'wird_type' => 'tilawah',
                'section_type' => 'juz',
                'under_wird' => null,
                'grade' => 2,
                'sanction' => 1,
                'is_repeated' => 1,
                'is_changed' => 1,
                'is_weekly_changed' => 0,
                'from' => 1,
                'to' => 30,
                'start_from' => 1,
                'end_to' => null,
                'change_value' => 1,
                'repeated_from_list' => null,
                'days' => WeekDays::values(),
            ],
            // سماع حزب
            [
                'group_id' => $this->id,
                'wird_type' => 'sama',
                'section_type' => 'hizb',
                'under_wird' => null,
                'grade' => 2,
                'sanction' => 1,
                'is_repeated' => 1,
                'is_changed' => 1,
                'is_weekly_changed' => 0,
                'from' => 1,
                'to' => 60,
                'start_from' => 1,
                'end_to' => null,
                'change_value' => 1,
                'repeated_from_list' => null,
                'days' => WeekDays::values(),
            ],
            // التحضير الأسبوعي
            [
                'title' => 'التحضير الأسبوعي',
                'group_id' => $this->id,
                'wird_type' => 'tilawah_or_sama',
                'section_type' => 'page',
                'under_wird' => null,
                'grade' => 1,
                'sanction' => 1,
                'is_repeated' => 1,
                'is_changed' => 0,
                'is_weekly_changed' => 1,
                'from' => 1,
                'to' => 604,
                'start_from' => 2,
                'end_to' => 6,
                'change_value' => 5,
                'repeated_from_list' => null,
                'days' => WeekDays::values(),
            ],
            // التحضير الليلي
            [
                'title' => 'التحضير الليلي',
                'group_id' => $this->id,
                'wird_type' => 'tilawah_or_sama',
                'section_type' => 'page',
                'under_wird' => null,
                'grade' => 1,
                'sanction' => 1,
                'is_repeated' => 1,
                'is_changed' => 1,
                'is_weekly_changed' => 0,
                'from' => 1,
                'to' => 604,
                'start_from' => 1,
                'end_to' => null,
                'change_value' => 1,
                'repeated_from_list' => null,
                'days' => WeekDays::excludeDays(['thursday', 'friday']),
            ],
            // التحضير القبلي
            [
                'title' => 'التحضير القبلي',
                'group_id' => $this->id,
                'wird_type' => 'tilawah_or_sama',
                'section_type' => 'page',
                'under_wird' => null,
                'grade' => 1,
                'sanction' => 1,
                'is_repeated' => 1,
                'is_changed' => 1,
                'is_weekly_changed' => 0,
                'from' => 1,
                'to' => 604,
                'start_from' => 1,
                'end_to' => null,
                'change_value' => 1,
                'repeated_from_list' => null,
                'days' => WeekDays::excludeDays(['thursday', 'friday']),
            ],
            // تفسير المحفوظ
            [
                'title' => 'التفسير',
                'group_id' => $this->id,
                'wird_type' => 'qiraah',
                'section_type' => 'page',
                'under_wird' => null,
                'grade' => 1,
                'sanction' => 1,
                'is_repeated' => 1,
                'is_changed' => 1,
                'is_weekly_changed' => 0,
                'from' => 1,
                'to' => 604,
                'start_from' => 1,
                'end_to' => null,
                'change_value' => 1,
                'repeated_from_list' => null,
                'days' => WeekDays::excludeDays(['thursday', 'friday']),
            ],
            // تدبر المحفوظ
            [
                'title' => 'التدبر',
                'group_id' => $this->id,
                'wird_type' => 'qiraah',
                'section_type' => 'page',
                'under_wird' => null,
                'grade' => 1,
                'sanction' => 1,
                'is_repeated' => 1,
                'is_changed' => 1,
                'is_weekly_changed' => 0,
                'from' => 1,
                'to' => 604,
                'start_from' => 1,
                'end_to' => null,
                'change_value' => 1,
                'repeated_from_list' => null,
                'days' => WeekDays::excludeDays(['thursday', 'friday']),
            ],
            //  الوقفات التدبرية للمحفوظ
            [
                'title' => 'الوقفات التدبرية',
                'group_id' => $this->id,
                'wird_type' => 'kitabah',
                'section_type' => 'none',
                'under_wird' => null,
                'grade' => 1,
                'sanction' => 1,
                'is_repeated' => 1,
                'is_changed' => 1,
                'is_weekly_changed' => 0,
                'from' => 1,
                'to' => 604,
                'start_from' => 1,
                'end_to' => null,
                'change_value' => 1,
                'repeated_from_list' => null,
                'days' => WeekDays::excludeDays(['thursday', 'friday']),
            ],
            // ضبط التلاوة 
            [
                'title' => 'ضبط التلاوة',
                'group_id' => $this->id,
                'wird_type' => 'dars',
                'section_type' => 'page',
                'under_wird' => null,
                'grade' => 2,
                'sanction' => 2,
                'is_repeated' => 1,
                'is_changed' => 1,
                'is_weekly_changed' => 0,
                'from' => 1,
                'to' => 604,
                'start_from' => 1,
                'end_to' => null,
                'change_value' => 1,
                'repeated_from_list' => null,
                'days' => WeekDays::excludeDays(['thursday', 'friday']),
            ],
            // الصلاة بالمحفوظ 
            [
                'title' => 'الصلاة بالمحفوظ',
                'group_id' => $this->id,
                'wird_type' => 'salah_mahsoof',
                'section_type' => 'page',
                'under_wird' => null,
                'grade' => 1,
                'sanction' => 1,
                'is_repeated' => 1,
                'is_changed' => 1,
                'is_weekly_changed' => 0,
                'from' => 1,
                'to' => 604,
                'start_from' => 1,
                'end_to' => null,
                'change_value' => 1,
                'repeated_from_list' => null,
                'days' => WeekDays::values(),
            ],
            // الحلقة 
            [
                'title' => 'الحلقة',
                'group_id' => $this->id,
                'wird_type' => 'halaqah',
                'section_type' => 'page',
                'under_wird' => null,
                'grade' => 10,
                'sanction' => 5,
                'is_repeated' => 1,
                'is_changed' => 1,
                'is_weekly_changed' => 0,
                'from' => 1,
                'to' => 604,
                'start_from' => 1,
                'end_to' => null,
                'change_value' => 1,
                'repeated_from_list' => null,
                'days' => WeekDays::excludeDays(['thursday', 'friday']),
            ],
            //  درس التفسير
            [
                'title' => 'درس التفسير',
                'group_id' => $this->id,
                'wird_type' => 'dars',
                'section_type' => 'none',
                'under_wird' => null,
                'grade' => 5,
                'sanction' => 5,
                'is_repeated' => 1,
                'is_changed' => 0,
                'is_weekly_changed' => 1,
                'from' => 1,
                'to' => 604,
                'start_from' => 1,
                'end_to' => null,
                'change_value' => 1,
                'repeated_from_list' => null,
                'days' => array_values([WeekDays::THURSDAY]),
            ],
            //  درس التجويد
            [
                'title' => 'درس التجويد',
                'group_id' => $this->id,
                'wird_type' => 'dars',
                'section_type' => 'none',
                'under_wird' => null,
                'grade' => 5,
                'sanction' => 5,
                'is_repeated' => 1,
                'is_changed' => 0,
                'is_weekly_changed' => 1,
                'from' => 1,
                'to' => 604,
                'start_from' => 1,
                'end_to' => null,
                'change_value' => 1,
                'repeated_from_list' => null,
                'days' => array_values([WeekDays::FRIDAY]),
            ],
            // التلخيص والفوائد
            [
                'title' => 'التلخيص والفوائد',
                'group_id' => $this->id,
                'wird_type' => 'kitabah',
                'section_type' => 'none',
                'under_wird' => null,
                'grade' => 1,
                'sanction' => 1,
                'is_repeated' => 1,
                'is_changed' => 1,
                'is_weekly_changed' => 0,
                'from' => 1,
                'to' => 604,
                'start_from' => 1,
                'end_to' => null,
                'change_value' => 1,
                'repeated_from_list' => null,
                'days' => array_values([WeekDays::THURSDAY, WeekDays::FRIDAY]),
            ],
        ];
        foreach ($configWirds as $configWird) {
            $this->groupWirdConfigs()->create($configWird);
        }

        $this->save();
    }
}

// case HIFZ = 'hifz';                // حفظ
// case MURAJAA = 'murajaa';          // مراجعه
// case TILAWAH = 'tilawah';          // تلاوة
// case SAMA = 'sama';                // سماع
// case DARS = 'dars';                // درس
// case QIRAAH = 'qiraah';            // قرآءه
// case TILAWAH_OR_SAMA = 'tilawah_or_sama'; // تلاوه او سماع
// case KITABAH = 'kitabah';          // كتابه
// case SARD = 'sard';                // سرد
// case HALAQAH = 'halaqah';          // حلقه
// case SALAH_MAHSOOF = 'salah_mahsoof'; // الصلاة بالمحفوظ
// case MUTOON = 'mutoon';            // متون

// case JUZ = 'juz';
// case HIZB = 'hizb';
// case QUARTER = 'quarter';
// case SURAH = 'surah';
// case PAGE = 'page';
// case AYAH = 'ayah';
// case POETRY_LINE = 'poetry_line';