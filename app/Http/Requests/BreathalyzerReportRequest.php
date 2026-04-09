<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BreathalyzerReportRequest extends FormRequest
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
     */
    public function rules(): array
    {
        $publication = $this->route('publication');
        $isUpdate = $publication !== null;

        return [
            'tema' => 'required|string|min:3|max:255',
            'fecha' => 'required|date|before_or_equal:today',
            'puntos_revision' => 'required|integer|min:0|max:9999',
            'pruebas_realizadas' => 'required|integer|min:0|max:999999',
            'conductores_no_aptos' => 'required|integer|min:0|max:999999',
            'mujeres_no_aptas' => 'required|integer|min:0|max:999999',
            'hombres_no_aptos' => 'required|integer|min:0|max:999999',
            'automoviles_camionetas' => 'required|integer|min:0|max:999999',
            'motocicletas' => 'required|integer|min:0|max:999999',
            'transporte_colectivo' => 'required|integer|min:0|max:999999',
            'transporte_individual' => 'required|integer|min:0|max:999999',
            'transporte_carga' => 'required|integer|min:0|max:999999',
            'vehiculos_emergencia' => 'required|integer|min:0|max:999999',
            'descripcion' => 'nullable|string|max:5000',
            'archivos' => $isUpdate ? 'nullable|array' : 'required|array|min:1',
            'archivos.*' => 'file|mimes:xlsx,xls,jpg,jpeg,png|max:10240',
        ];
    }

    public function messages(): array
    {
        return [
            'tema.required' => 'El tema es obligatorio.',
            'tema.min' => 'El tema debe tener al menos 3 caracteres.',
            'tema.max' => 'El tema no puede exceder 255 caracteres.',
            'fecha.required' => 'La fecha de la actividad es obligatoria.',
            'fecha.date' => 'La fecha debe ser una fecha válida.',
            'fecha.before_or_equal' => 'La fecha no puede ser futura.',
            'puntos_revision.required' => 'Los puntos de revisión son obligatorios.',
            'puntos_revision.integer' => 'Los puntos de revisión deben ser un número entero.',
            'puntos_revision.min' => 'Los puntos de revisión no pueden ser negativos.',
            'pruebas_realizadas.required' => 'El número de pruebas realizadas es obligatorio.',
            'pruebas_realizadas.integer' => 'El número de pruebas realizadas debe ser un número entero.',
            'pruebas_realizadas.min' => 'El número de pruebas realizadas no puede ser negativo.',
            'conductores_no_aptos.required' => 'El número de conductores no aptos es obligatorio.',
            'conductores_no_aptos.integer' => 'El número de conductores no aptos debe ser un número entero.',
            'conductores_no_aptos.min' => 'El número de conductores no aptos no puede ser negativo.',
            'descripcion.max' => 'La descripción no puede exceder 5000 caracteres.',
            'archivos.required' => 'Debe subir al menos un archivo (Excel y fotografía).',
            'archivos.array' => 'Los archivos deben ser un conjunto válido.',
            'archivos.min' => 'Debe subir al menos un archivo.',
            'archivos.*.file' => 'Todos los elementos deben ser archivos válidos.',
            'archivos.*.mimes' => 'Los archivos deben ser Excel (XLSX, XLS) o imágenes (JPG, JPEG, PNG).',
            'archivos.*.max' => 'Cada archivo no debe exceder 10 MB.',
        ];
    }

    public function attributes(): array
    {
        return [
            'tema' => 'tema',
            'fecha' => 'fecha de la actividad',
            'puntos_revision' => 'puntos de revisión',
            'pruebas_realizadas' => 'pruebas realizadas',
            'conductores_no_aptos' => 'conductores no aptos',
            'descripcion' => 'descripción',
            'archivos' => 'archivos',
        ];
    }
}
