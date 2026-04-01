<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestStatusHistory extends Model
{
    protected $fillable = ['oldStatus', 'newStatus', 'changedAt', 'purchase_request_id', 'user_id'];

    protected $casts = [
        'oldStatus' => \App\Enums\PurchaseRequestStatus::class,
        'newStatus' => \App\Enums\PurchaseRequestStatus::class,
        'changedAt' => 'datetime',
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
