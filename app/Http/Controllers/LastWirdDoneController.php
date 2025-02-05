<?php

// namespace App\Http\Controllers;

// use App\Http\Resources\WirdDoneResource;
// use Illuminate\Http\Request;
// use App\Http\Resources\GroupWirdConfigResource;
// use App\Http\Resources\WirdResource;
// use App\Models\GroupWirdConfig;
// use App\Models\Wird;
// use App\Traits\APIResponse;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\DB;

// class WirdDoneController extends Controller
// {
//     use APIResponse;

//     public function wirdDone(Request $request)
//     {
//         $this->validate($request, [
//             'wird_id' => 'required|exists:wirds,id',
//             'score' => 'nullable|integer|min:1',
//             'is_completed' => 'required|boolean',
//         ]);

//         $score = $request->input('score') ?: null;
//         $isCompleted = $request->input('is_completed') ?? null;


//         $wirdId = $request->input('wird_id');
//         $wirdDone = Auth::user()->wirdDones()->where('wird_id', $wirdId)->first();
//         if (!$wirdDone) {
//             Auth::user()->wirdDones()->create([
//                 'wird_id' => $wirdId,
//                 'score' => $score,
//                 'is_completed' => $isCompleted,
//             ]);
//             return $this->successResponse([], 'لقد أتممت الورد');
//         } else {
//             $wirdDone->score = $isCompleted ? ($score ?: null) : null;
//             $wirdDone->is_completed = $isCompleted;
//             $wirdDone->save();
//             return $this->successResponse([], $isCompleted ? 'لقد أتممت الورد' : 'حاول أن لا تضيع أوراد اليوم');
//         }

//     }
// }
