<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Opponent;
use App\Models\SportsHall;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MatchSuggestionController extends Controller
{
    public function locations(Request $request): JsonResponse
    {
        $query = trim((string) $request->query('q', ''));

        return response()->json(
            SportsHall::query()
                ->when($query !== '', fn ($builder) => $builder->where('name', 'like', "%{$query}%"))
                ->orderBy('name')
                ->limit(8)
                ->get(['id', 'name'])
        );
    }

    public function opponents(Request $request): JsonResponse
    {
        $query = trim((string) $request->query('q', ''));

        return response()->json(
            Opponent::query()
                ->when($query !== '', fn ($builder) => $builder->where('name', 'like', "%{$query}%"))
                ->orderBy('name')
                ->limit(8)
                ->get(['id', 'name', 'logo_path'])
        );
    }
}
