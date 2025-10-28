<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DeathCause;
use App\Models\DeathLocation;

class LookupController extends Controller
{
    /**
     * Search death causes for Tom Select autocomplete
     */
    public function searchCauses(Request $request)
    {
        $q = trim((string)$request->get('q', ''));
        $query = DeathCause::query();

        if ($q !== '') {
            $query->where('name', 'like', "%{$q}%");
        }

        $results = $query->orderBy('name')->limit(30)->get(['id', 'name']);

        return response()->json($results);
    }

    /**
     * Search death locations for Tom Select autocomplete
     */
    public function searchLocations(Request $request)
    {
        $q = trim((string)$request->get('q', ''));
        $query = DeathLocation::query()->where('is_active', true);

        if ($q !== '') {
            $query->where('name', 'like', "%{$q}%");
        }

        // return most used first when query empty
        if ($q === '') {
            $query->orderByDesc('usage_count');
        } else {
            $query->orderBy('name');
        }

        $results = $query->limit(40)->get(['id', 'name']);

        return response()->json($results);
    }
}
