<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDepartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->role === 'directeur';
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:departments,name',
            'manager_id' => [
                'nullable',
                'exists:users,id',
                'unique:departments,manager_id'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'manager_id.unique' => 'Ce manager est déjà assigné à un autre département.',
        ];
    }
}
