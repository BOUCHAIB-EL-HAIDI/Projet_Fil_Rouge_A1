<?php

namespace App\Http\Controllers\Api;

use App\Enums\ApprovalDecision;
use App\Enums\PurchaseRequestStatus;
use App\Http\Controllers\Controller;
use App\Models\Approval;
use App\Models\PurchaseRequest;
use App\Models\RequestStatusHistory;
use App\Events\PurchaseRequestStatusUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApprovalController extends Controller
{
    public function store(Request $request, PurchaseRequest $purchaseRequest)
    {
        $request->validate([
            'decision' => 'required|string|in:approved,rejected',
            'comment' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($request, $purchaseRequest) {
            $user = $request->user();
            $role = $user->role;
            
            if ($purchaseRequest->status === PurchaseRequestStatus::PENDING_MANAGER) {
                if ($role !== 'manager' || $user->department_id !== $purchaseRequest->department_id) {
                    return response()->json(['message' => 'Only the department manager can approve this step.'], 403);
                }
            } elseif ($purchaseRequest->status === PurchaseRequestStatus::PENDING_DIRECTEUR) {
                if ($role !== 'directeur') {
                    return response()->json(['message' => 'Only the directeur can approve this step.'], 403);
                }
            } else {
                return response()->json(['message' => 'This request does not require approval at this stage.'], 422);
            }

            $oldStatus = $purchaseRequest->status;
            $newStatus = $oldStatus;

            if ($request->decision === 'rejected') {
                $newStatus = PurchaseRequestStatus::REJECTED;
            } else {
                if ($oldStatus === PurchaseRequestStatus::PENDING_MANAGER) {
                    $newStatus = PurchaseRequestStatus::PENDING_DIRECTEUR;
                } else {
                    $newStatus = PurchaseRequestStatus::APPROVED;
                }
            }

            $approval = $purchaseRequest->approvals()->create([
                'step' => $oldStatus === PurchaseRequestStatus::PENDING_MANAGER ? 1 : 2,
                'decision' => $request->decision,
                'comment' => $request->comment,
                'decidedAt' => now(),
                'user_id' => $user->id,
            ]);

            $purchaseRequest->update(['status' => $newStatus]);

            RequestStatusHistory::create([
                'purchase_request_id' => $purchaseRequest->id,
                'user_id' => $user->id,
                'oldStatus' => $oldStatus,
                'newStatus' => $newStatus,
                'changedAt' => now(),
            ]);

            $decisionText = $request->decision === 'approved' ? 'approuvée' : 'refusée';
            $purchaseRequest->notifyInvolvedUsers(
                "Décision d'approbation",
                "La demande '{$purchaseRequest->title}' a été {$decisionText} par " . $user->name . ".",
                $user->id
            );

            broadcast(new PurchaseRequestStatusUpdated($purchaseRequest));

            return $approval;
        });
    }
}
