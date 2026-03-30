<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\PurchaseRequest;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index(PurchaseRequest $purchaseRequest)
    {
        $this->authorizeAccess($purchaseRequest);
        return $purchaseRequest->messages()->with('user')->get();
    }

    public function store(Request $request, PurchaseRequest $purchaseRequest)
    {
        $this->authorizeAccess($purchaseRequest);

        $request->validate([
            'content' => 'required|string',
        ]);

        $message = $purchaseRequest->messages()->create([
            'content' => $request->content,
            'user_id' => $request->user()->id,
        ]);

        $message->load('user');

        broadcast(new \App\Events\MessageSent($message))->toOthers();

        return $message;
    }

    private function authorizeAccess(PurchaseRequest $request)
    {
        $user = auth()->user();
        $isOwner = $request->user_id === $user->id;
        $isManager = $user->role === 'manager' && $user->department_id === $request->department_id;
        $isDirecteur = $user->role === 'directeur';
        $isAcheteur = $user->role === 'acheteur';

        if (!$isOwner && !$isManager && !$isDirecteur && !$isAcheteur) {
            abort(403, 'Unauthorized access to this chat stream.');
        }
    }
}
