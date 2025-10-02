<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePOIRequest;
use App\Models\POI;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

class POIController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/poi/create",
     *     tags={"Достопримечательности"},
     *     summary="Создать новую достопримечательность",
     *     description="Создаёт и возвращает новую достопримечательность в случае успеха",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"description", "file"},
     *                 @OA\Property(
     *                     property="file",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="description",
     *                     type="string",
     *                     example="Описание достопримечательности"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Успешное создание",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example="true"),
     *             @OA\Property(property="message", type="string", example="POI created"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="description", type="string", example="Описание достопримечательности"),
     *                 @OA\Property(property="file_name", type="string", example="pois/wydMNczrMWlmuNRPbB2jmO5PBzOEsPkm7mfchVng.png"),
     *                 @OA\Property(property="user_id", type="number", example=1),
     *                 @OA\Property(property="email", type="string", example="foo@bar.com"),
     *                 @OA\Property(property="updated_at", type="string", example="2000-01-01T00:00:00.000000Z"),
     *                 @OA\Property(property="created_at", type="string", example="2000-01-01T00:00:00.000000Z"),
     *                 @OA\Property(property="id", type="number", example=1),
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
     *                 @OA\Property(property="description", type="array", example={"The description field is required."},
     *                     @OA\Items(
     *                         type="string"
     *                     )
     *                 ),
     *                 @OA\Property(property="file", type="array", example={"The file field is required."},
     *                     @OA\Items(
     *                         type="string"
     *                     )
     *                 ),
     *             ),
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

    /**
     * @OA\Get(
     *     path="/api/poi/list",
     *     tags={"Достопримечательности"},
     *     summary="Получить список достопримечательностей",
     *     description="Возвращает список всех достопримечательностей",
     *     @OA\Response(
     *         response=200,
     *         description="Успешный вывод достопримечательностей",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example="true"),
     *             @OA\Property(property="message", type="string", example="POIs retrieved"),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="description", type="string", example="Описание достопримечательности"),
     *                     @OA\Property(property="file_name", type="string", example="pois/wydMNczrMWlmuNRPbB2jmO5PBzOEsPkm7mfchVng.png"),
     *                     @OA\Property(property="user_id", type="number", example=1),
     *                     @OA\Property(property="email", type="string", example="foo@bar.com"),
     *                     @OA\Property(property="updated_at", type="string", example="2000-01-01T00:00:00.000000Z"),
     *                     @OA\Property(property="created_at", type="string", example="2000-01-01T00:00:00.000000Z"),
     *                     @OA\Property(property="id", type="number", example=1),
     *                 ),
     *             ),
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
    public function GetPOIs(): JsonResponse
    {
        return response()->success(POI::all(), 'POIs retrieved', 200);
    }
}
