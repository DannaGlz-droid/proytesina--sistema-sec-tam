<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\District;

class DistrictController extends Controller
{
    /**
     * Search districts by name (for AJAX autocomplete)
     */
    public function search(Request $request)
    {
        $q = $request->get('q', '');
        $items = District::when($q, function($query) use ($q) {
                    $query->where('name', 'like', "%{$q}%");
                })
                ->orderBy('name')
                ->limit(20)
                ->get(['id', 'name']);

        // Agregar "Oficina Central" si coincide con la búsqueda
        $centralOffice = collect([
            ['id' => 999, 'name' => 'Oficina Central']
        ]);
        
        if ($q === '' || str_contains('Oficina Central', $q) || str_contains('oficina central', strtolower($q))) {
            $items = $items->concat($centralOffice);
        }

        // Return results as array
        return response()->json($items->toArray());
    }
}
