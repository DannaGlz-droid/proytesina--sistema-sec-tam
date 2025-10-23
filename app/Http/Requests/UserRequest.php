<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Allow all requests for now; use policies/gates if you need stricter control.
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = $this->route('user');
        $userId = $user ? $user->id : null;

        $passwordRule = $userId ? ['nullable', 'string', 'confirmed', 'min:6'] : ['required', 'string', 'confirmed', 'min:6'];

        return [
            'name' => 'required|string|max:255',
            'first_last_name' => 'nullable|string|max:255',
            'second_last_name' => 'nullable|string|max:255',
            'email' => ['required','email','max:255', Rule::unique('users','email')->ignore($userId)],
            'phone' => 'nullable|string|max:50',
            'username' => ['required','string','max:50', Rule::unique('users','username')->ignore($userId)],
            'password' => $passwordRule,
            'is_active' => 'nullable|boolean',
            'registration_date' => 'nullable|date',
            'last_session' => 'nullable|date',
            'position_id' => 'nullable|exists:positions,id',
            'jurisdiction_id' => 'nullable|exists:jurisdictions,id',
            'role_id' => 'nullable|exists:roles,id',
        ];
    }
}
