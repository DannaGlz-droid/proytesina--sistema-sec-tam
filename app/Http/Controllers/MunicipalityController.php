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
        $items = Municipality::when($q, function($query) use ($q) {
                    $query->where('name', 'like', "%{$q}%");
                })
                ->orderBy('name')
                ->limit(20)
                ->get(['id', 'name']);

        return response()->json($items);
    }

    /**
     * Store a new municipality (used when creating inline)
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'jurisdiction_id' => 'nullable|integer|exists:jurisdictions,id'
        ]);

        // Use firstOrCreate to avoid duplicates
        $mun = Municipality::firstOrCreate([
            'name' => $data['name']
        ], [
            'jurisdiction_id' => $data['jurisdiction_id'] ?? null
        ]);

        return response()->json($mun, 201);
    }
}
