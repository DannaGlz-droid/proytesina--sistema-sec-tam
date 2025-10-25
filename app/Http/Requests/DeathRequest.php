<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DeathRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        $death = $this->route('death');
        $deathId = $death ? $death->id : null;

        return [
            //
            'name' => 'required|string|max:255',
            'first_last_name' => 'required|string|max:255',
            'second_last_name' => 'required|string|max:255',
            'age' => 'required|integer|min:0',
            'sex' => 'required|string|in:male,female,other',
            'death_date' => 'required|date',
            'residence_municipality_id' => 'required|exists:municipalities,id',
            'jurisdiction_id' => 'required|exists:jurisdictions,id',
            'death_municipality_id' => 'required|exists:municipalities,id',
            'death_location_id' => 'required|exists:death_locations,id',
            'death_cause_id' => 'required|exists:death_causes,id',
        ];
    }
}
