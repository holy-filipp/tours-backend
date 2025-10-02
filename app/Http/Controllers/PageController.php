<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

class PageController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/content/udmurtia",
     *     tags={"Контент"},
     *     summary="Контент по Удмуртии",
     *     description="Возвращает статический контент в формате markdown для страницы",
     *     @OA\Response(
     *         response=200,
     *         description="Успешный вывод контента",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example="true"),
     *             @OA\Property(property="message", type="string", example="Content fetched"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="name", type="string", example="about-udmurtia"),
     *                 @OA\Property(property="content", type="string", example="Lorem ipsum"),
     *                 @OA\Property(property="updated_at", type="string", example="2000-01-01T00:00:00.000000Z"),
     *                 @OA\Property(property="created_at", type="string", example="2000-01-01T00:00:00.000000Z"),
     *                 @OA\Property(property="id", type="number", example=1),
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
    public function GetUdmurtia(): JsonResponse {
        $data = Page::where('name', 'about-udmurtia')->first();

        return response()->success($data, "Content fetched");
    }
}
