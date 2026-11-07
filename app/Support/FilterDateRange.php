<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FilterDateRange
{
    /**
     * Validate an optional date range used to refine a listing.
     *
     * Either boundary may be omitted, but supplied dates must use the value
     * produced by an HTML date input, cannot be in the future, and must keep
     * a chronological order when both are present.
     */
    public static function validate(Request $request, string $startKey, string $endKey): array
    {
        $endRules = ['nullable', 'date_format:Y-m-d', 'before_or_equal:today'];

        if ($request->filled($startKey) && $request->filled($endKey)) {
            $endRules[] = "after_or_equal:{$startKey}";
        }

        return Validator::make(
            $request->only([$startKey, $endKey]),
            [
                $startKey => ['nullable', 'date_format:Y-m-d', 'before_or_equal:today'],
                $endKey => $endRules,
            ],
            [
                "{$startKey}.date_format" => 'La fecha inicial no es válida.',
                "{$startKey}.before_or_equal" => 'La fecha inicial no puede ser posterior a hoy.',
                "{$endKey}.date_format" => 'La fecha final no es válida.',
                "{$endKey}.before_or_equal" => 'La fecha final no puede ser posterior a hoy.',
                "{$endKey}.after_or_equal" => 'La fecha final debe ser posterior o igual a la fecha inicial.',
            ],
        )->validate();
    }
}
