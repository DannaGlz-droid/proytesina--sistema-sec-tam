<?php

namespace App\Http\Requests;

use App\Config\ReportFileRequirements;
use App\Http\Requests\Concerns\ValidatesReportFileDuplicates;
use App\Models\District;
use App\Models\Municipality;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InjuryObservatoryReportRequest extends FormRequest
{
    use ValidatesReportFileDuplicates;

    protected function prepareForValidation(): void
    {
        $userDistrictId = (int) $this->user()?->district_id;

        if ($userDistrictId && $userDistrictId !== District::CENTRAL_OFFICE_ID) {
            $this->merge(['jurisdiccion' => $userDistrictId]);
        }
    }

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
        $userDistrictId = (int) $this->user()?->district_id;
        $canSelectAnyDistrict = $userDistrictId === District::CENTRAL_OFFICE_ID;
        $allowedDistrictIds = $canSelectAnyDistrict
            ? District::numberedCatalog()->pluck('id')->map(fn ($id) => (int) $id)->all()
            : [$userDistrictId];
        $municipalityRule = Rule::exists('municipalities', 'id');

        if (! $canSelectAnyDistrict) {
            $municipalityRule->where(fn ($query) => $query->where('district_id', $userDistrictId));
        }

        return [
            'tema' => 'required|string|min:3|max:150',
            'fecha' => 'required|date|before_or_equal:today',
            'municipio' => ['required', 'integer', $municipalityRule],
            'jurisdiccion' => ['required', 'integer', Rule::in($allowedDistrictIds)],
            'descripcion' => 'nullable|string|max:5000',
            // En modo edición, los archivos son opcionales; en creación, se exige al menos uno.
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
                $this->addDuplicateFileErrors($validator);

                $publication = $this->route('publication');
                $municipality = Municipality::find($this->input('municipio'));

                if ($municipality && (int) $municipality->district_id !== (int) $this->input('jurisdiccion')) {
                    $validator->errors()->add('municipio', 'El municipio no pertenece al distrito seleccionado.');
                }

                $filesToDeleteIds = $this->filled('files_to_delete')
                    ? array_filter(explode(',', (string) $this->input('files_to_delete')))
                    : [];
                $remainingFiles = $publication
                    ? $publication->files->whereNotIn('id', $filesToDeleteIds)->all()
                    : [];
                $newFiles = $this->file('archivos', []);

                if (! is_array($newFiles)) {
                    $newFiles = [];
                }

                $requirements = ReportFileRequirements::getRequirements('observatorio');
                $allRemainingFiles = collect($remainingFiles)->merge($newFiles);
                $excelCount = $allRemainingFiles
                    ->filter(fn ($file) => ReportFileRequirements::getFileType(
                        method_exists($file, 'getClientOriginalName')
                            ? $file->getClientOriginalName()
                            : $file->original_name
                    ) === 'excel')
                    ->count();

                if ($excelCount < $requirements['required_files']['excel']['min']) {
                    $validator->errors()->add(
                        'archivos',
                        ReportFileRequirements::getMissingFilesMessage('observatorio', $allRemainingFiles)
                    );
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
            'tema.max' => 'El tema no puede exceder 150 caracteres.',
            'fecha.required' => 'La fecha de la actividad es obligatoria.',
            'fecha.date' => 'La fecha debe ser una fecha válida.',
            'fecha.before_or_equal' => 'La fecha no puede ser futura.',
            'municipio.required' => 'El municipio es obligatorio.',
            'municipio.exists' => 'El municipio seleccionado no es válido.',
            'jurisdiccion.required' => 'El distrito es obligatorio.',
            'jurisdiccion.in' => 'El distrito seleccionado no está permitido para este usuario.',
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
            'jurisdiccion' => 'distrito',
            'descripcion' => 'descripción',
            'archivos' => 'archivos',
            'archivos.*' => 'archivo',
        ];
    }
}
