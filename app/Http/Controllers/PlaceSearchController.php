<?php

namespace App\Http\Controllers;

use App\Services\PlaceSearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlaceSearchController extends Controller
{
    public function __invoke(Request $request, PlaceSearchService $places): JsonResponse
    {
        $query = (string) $request->query('q', '');

        return response()->json([
            'data' => $places->search($query),
        ]);
    }
}
