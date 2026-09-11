<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelFormat;
use App\Exports\DeathsExport;

class DeathExportController extends Controller
{
    public function export(Request $request)
    {
        $format = $request->get('format', 'xlsx');
        $filters = $request->all();

        $ext = $format === 'csv' ? 'csv' : 'xlsx';
        $scope = $request->filled('analysis_type') ? '_datos_grafica' : '';
        $fileName = 'defunciones' . $scope . '_' . now()->format('Ymd_His') . '.' . $ext;

        return Excel::download(
            new DeathsExport($filters),
            $fileName,
            $format === 'csv' ? ExcelFormat::CSV : ExcelFormat::XLSX
        );
    }
}
