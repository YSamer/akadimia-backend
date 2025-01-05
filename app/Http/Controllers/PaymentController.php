<?php

namespace App\Http\Controllers;

use App\Http\Resources\BatchApplyResource;
use App\Http\Resources\PaymentResource;
use App\Models\BatchApply;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\APIResponse;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    use APIResponse;

    /**
     * Get all payments with pagination.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllPayments(Request $request)
    {
        $perPage = $request->input('per_page', 25);

        $payments = Payment::paginate($perPage);

        return $this->successResponse(PaymentResource::collection($payments), 'Payments fetched successfully.');
    }

    /**
     * Create a new payment.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createPayment(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'image' => 'required|file|mimes:jpg,jpeg,png,pdf|max:4096',
        ]);

        $filePath = $request->file('image')->store('payments', 'public');

        $user_id = Auth::id();
        $payment = Payment::create([
            'user_id' => $user_id,
            'amount' => $request->amount,
            'image' => $filePath,
            'is_confirm' => false,
        ]);

        return $this->successResponse(new PaymentResource($payment), 'Payment created successfully.');
    }

    public function achievePayment(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'image' => 'required|file|mimes:jpg,jpeg,png,pdf|max:4096',
            'batch_id' => 'required|integer',
            'achievement_id' => 'required|integer',
        ]);

        try {
            DB::beginTransaction();
            $user_id = Auth::id();

            $filePath = $request->file('image')->store('payments', 'public');

            $apply = BatchApply::where([
                'batch_id' => $request->batch_id,
                'user_id' => $user_id,
            ])->first();

            if (!$apply) {
                return $this->errorResponse('لا يوجد تقدم لهذه المرحلة', 404);
            }

            if (!$apply->payment_id) {
                $payment = Payment::create([
                    'user_id' => $user_id,
                    'amount' => $request->amount,
                    'image' => $filePath,
                    'is_confirm' => false,
                ]);
                $apply->payment_id = $payment->id;
                $apply->save();
            }

            if (!in_array($request->achievement_id, $apply->achievement_ids ?? [])) {
                $apply->achievement_ids = array_merge($apply->achievement_ids ?? [], [$request->achievement_id]);
                $apply->save();

                DB::commit();
                return $this->successResponse(new BatchApplyResource($apply), 'تم التحديث بنجاح');
            }

            DB::commit();
            return $this->successResponse(new BatchApplyResource($apply), 'الإنجاز موجود بالفعل');
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->errorResponse('Payment Confirmation failed. Please try again.', 500);
        }
    }

    /**
     * Confirm a payment.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function confirmPayment(Request $request, $id)
    {
        $admin = Auth::guard('admin')->user();

        if (!$admin) {
            return $this->errorResponse('Unauthorized.', null, 403);
        }

        $payment = Payment::find($id);

        if (!$payment) {
            return $this->errorResponse('Payment not found.', null, 404);
        }

        $payment->update([
            'is_confirm' => true,
            'confirmed_by' => $admin->id,
        ]);

        return $this->successResponse(new PaymentResource($payment), 'Payment confirmed successfully.');
    }
}