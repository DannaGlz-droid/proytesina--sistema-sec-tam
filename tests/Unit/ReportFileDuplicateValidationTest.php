<?php

use App\Http\Requests\Concerns\ValidatesReportFileDuplicates;
use Illuminate\Http\UploadedFile;

function duplicateFileErrors(array $files, ?object $publication = null, string $filesToDelete = ''): array
{
    $request = new class($files, $publication, $filesToDelete)
    {
        use ValidatesReportFileDuplicates;

        public function __construct(
            private array $files,
            private ?object $publication,
            private string $filesToDelete,
        ) {}

        public function file(string $key, mixed $default = null): mixed
        {
            return $key === 'archivos' ? $this->files : $default;
        }

        public function route(string $key): mixed
        {
            return $key === 'publication' ? $this->publication : null;
        }

        public function input(string $key, mixed $default = null): mixed
        {
            return $key === 'files_to_delete' ? $this->filesToDelete : $default;
        }

        public function collectDuplicateErrors(object $validator): void
        {
            $this->addDuplicateFileErrors($validator);
        }
    };

    $errors = new class
    {
        public array $messages = [];

        public function add(string $key, string $message): void
        {
            $this->messages[$key][] = $message;
        }
    };

    $validator = new class($errors)
    {
        public function __construct(private object $errors) {}

        public function errors(): object
        {
            return $this->errors;
        }
    };

    $request->collectDuplicateErrors($validator);

    return $errors->messages;
}

it('rejects a repeated file in the same selection', function (): void {
    $files = [
        UploadedFile::fake()->create('evidencia.xlsx', 10),
        UploadedFile::fake()->create('EVIDENCIA.xlsx', 10),
    ];

    $errors = duplicateFileErrors($files);

    expect($errors['archivos'][0])->toContain('repetido en la selección');
});

it('rejects a file already saved on the report', function (): void {
    $file = UploadedFile::fake()->create('evidencia.xlsx', 10);
    $publication = (object) [
        'files' => collect([
            (object) [
                'id' => 7,
                'original_name' => 'EVIDENCIA.xlsx',
                'file_size' => $file->getSize(),
            ],
        ]),
    ];

    $errors = duplicateFileErrors([$file], $publication);

    expect($errors['archivos'][0])->toContain('ya está guardado');
});

it('allows replacing a saved file marked for deletion', function (): void {
    $file = UploadedFile::fake()->create('evidencia.xlsx', 10);
    $publication = (object) [
        'files' => collect([
            (object) [
                'id' => 7,
                'original_name' => 'evidencia.xlsx',
                'file_size' => $file->getSize(),
            ],
        ]),
    ];

    expect(duplicateFileErrors([$file], $publication, '7'))->toBe([]);
});
