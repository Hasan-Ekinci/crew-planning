<?php

namespace App\Http\Requests;

use App\Models\Role;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class SaveEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id' => ['nullable', 'integer', 'exists:users,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->integer('id')),
            ],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['required', 'integer', 'distinct', 'exists:roles,id'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->any() || count($this->input('roles', [])) < 2) {
                    return;
                }

                $hasAdminRole = Role::query()
                    ->whereIn('id', $this->input('roles'))
                    ->pluck('name')
                    ->contains(fn (string $name) => strtolower($name) === 'admin');

                if ($hasAdminRole) {
                    $validator->errors()->add('roles', 'The Admin role cannot be combined with other roles.');
                }
            },
        ];
    }
}
