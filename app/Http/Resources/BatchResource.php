<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\BatchApplyResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\MyFunctions;

class BatchResource extends JsonResource
{
    use MyFunctions;

    public $resource;
    public $show;
    public function __construct($resource, $show = null)
    {
        $this->resource = $resource;
        $this->show = $show;
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        $user = Auth::user();
        $userApply = ($user && $user instanceof \App\Models\User) ? $user->applies->where('batch_id', $this->id)->first() : null;

        $data = [
            'id' => $this->id,
            'name' => $this->name ? $this->name : $this->numberToArabicOrdinalFemale($this->id),
            'submission_date' => $this->submission_date ? $this->submission_date->format('Y-m-d') : null,
            'start_date' => $this->start_date ? $this->start_date->format('Y-m-d') : null,
            'max_number' => $this->max_number,
            'gender' => $this->gender,
            'groups' => GroupResource::collection($this->whenLoaded('groups')),
            'achievements' => AchievementResource::collection($this->whenLoaded('achievements')),
            'members_num' => $this->allMembers()->count(),
            'users_num' => $this->usersMembers()->count(),
        ];

        if ($user && $user instanceof \App\Models\User) {
            $data['is_apply'] = $user->applies->where('batch_id', $this->id)->first() ? true : false;
        }

        if ($userApply && $this->show) {
            $exams = $this->exams()
                ->where('forwardable_type', 'App\Models\Batch')
                ->where('is_apply', true)
                ->get();
                
            $all_achievements_ids = $this->achievements->pluck('id')->sort()->values()->toArray();
            $applied_achievements_ids = collect($userApply->achievement_ids)->map(fn($id) => (int) $id)->sort()->values()->toArray();
            $data['is_apply_complete'] = $applied_achievements_ids === $all_achievements_ids;
            $data['applies'] = new BatchApplyResource($userApply);
            $data['exams'] = ExamResource::collection($exams);
        }

        return $data;
    }
}
