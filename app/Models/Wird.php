<?php

namespace App\Models;

use App\Enums\SectionType;
use App\Enums\WirdType;
use App\Traits\MyFunctions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wird extends Model
{
    use HasFactory, MyFunctions;


    // Define the table associated with the model
    protected $table = 'wirds';

    // Define the fillable fields (to protect against mass assignment)
    protected $fillable = [
        'group_id',
        'group_wird_config_id',
        'date',
        'title',
        'start_from',
        'end_to',
        'file_path',
        'url',
    ];

    // Define the relationships

    /**
     * Get the group that owns the Wird.
     */
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Get the group config associated with the Wird.
     */
    public function groupWirdConfig()
    {
        return $this->belongsTo(GroupWirdConfig::class, 'group_wird_config_id');
    }

    /**
     * Get title the Wird.
     */
    public function getTitle()
    {
        $sectionTypeName = 'ال' . SectionType::from($this->groupWirdConfig->section_type)->arabicName();
        $sectionTypeType = SectionType::from($this->groupWirdConfig->section_type)->type();
        $wirdTypeName = WirdType::from($this->groupWirdConfig->wird_type)->arabicName();

        $start_from = $this->start_from;
        $end_to = $this->end_to ?: ($start_from + $this->groupWirdConfig->change_value - 1);
        $wirdsRange = $this->groupWirdConfig->getWirdsRange($start_from, $end_to);

        $newTitle = $wirdTypeName . ' ';
        $groups = $this->splitConsecutiveNumbers($wirdsRange);

        foreach ($groups as $index => $group) {
            if ($index > 0) {
                $newTitle .= ' و';
            }
            if (count($group) == 1) {
                $newTitle .= $sectionTypeName . ' ' . $this->numberToArabicOrdinal($group[0], $sectionTypeType);
            } elseif (count($group) == 2) {
                $newTitle .= $sectionTypeName . ' ' . $this->numberToArabicOrdinal($group[0], $sectionTypeType)
                    . ' و ' . $this->numberToArabicOrdinal($group[1], $sectionTypeType);
            } elseif (count($group) > 2) {
                $newTitle .= 'من ' . $sectionTypeName . ' ' . $this->numberToArabicOrdinal($group[0], $sectionTypeType)
                    . ' إلى ' . $this->numberToArabicOrdinal(end($group), $sectionTypeType);
            } else {
                $newTitle .= $sectionTypeName . ' ' . $this->numberToArabicOrdinal($start_from, $sectionTypeType);
            }
        }

        return $this->title ?: $newTitle;
    }
}
