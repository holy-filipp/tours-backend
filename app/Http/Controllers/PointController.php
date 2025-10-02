<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePointRequest;
use App\Http\Requests\UploadPointImageRequest;
use App\Models\Point;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use OpenApi\Annotations as OA;

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

    /**
     * @OA\Post(
     *     path="/api/trip/image",
     *     tags={"Точки"},
     *     summary="Загрузить картинку для точки",
     *     description="Загружает картинку для точки и возвращает её путь в случае успеха",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"file"},
     *                 @OA\Property(
     *                     property="file",
     *                     type="string"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Успешная загрузка",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example="true"),
     *             @OA\Property(property="message", type="string", example="File uploaded"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="file_name", type="string", example="pois/wydMNczrMWlmuNRPbB2jmO5PBzOEsPkm7mfchVng.png"),
     *             ),
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Ошибка валидации данных",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example="false"),
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="file", type="array", example={"The file field is required."},
     *                     @OA\Items(
     *                         type="string"
     *                     )
     *                 ),
     *             ),
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Роли пользователя не достаточно для выполнения операции",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="The role does not match the requirements"),
     *             @OA\Property(property="errors", type="array",
     *                 @OA\Items(
     *                     type="string"
     *                 ),
     *                 example={}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Пользователь не авторизирован",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated."),
     *         )
     *     )
     * )
     */
    public function UploadImage(UploadPointImageRequest $request): JsonResponse
    {
        $path = $request->file('file')->store('points', 'public');

        return response()->success(['file_name' => $path], 'File uploaded');
    }
}
