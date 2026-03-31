<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Approval extends Model
{
    protected $fillable = ['step', 'decision', 'comment', 'decidedAt', 'purchase_request_id', 'user_id'];

    protected $casts = [
        'decision' => \App\Enums\ApprovalDecision::class,
        'decidedAt' => 'datetime',
    ];

    public function purchaseRequest()
    {
        return $this->belongsTo(PurchaseRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
