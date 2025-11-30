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
            'name' => 'required|string|min:2|max:191',
            'first_last_name' => 'required|string|min:2|max:191',
            'second_last_name' => 'nullable|string|min:2|max:191',
            'email' => ['required','email','max:255', Rule::unique('users','email')->ignore($userId)],
            'phone' => 'nullable|string|regex:/^[0-9+\-\(\)\s]{8,20}$/',
            'username' => ['required','string','min:3','max:50','regex:/^[a-zA-Z0-9_.-]+$/', Rule::unique('users','username')->ignore($userId)],
            'password' => $passwordRule,
            'is_active' => 'nullable|boolean',
            'position_id' => ['required', function ($attribute, $value, $fail) {
                if ($value != 0 && !\App\Models\Position::where('id', $value)->exists()) {
                    $fail('El cargo seleccionado no es válido.');
                }
            }],
            'jurisdiction_id' => 'required|exists:jurisdictions,id',
            'role_id' => 'required|exists:roles,id',
        ];
    }
}
