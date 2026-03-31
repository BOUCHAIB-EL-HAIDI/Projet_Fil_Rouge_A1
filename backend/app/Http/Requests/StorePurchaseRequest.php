<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->role === 'demandeur';
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|string|in:low,medium,high,urgent',
            'items' => 'required|array|min:1',
            'items.*.productName' => 'required|string',
            'items.*.reference' => 'nullable|string',
            'items.*.quantity' => 'required|integer|min:1',
            'is_draft' => 'sometimes|boolean'
        ];
    }
}
