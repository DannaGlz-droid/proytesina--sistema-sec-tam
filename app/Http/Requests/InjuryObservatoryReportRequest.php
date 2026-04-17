<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Config\ReportFileRequirements;
use Closure;

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
            // En modo edición, los archivos son opcionales; en creación, obligatorio al menos 1
            'archivos' => $isUpdate ? 'nullable|array' : 'required|array|min:1',
            'archivos.*' => 'file|mimes:xlsx,xls|max:10240',
            'files_to_delete' => 'nullable|string', // IDs de archivos a eliminar separados por comas
        ];
    }

    /**
     * Get the "after" validation callbacks.
     *
     * @return array<int, (Closure(static): void)>
     */
    public function after(): array
    {
        return [
            function ($validator) {
                // Solo validar en modo actualización
                if ($this->route('publication')) {
                    $publication = $this->route('publication');
                    
                    // Obtener archivos a eliminar
                    $filesToDeleteIds = [];
                    if ($this->filled('files_to_delete')) {
                        $filesToDeleteIds = array_filter(
                            explode(',', $this->input('files_to_delete')),
                            fn($id) => !empty($id)
                        );
                    }
                    
                    // Contar archivos existentes que NO van a ser eliminados
                    $remainingFiles = $publication->files
                        ->whereNotIn('id', $filesToDeleteIds)
                        ->all();
                    
                    // Contar archivos nuevos por tipo
                    $newFiles = $this->file('archivos', []);
                    if (!is_array($newFiles)) {
                        $newFiles = [];
                    }
                    
                    // Contar existentes Excel
                    $excelCount = count(array_filter($remainingFiles, fn($f) => 
                        in_array(strtolower(pathinfo($f->original_name, PATHINFO_EXTENSION)), ['xlsx', 'xls'])
                    ));
                    
                    // Contar nuevos Excel
                    foreach ($newFiles as $file) {
                        $ext = strtolower(pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION));
                        if (in_array($ext, ['xlsx', 'xls'])) $excelCount++;
                    }
                    
                    // Validar contra requisitos (observatorio solo requiere 1 XLSX)
                    $requirements = ReportFileRequirements::getRequirements('observatorio');
                    $missingMessage = ReportFileRequirements::getMissingFilesMessage('observatorio', 
                        collect($remainingFiles)->merge($newFiles));
                    
                    // Verificar que se cumplan los requisitos mínimos
                    if ($excelCount < $requirements['required_files']['excel']['min']) {
                        $validator->errors()->add('archivos', $missingMessage);
                    }
                }
            },
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
            'archivos.required' => 'Debe subir al menos un archivo Excel.',
            'archivos.array' => 'Los archivos deben ser un array válido.',
            'archivos.min' => 'Debe subir al menos 1 archivo Excel.',
            'archivos.*.file' => 'Debe subir un archivo válido.',
            'archivos.*.mimes' => 'Los archivos deben ser Excel (XLSX, XLS).',
            'archivos.*.max' => 'Cada archivo no debe exceder 10 MB.',
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
            'archivos' => 'archivos',
            'archivos.*' => 'archivo',
        ];
    }
}
