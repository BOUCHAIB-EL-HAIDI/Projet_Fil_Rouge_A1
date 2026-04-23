<?php

namespace App\Http\Controllers\Api;

use App\Enums\DevisStatus;
use App\Enums\PurchaseRequestStatus;
use App\Http\Controllers\Controller;
use App\Models\Devis;
use App\Models\PurchaseRequest;
use Illuminate\Http\Request;
use App\Http\Requests\StoreDevisRequest;

class DevisController extends Controller
{
    public function index(PurchaseRequest $purchaseRequest)
    {
        return response()->json($purchaseRequest->devis()->with('supplier')->get());
    }

    public function store(StoreDevisRequest $request, PurchaseRequest $purchaseRequest)
    {
        $data = $request->validated();
        
        $totalPrice = 0;
        foreach ($data['items_prices'] as $item) {
            $totalPrice += ($item['price'] * $item['quantity']);
        }

        $data['price'] = $totalPrice;
        $data['status'] = DevisStatus::RECEIVED;

        $devis = $purchaseRequest->devis()->create($data);

        // Update request status to consultation if it was in_progress or approved
        if (in_array($purchaseRequest->status, [PurchaseRequestStatus::APPROVED, PurchaseRequestStatus::IN_PROGRESS])) {
            $purchaseRequest->update(['status' => PurchaseRequestStatus::CONSULTATION]);
            event(new \App\Events\PurchaseRequestStatusUpdated($purchaseRequest));
        }

        // Notify involved users that a devis was added
        $purchaseRequest->notifyInvolvedUsers(
            "Nouveau Devis Ajouté",
            "L'acheteur a ajouté un nouveau devis du fournisseur {$devis->supplier->name}.",
            $request->user()->id
        );

        return response()->json($devis->load('supplier'), 201);
    }

    public function update(StoreDevisRequest $request, PurchaseRequest $purchaseRequest, Devis $devi)
    {
        $data = $request->validated();

        $totalPrice = 0;
        foreach ($data['items_prices'] as $item) {
            $totalPrice += ($item['price'] * $item['quantity']);
        }
        $data['price'] = $totalPrice;

        $devi->update($data);
        return response()->json($devi->load('supplier'));
    }

    public function select(Request $request, PurchaseRequest $purchaseRequest, Devis $devi)
    {
        $user = $request->user();
        
        // Permission check: Strictly restricted to the Directeur only
        if ($user->role !== 'directeur') {
            return response()->json(['message' => 'Seul le Directeur peut sélectionner un devis pour validation finale.'], 403);
        }

        return \DB::transaction(function () use ($purchaseRequest, $devi, $user) {
            // Unselect others (explicitly use value property for query builder!)
            $purchaseRequest->devis()->where('id', '!=', $devi->id)
                ->update(['status' => DevisStatus::REJECTED->value]);

            // Select this one
            $devi->update(['status' => DevisStatus::SELECTED]);

            // Update request status to ordered
            $purchaseRequest->update(['status' => PurchaseRequestStatus::ORDERED]);

            // Notify everyone of the decision
            $purchaseRequest->notifyInvolvedUsers(
                "Devis Accépté",
                "Le directeur {$user->name} a confirmé le devis et validé la commande.",
                $user->id
            );

            // Broadcast status update event
            event(new \App\Events\PurchaseRequestStatusUpdated($purchaseRequest));

            return response()->json($devi->load('supplier'));
        });
    }

    public function destroy(Request $request, PurchaseRequest $purchaseRequest, Devis $devi)
    {
        if ($request->user()->role !== 'acheteur') {
            return response()->json(['message' => 'Seul l\'acheteur peut supprimer des devis.'], 403);
        }

        $devi->delete();
        return response()->json(null, 204);
    }
}
