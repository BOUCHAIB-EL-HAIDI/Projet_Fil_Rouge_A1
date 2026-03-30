<?php

namespace App\Http\Controllers\Api;

use App\Enums\PurchaseRequestStatus;
use App\Http\Controllers\Controller;
use App\Models\PurchaseRequest;
use App\Models\RequestStatusHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Http\Requests\StorePurchaseRequest;

class PurchaseRequestController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = PurchaseRequest::with(['user', 'department', 'items']);

        if ($user->role === 'directeur') {
        } elseif ($user->role === 'manager') {
            $query->where(function($q) use ($user) {
                $q->where('department_id', $user->department_id)
                  ->orWhere('user_id', $user->id);
            });
        } elseif ($user->role === 'acheteur') {
            $query->where(function($q) use ($user) {
                $q->whereIn('status', [
                    PurchaseRequestStatus::APPROVED,
                    PurchaseRequestStatus::IN_PROGRESS,
                    PurchaseRequestStatus::ORDERED,
                    PurchaseRequestStatus::DELIVERED
                ])->orWhere('user_id', $user->id);
            });
        } else {
            $query->where('user_id', $user->id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        return response()->json($query->latest()->paginate(10));
    }

    public function store(StorePurchaseRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $user = $request->user();
            $status = $request->is_draft ? PurchaseRequestStatus::DRAFT : PurchaseRequestStatus::PENDING_MANAGER;

            $purchaseRequest = PurchaseRequest::create([
                'title' => $request->title,
                'description' => $request->description,
                'priority' => $request->priority,
                'status' => $status,
                'user_id' => $user->id,
                'department_id' => $user->department_id,
            ]);

            foreach ($request->items as $item) {
                $purchaseRequest->items()->create($item);
            }

            RequestStatusHistory::create([
                'purchase_request_id' => $purchaseRequest->id,
                'user_id' => $user->id,
                'newStatus' => $purchaseRequest->status,
                'changedAt' => now(),
            ]);

            if ($status === PurchaseRequestStatus::PENDING_MANAGER) {
                $purchaseRequest->notifyInvolvedUsers(
                    "Nouvelle Demande",
                    "Une nouvelle demande '{$purchaseRequest->title}' a été créée.",
                    $user->id
                );
            }

            return response()->json($purchaseRequest->load('items'), 201);
        });
    }


    public function show(PurchaseRequest $purchaseRequest)
    {
        return $purchaseRequest->load([
            'user',
            'department',
            'items',
            'approvals' => fn ($q) => $q->orderBy('step'),
            'approvals.user',
            'messages.user',
            'attachments',
            'devis.supplier',
            'statusHistories.user',
        ]);
    }

    public function update(Request $request, PurchaseRequest $purchaseRequest)
    {
        $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'priority' => 'sometimes|string',
            'status' => 'sometimes|string',
        ]);

        $user = $request->user();
        $oldStatus = $purchaseRequest->status;
        $requestedStatus = $request->status;

        if ($requestedStatus && $oldStatus->value !== $requestedStatus) {
            if ($user->role === 'demandeur') {
                if ($oldStatus !== PurchaseRequestStatus::DRAFT || $requestedStatus !== PurchaseRequestStatus::PENDING_MANAGER->value) {
                    return response()->json(['message' => 'Action non autorisée pour votre rôle.'], 403);
                }
            } elseif ($user->role === 'acheteur') {
                if (!in_array($requestedStatus, [
                    PurchaseRequestStatus::IN_PROGRESS->value,
                    PurchaseRequestStatus::ORDERED->value,
                    PurchaseRequestStatus::CONSULTATION->value,
                    PurchaseRequestStatus::DELIVERED->value
                ])) {
                    return response()->json(['message' => 'Configuration de statut invalide pour l\'acheteur.'], 403);
                }
            } elseif ($user->role !== 'directeur') {
                 if ($purchaseRequest->user_id !== $user->id) {
                     return response()->json(['message' => 'Veuillez utiliser le module de validation.'], 403);
                 }
            }
        }

        $purchaseRequest->update($request->only(['title', 'description', 'priority', 'status']));

        if ($requestedStatus && $oldStatus->value !== $requestedStatus) {
            RequestStatusHistory::create([
                'purchase_request_id' => $purchaseRequest->id,
                'user_id' => $user->id,
                'oldStatus' => $oldStatus,
                'newStatus' => $purchaseRequest->status,
                'changedAt' => now(),
            ]);

            $purchaseRequest->notifyInvolvedUsers(
                "Mise à jour d'état",
                "La demande '{$purchaseRequest->title}' est passée à l'état : " . $requestedStatus,
                $user->id
            );

            event(new \App\Events\PurchaseRequestStatusUpdated($purchaseRequest));
        }

        return response()->json($purchaseRequest);
    }


    public function destroy(PurchaseRequest $purchaseRequest)
    {
        $purchaseRequest->delete();
        return response()->json(null, 204);
    }
}
