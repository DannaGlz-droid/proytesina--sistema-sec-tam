<?php

namespace App\Http\Requests\Concerns;

use Illuminate\Http\UploadedFile;

trait ValidatesReportFileDuplicates
{
    protected function addDuplicateFileErrors($validator): void
    {
        $newFiles = $this->file('archivos', []);

        if (! is_array($newFiles)) {
            return;
        }

        $seen = [];
        $reported = [];

        foreach ($newFiles as $file) {
            if (! $file instanceof UploadedFile || ! $file->isValid()) {
                continue;
            }

            $fingerprint = $this->reportFileFingerprint($file->getClientOriginalName(), $file->getSize());

            if (isset($seen[$fingerprint]) && ! isset($reported[$fingerprint])) {
                $validator->errors()->add(
                    'archivos',
                    'El archivo "'.$file->getClientOriginalName().'" está repetido en la selección.'
                );
                $reported[$fingerprint] = true;
            }

            $seen[$fingerprint] = true;
        }

        $publication = $this->route('publication');

        if (! $publication) {
            return;
        }

        $filesToDeleteIds = collect(explode(',', (string) $this->input('files_to_delete', '')))
            ->filter(fn ($id) => ctype_digit(trim((string) $id)))
            ->map(fn ($id) => (int) $id)
            ->all();

        $existingFingerprints = $publication->files
            ->whereNotIn('id', $filesToDeleteIds)
            ->mapWithKeys(fn ($file) => [
                $this->reportFileFingerprint($file->original_name, $file->file_size) => true,
            ]);

        foreach ($newFiles as $file) {
            if (! $file instanceof UploadedFile || ! $file->isValid()) {
                continue;
            }

            $fingerprint = $this->reportFileFingerprint($file->getClientOriginalName(), $file->getSize());

            if ($existingFingerprints->has($fingerprint) && ! isset($reported[$fingerprint])) {
                $validator->errors()->add(
                    'archivos',
                    'El archivo "'.$file->getClientOriginalName().'" ya está guardado. Márquelo para eliminar o reemplazar antes de volver a agregarlo.'
                );
                $reported[$fingerprint] = true;
            }
        }
    }

    private function reportFileFingerprint(string $name, int $size): string
    {
        return mb_strtolower(trim($name), 'UTF-8').'|'.$size;
    }
}
