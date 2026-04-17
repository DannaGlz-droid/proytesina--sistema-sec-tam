<?php

namespace App\Config;

/**
 * Define the mandatory file requirements for each report type
 * Used to validate that required files are not deleted during updates
 */
class ReportFileRequirements
{
    /**
     * Get file requirements for a report type
     * 
     * @param string $reportType
     * @return array
     */
    public static function getRequirements($reportType): array
    {
        $requirements = [
            'observatorio' => [
                'description' => 'Observatorio de Lesiones',
                'required_files' => [
                    'excel' => ['min' => 1, 'extensions' => ['xlsx', 'xls']],
                ],
            ],
            'seguridad-vial' => [
                'description' => 'Seguridad Vial',
                'required_files' => [
                    'pdf' => ['min' => 1, 'extensions' => ['pdf']],
                    'excel' => ['min' => 1, 'extensions' => ['xlsx', 'xls']],
                    'photos' => ['min' => 4, 'extensions' => ['jpg', 'jpeg', 'png']],
                ],
            ],
            'grupos-vulnerables' => [
                'description' => 'Grupos Vulnerables',
                'required_files' => [
                    'pdf' => ['min' => 1, 'extensions' => ['pdf']],
                    'excel' => ['min' => 1, 'extensions' => ['xlsx', 'xls']],
                    'photos' => ['min' => 4, 'extensions' => ['jpg', 'jpeg', 'png']],
                ],
            ],
            'alcoholimetria' => [
                'description' => 'Alcoholimetría',
                'required_files' => [
                    'excel' => ['min' => 1, 'extensions' => ['xlsx', 'xls']],
                    'photos' => ['min' => 1, 'extensions' => ['jpg', 'jpeg', 'png']],
                ],
            ],
        ];

        return $requirements[$reportType] ?? [];
    }

    /**
     * Get all available requirements
     */
    public static function all(): array
    {
        return [
            'observatorio' => self::getRequirements('observatorio'),
            'seguridad-vial' => self::getRequirements('seguridad-vial'),
            'grupos-vulnerables' => self::getRequirements('grupos-vulnerables'),
            'alcoholimetria' => self::getRequirements('alcoholimetria'),
        ];
    }

    /**
     * Get file type from extension
     */
    public static function getFileType($filename): ?string
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (in_array($extension, ['jpg', 'jpeg', 'png'])) {
            return 'photos';
        } elseif (in_array($extension, ['xlsx', 'xls'])) {
            return 'excel';
        } elseif ($extension === 'pdf') {
            return 'pdf';
        }

        return null;
    }

    /**
     * Get filename from different file object types
     */
    private static function getFileName($file): string
    {
        // If it's an UploadedFile (has getClientOriginalName method)
        if (method_exists($file, 'getClientOriginalName')) {
            return $file->getClientOriginalName();
        }
        
        // If it's a PublicationFile model (has original_name attribute)
        if (isset($file->original_name)) {
            return $file->original_name;
        }
        
        // Fallback (shouldn't happen in normal flow)
        return '';
    }

    /**
     * Check if a report has all required files
     *
     * @param string $reportType
     * @param array $files - Array of file objects with 'original_name' property
     * @return array - ['valid' => bool, 'missing' => array of missing file types]
     */
    public static function validateFiles($reportType, $files): array
    {
        $requirements = self::getRequirements($reportType);
        $missing = [];

        if (empty($requirements)) {
            return ['valid' => true, 'missing' => []];
        }

        foreach ($requirements['required_files'] as $fileType => $requirement) {
            // Count files of this type
            $count = 0;
            foreach ($files as $file) {
                $filename = self::getFileName($file);
                if (self::getFileType($filename) === $fileType) {
                    $count++;
                }
            }

            if ($count < $requirement['min']) {
                $missing[] = [
                    'type' => $fileType,
                    'required' => $requirement['min'],
                    'current' => $count,
                ];
            }
        }

        return [
            'valid' => empty($missing),
            'missing' => $missing,
        ];
    }

    /**
     * Get human-readable message for missing files
     */
    public static function getMissingFilesMessage($reportType, $files): ?string
    {
        $validation = self::validateFiles($reportType, $files);

        if ($validation['valid']) {
            return null;
        }

        $messages = [];
        foreach ($validation['missing'] as $missing) {
            $typeNames = [
                'pdf' => 'documento PDF',
                'excel' => 'archivo Excel',
                'photos' => 'fotografías',
            ];

            $typeName = $typeNames[$missing['type']] ?? $missing['type'];
            $messages[] = "{$typeName} ({$missing['current']}/{$missing['required']})";
        }

        return 'El reporte requiere: ' . implode(', ', $messages);
    }
}
