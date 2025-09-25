<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePointRequest;
use App\Http\Requests\UploadPointImageRequest;
use App\Models\Point;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class PointController extends Controller
{
    public function CreatePoint(CreatePointRequest $request): JsonResponse
    {
        $data = $request->validated();

        if(!Storage::disk('public')->exists($data['file_name'])) {
            return response()->error([], 'Image does not exist', 400);
        }

        $point = Point::create($data);

        return response()->success($point, 'Point created', 201);
    }

    public function UploadImage(UploadPointImageRequest $request): JsonResponse
    {
        $path = $request->file('file')->store('points', 'public');

        return response()->success(['file_name' => $path], 'File uploaded');
    }
}
