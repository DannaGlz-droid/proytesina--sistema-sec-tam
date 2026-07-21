<?php

namespace App\Http\Requests;

use App\Models\District;
use App\Models\Position;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => $this->normalizePersonName($this->input('name')),
            'first_last_name' => $this->normalizePersonName($this->input('first_last_name')),
            'second_last_name' => $this->normalizePersonName($this->input('second_last_name')),
            'email' => $this->input('email') !== null ? mb_strtolower(trim((string) $this->input('email')), 'UTF-8') : null,
            'username' => $this->input('username') !== null ? mb_strtolower(trim((string) $this->input('username')), 'UTF-8') : null,
            'phone' => $this->input('phone') !== null ? trim((string) $this->input('phone')) : null,
        ]);
    }

    private function normalizePersonName($value): ?string
    {
        if ($value === null) {
            return null;
        }

        $normalized = preg_replace('/\s+/u', ' ', trim((string) $value));

        if ($normalized === '') {
            return null;
        }

        $normalized = mb_convert_case(mb_strtolower($normalized, 'UTF-8'), MB_CASE_TITLE, 'UTF-8');
        $lowercaseParticles = ['De', 'Del', 'La', 'Las', 'Los', 'Y'];

        $parts = explode(' ', $normalized);
        foreach ($parts as $index => $part) {
            if ($index > 0 && in_array($part, $lowercaseParticles, true)) {
                $parts[$index] = mb_strtolower($part, 'UTF-8');
            }
        }

        return implode(' ', $parts);
    }

    public function rules(): array
    {
        $user = $this->route('user');
        $userId = $user ? $user->id : null;

        $passwordRule = $userId
            ? ['nullable', 'confirmed', Password::defaults()]
            : ['required', 'confirmed', Password::defaults()];

        return [
            'name' => 'required|string|min:2|max:191',
            'first_last_name' => 'required|string|min:2|max:191',
            'second_last_name' => 'nullable|string|min:2|max:191',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'phone' => 'nullable|string|regex:/^[0-9]{10}$/',
            'username' => ['required', 'string', 'min:3', 'max:50', 'regex:/^[a-zA-Z0-9_.-]+$/', Rule::unique('users', 'username')->ignore($userId)],
            'password' => $passwordRule,
            'is_active' => $userId ? 'required|boolean' : 'nullable|boolean',
            'position_id' => ['required', function ($attribute, $value, $fail) {
                $position = Position::find($value);
                $blocked = ['administrador', 'admin', 'no definido'];

                if (!$position || in_array(mb_strtolower(trim((string) $position->name), 'UTF-8'), $blocked, true)) {
                    $fail('El cargo seleccionado no es válido.');
                }
            }],
            'district_id' => ['required', function ($attribute, $value, $fail) {
                $allowedIds = District::userAssignmentCatalog()->pluck('id')->map(fn ($id) => (int) $id)->all();

                if (!in_array((int) $value, $allowedIds, true)) {
                    $fail('El distrito seleccionado no es válido.');
                }
            }],
            'role_id' => 'required|exists:roles,id',
        ];
    }

    public function messages(): array
    {
        return [
            'email.email' => 'Capture un correo electrónico válido.',
            'phone.regex' => 'El teléfono debe tener exactamente 10 dígitos, sin espacios, guiones ni lada.',
        ];
    }
}
