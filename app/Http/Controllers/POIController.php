<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePOIRequest;
use App\Models\POI;
use Illuminate\Http\JsonResponse;

class POIController extends Controller
{
    public function CreatePOI(CreatePOIRequest $request): JsonResponse
    {
        $data = $request->validated();
        $path = $request->file('file')->store('pois', 'public');
        $poi = POI::create([
            'description' => $data['description'],
            'file_name' => $path,
            'user_id' => $request->user()->id,
        ]);

        return response()->success($poi, 'POI created', 201);
    }

    public function GetPOIs(): JsonResponse
    {
        return response()->success(POI::all(), 'POIs retrieved', 200);
    }
}
