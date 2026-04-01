<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Devis extends Model
{
    protected $fillable = ['price', 'deliveryTime', 'paymentMethod', 'status', 'supplier_id', 'purchase_request_id', 'items_prices'];

    protected $casts = [
        'status' => \App\Enums\DevisStatus::class,
        'items_prices' => 'array',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchaseRequest()
    {
        return $this->belongsTo(PurchaseRequest::class);
    }
}
