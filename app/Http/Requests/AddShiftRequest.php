<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AddShiftRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string',
            'description' => 'string|nullable',
            'max_crew' => 'required|int|min:0',
            'shift_role' => 'required|int',
            'start_datetime' => 'required|date_format:Y-m-d\TH:i',
            'end_datetime' => 'required|date_format:Y-m-d\TH:i',
            'employees' => 'nullable',
            'employees.*.id' => 'required',
        ];
    }
}
