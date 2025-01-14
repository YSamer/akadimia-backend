<?php

namespace App\Http\Resources;

use App\Enums\SectionType;
use App\Enums\WirdType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WirdResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        $sectionTypeName = SectionType::from($this->section_type)->arabicName();
        $wirdTypeName = WirdType::from($this->wird_type)->arabicName();
        return [
            'id' => $this->id,
            'group_id' => $this->group_id,
            'group_wird_config_id' => $this->group_wird_config_id,
            'date' => $this->date,
            'title' => $this->getTitle(),
            'start_from' => $this->start_from,
            'end_to' => $this->end_to,
            'file_path' => $this->file_path,
            'url' => $this->url,
            // 
            // 'range' => $this->groupWirdConfig->getWirdsRange($this->start_from, $this->end_to),
            // 'description' => $this->groupWirdConfig->description,
            // 'section_type' => $this->groupWirdConfig->section_type,
            // 'section_type_name' => $sectionTypeName,
            // 'wird_type' => $this->groupWirdConfig->wird_type,
            // 'wird_type_name' => $wirdTypeName,
            // 'under_wird' => $this->groupWirdConfig->under_wird,
            // 'grade' => $this->groupWirdConfig->grade,
            // 'sanction' => $this->groupWirdConfig->sanction,
            'description' => $this->description,
            'section_type' => $this->section_type,
            'section_type_name' => $sectionTypeName,
            'wird_type' => $this->wird_type,
            'wird_type_name' => $wirdTypeName,
            'under_wird' => $this->under_wird,
            'grade' => $this->grade,
            'sanction' => $this->sanction,
            // 
            'change_value' => $this->groupWirdConfig->change_value,
            'repeated_from_list' => $this->groupWirdConfig->repeated_from_list,
            // 
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }

}
