<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDevisRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->role === 'acheteur';
    }

    public function rules(): array
    {
        return [
            'supplier_id' => 'required|exists:suppliers,id',
            'deliveryTime' => 'nullable|string',
            'paymentMethod' => 'nullable|string',
            'items_prices' => 'required|array',
            'items_prices.*.item_id' => 'required|exists:request_items,id',
            'items_prices.*.price' => 'required|numeric|min:0',
            'items_prices.*.quantity' => 'required|integer|min:1',
        ];
    }
}
