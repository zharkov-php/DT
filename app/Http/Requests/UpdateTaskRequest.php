<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'nullable|string|max:190',
            'description' => 'nullable|string',
            'status' => 'nullable|in:todo,in_progress,done',
            'priority' => 'nullable|in:low,medium,high',
            'assigned_to' => 'nullable|exists:users,id',
        ];
    }
}
