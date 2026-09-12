<?php

namespace App\Support;

final class CatalogLabel
{
    private const ACRONYMS = [
        'DIF',
        'IMSS',
        'INSABI',
        'ISSSTE',
        'SEDENA',
    ];

    private const CAUSES = [
        'PEATON RESIDENCIA' => 'Peatón residencia',
        'VEHICULO DE MOTOR RESIDENCIA' => 'Vehículo de motor residencia',
        'EXPO FUEGO Y HUMO RESIDENCIA' => 'Exposición a fuego y humo residencia',
        'CAIDAS ACCIDENTALES' => 'Caídas accidentales',
        'AHOGAMIENTO RESIDENCIA' => 'Ahogamiento residencia',
        'ENVENENAMIENTO RES' => 'Envenenamiento residencia',
        'OTROS ACCIDENTES' => 'Otros accidentes',
    ];

    public static function municipality(?string $value): string
    {
        return self::properName($value);
    }

    public static function district(?string $value): string
    {
        $value = self::clean($value);

        if ($value === '') {
            return '';
        }

        $value = preg_replace('/^Distrito:\s*/iu', '', $value) ?? $value;

        if (preg_match('/^([IVXLCDM]+)\s*(?:-|·)\s*(.+)$/iu', $value, $matches)) {
            return mb_strtoupper($matches[1], 'UTF-8').' · '.self::properName($matches[2]);
        }

        return self::properName($value);
    }

    public static function cause(?string $value): string
    {
        $value = self::clean($value);

        if ($value === '') {
            return '';
        }

        $key = mb_strtoupper($value, 'UTF-8');

        return self::CAUSES[$key] ?? self::sentence($value);
    }

    public static function location(?string $value): string
    {
        return self::sentence($value);
    }

    public static function properName(?string $value): string
    {
        $value = self::clean($value);

        if ($value === '') {
            return '';
        }

        $formatted = mb_convert_case(mb_strtolower($value, 'UTF-8'), MB_CASE_TITLE, 'UTF-8');
        $formatted = self::lowercaseConnectors($formatted);

        return self::restoreAcronyms($formatted);
    }

    public static function sentence(?string $value): string
    {
        $value = self::clean($value);

        if ($value === '') {
            return '';
        }

        $formatted = mb_strtolower($value, 'UTF-8');
        $formatted = mb_strtoupper(mb_substr($formatted, 0, 1, 'UTF-8'), 'UTF-8')
            .mb_substr($formatted, 1, null, 'UTF-8');

        return self::restoreAcronyms($formatted);
    }

    private static function clean(?string $value): string
    {
        return trim(preg_replace('/\s+/u', ' ', (string) $value) ?? '');
    }

    private static function lowercaseConnectors(string $value): string
    {
        foreach (['A', 'Al', 'De', 'Del', 'E', 'El', 'En', 'La', 'Las', 'Los', 'Y'] as $word) {
            $value = preg_replace(
                '/(?<!^)\b'.preg_quote($word, '/').'\b/u',
                mb_strtolower($word, 'UTF-8'),
                $value
            ) ?? $value;
        }

        return $value;
    }

    private static function restoreAcronyms(string $value): string
    {
        foreach (self::ACRONYMS as $acronym) {
            $value = preg_replace('/\b'.preg_quote($acronym, '/').'\b/iu', $acronym, $value) ?? $value;
        }

        return $value;
    }
}
