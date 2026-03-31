<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDepartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->role === 'directeur';
    }

    public function rules(): array
    {
        $departmentId = $this->route('department')->id ?? $this->route('department');

        return [
            'name' => 'sometimes|string|max:255|unique:departments,name,' . $departmentId,
            'manager_id' => [
                'nullable',
                'exists:users,id',
                'unique:departments,manager_id,' . $departmentId
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
