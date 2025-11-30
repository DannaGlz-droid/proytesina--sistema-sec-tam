<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InjuryObservatoryReportRequest extends FormRequest
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
        $publication = $this->route('publication');
        $isUpdate = $publication !== null;

        return [
            'tema' => 'required|string|min:3|max:255',
            'fecha' => 'required|date|before_or_equal:today',
            'municipio' => 'required|exists:municipalities,id',
            'jurisdiccion' => 'required|exists:jurisdictions,id',
            'descripcion' => 'nullable|string|max:5000',
            // En modo edición, el archivo es opcional
            'archivo' => $isUpdate ? 'nullable|file|mimes:xlsx,xls|max:10240' : 'required|file|mimes:xlsx,xls|max:10240',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'tema.required' => 'El tema es obligatorio.',
            'tema.min' => 'El tema debe tener al menos 3 caracteres.',
            'tema.max' => 'El tema no puede exceder 255 caracteres.',
            'fecha.required' => 'La fecha de la actividad es obligatoria.',
            'fecha.date' => 'La fecha debe ser una fecha válida.',
            'fecha.before_or_equal' => 'La fecha no puede ser futura.',
            'municipio.required' => 'El municipio es obligatorio.',
            'municipio.exists' => 'El municipio seleccionado no es válido.',
            'jurisdiccion.required' => 'La jurisdicción es obligatoria.',
            'jurisdiccion.exists' => 'La jurisdicción seleccionada no es válida.',
            'descripcion.max' => 'La descripción no puede exceder 5000 caracteres.',
            'archivo.required' => 'Debe subir un archivo Excel.',
            'archivo.file' => 'Debe subir un archivo válido.',
            'archivo.mimes' => 'El archivo debe ser Excel (XLSX, XLS).',
            'archivo.max' => 'El archivo no debe exceder 10 MB.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'tema' => 'tema',
            'fecha' => 'fecha de la actividad',
            'municipio' => 'municipio',
            'jurisdiccion' => 'jurisdicción',
            'descripcion' => 'descripción',
            'archivo' => 'archivo',
        ];
    }
}
