<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Config\ReportFileRequirements;
use Closure;

class RoadSafetyReportRequest extends FormRequest
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
            'lugar' => 'required|string|min:3|max:255',
            'activity_type_id' => 'required|exists:activity_types,id',
            'participantes' => 'required|integer|min:1|max:9999',
            'promotor' => 'required|string|min:3|max:255',
            'municipio' => 'nullable|exists:municipalities,id',
            'jurisdiccion' => 'nullable|exists:jurisdictions,id',
            'descripcion' => 'nullable|string|max:5000',
            // En modo edición, los archivos son opcionales
            'archivos' => $isUpdate ? 'nullable|array' : 'required|array|min:1',
            'archivos.*' => 'file|mimes:pdf,xlsx,xls,jpg,jpeg,png|max:10240', // 10MB por archivo
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
                    
                    // Contar existentes por tipo
                    $pdfCount = count(array_filter($remainingFiles, fn($f) => 
                        strtolower(pathinfo($f->original_name, PATHINFO_EXTENSION)) === 'pdf'
                    ));
                    $excelCount = count(array_filter($remainingFiles, fn($f) => 
                        in_array(strtolower(pathinfo($f->original_name, PATHINFO_EXTENSION)), ['xlsx', 'xls'])
                    ));
                    $photosCount = count(array_filter($remainingFiles, fn($f) => 
                        in_array(strtolower(pathinfo($f->original_name, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png'])
                    ));
                    
                    // Contar nuevos por tipo
                    foreach ($newFiles as $file) {
                        $ext = strtolower(pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION));
                        if ($ext === 'pdf') $pdfCount++;
                        elseif (in_array($ext, ['xlsx', 'xls'])) $excelCount++;
                        elseif (in_array($ext, ['jpg', 'jpeg', 'png'])) $photosCount++;
                    }
                    
                    // Validar contra requisitos
                    $requirements = ReportFileRequirements::getRequirements('seguridad-vial');
                    $missingMessage = ReportFileRequirements::getMissingFilesMessage('seguridad-vial', 
                        collect($remainingFiles)->merge($newFiles));
                    
                    // Verificar que se cumplan los requisitos mínimos
                    if ($pdfCount < $requirements['required_files']['pdf']['min'] ||
                        $excelCount < $requirements['required_files']['excel']['min'] ||
                        $photosCount < $requirements['required_files']['photos']['min']) {
                        
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
            'lugar.required' => 'El lugar es obligatorio.',
            'lugar.min' => 'El lugar debe tener al menos 3 caracteres.',
            'lugar.max' => 'El lugar no puede exceder 255 caracteres.',
            'activity_type_id.required' => 'El tipo de actividad es obligatorio.',
            'activity_type_id.exists' => 'El tipo de actividad seleccionado no es válido.',
            'participantes.required' => 'El número de participantes es obligatorio.',
            'participantes.integer' => 'El número de participantes debe ser un número entero.',
            'participantes.min' => 'Debe haber al menos 1 participante.',
            'participantes.max' => 'El número de participantes no puede exceder 9999.',
            'promotor.required' => 'El promotor es obligatorio.',
            'promotor.min' => 'El promotor debe tener al menos 3 caracteres.',
            'promotor.max' => 'El promotor no puede exceder 255 caracteres.',
            'municipio.exists' => 'El municipio seleccionado no es válido.',
            'jurisdiccion.exists' => 'La jurisdicción seleccionada no es válida.',
            'descripcion.max' => 'La descripción no puede exceder 5000 caracteres.',
            'archivos.required' => 'Debe subir al menos un archivo.',
            'archivos.array' => 'Los archivos deben ser un conjunto válido.',
            'archivos.min' => 'Debe subir al menos un archivo.',
            'archivos.*.file' => 'Cada elemento debe ser un archivo válido.',
            'archivos.*.mimes' => 'Los archivos deben ser PDF, Excel (XLSX, XLS) o imágenes (JPG, JPEG, PNG).',
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
            'lugar' => 'lugar',
            'activity_type_id' => 'tipo de actividad',
            'participantes' => 'participantes',
            'promotor' => 'promotor',
            'descripcion' => 'descripción',
            'archivos' => 'archivos',
        ];
    }
}
