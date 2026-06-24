<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Municipality;

class MunicipalityController extends Controller
{
    /**
     * Search municipalities by name (for AJAX autocomplete)
     */
    public function search(Request $request)
    {
        $q = $request->get('q', '');
        $districtId = $request->get('district_id');
        $limit = min(max((int) $request->get('limit', 20), 1), 100);

        $items = Municipality::when($q, function($query) use ($q) {
                    $query->where('name', 'like', "%{$q}%");
                })
                ->when($districtId, function($query) use ($districtId) {
                    $query->where('district_id', $districtId);
                })
                ->orderBy('name')
                ->limit($limit)
                ->get(['id', 'name']);

        // Return results as array (do NOT append a "No encontrado" option)
        return response()->json($items->toArray());
    }

    /**
     * Store a new municipality (used when creating inline)
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'district_id' => 'nullable|integer|exists:districts,id'
        ]);

        // Use firstOrCreate to avoid duplicates
        $mun = Municipality::firstOrCreate([
            'name' => $data['name']
        ], [
            'district_id' => $data['district_id'] ?? null
        ]);

        return response()->json($mun, 201);
    }
}
