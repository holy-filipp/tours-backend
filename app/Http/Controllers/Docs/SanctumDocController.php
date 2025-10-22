<?php

namespace App\Http\Controllers\Docs;

use OpenApi\Annotations as OA;

/**
 * @OA\Get(
 *     operationId="getCsrfCookie",
 *     path="/api/sanctum/csrf-cookie",
 *     tags={"Авторизация"},
 *     summary="Получить CSRF токен",
 *     description="Генерирует и возвращает в XSRF-TOKEN cookie новый CSRF токен",
 *     @OA\Response(
 *         response=204,
 *         description="Успешное создание",
 *         @OA\Header(
 *             header="XSRF-TOKEN",
 *             description="CSRF токен",
 *             @OA\Schema(type="string", example="abcdefgiekqwe32")
 *         )
 *     )
 * )
 */
class SanctumDocController {}
