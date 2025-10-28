<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DeathsExport;

class DeathExportController extends Controller
{
    public function export(Request $request)
    {
        $format = $request->get('format', 'xlsx');
        $filters = $request->all();

        $ext = $format === 'csv' ? 'csv' : 'xlsx';
        $fileName = 'defunciones_' . now()->format('Ymd_His') . '.' . $ext;

        return Excel::download(new DeathsExport($filters), $fileName);
    }
}
