<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateComplexTripRequest;
use App\Http\Requests\CreateTripRequest;
use App\Models\Trip;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class TripController extends Controller
{
    public function CreateTrip(CreateTripRequest $request): JsonResponse
    {
        $trip = Trip::create($request->validated());

        return response()->success($trip, 'Trip created', 201);
    }

    public function ComplexCreateTrip(CreateComplexTripRequest $request): JsonResponse
    {
        $data = $request->validated();
        $errors = [];
        $disk = Storage::disk('public');

        foreach ($data['points'] as $point) {
            if(!$disk->exists($point['file_name'])) {
                $errors[] = sprintf('Image \'%s\' does not exist', $point['file_name']);
            }
        }

        if (count($errors) > 0) {
            return response()->error([
                'points.*.file_name' => $errors,
            ], 'Validation error', 422);
        }

        return response()->success($data, 'OK', 200);
    }
}
