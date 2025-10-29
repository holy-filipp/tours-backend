<?php

namespace App\Http\Controllers;

use App\Http\Requests\SigninRequest;
use App\Http\Requests\SignupRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use OpenApi\Annotations as OA;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * @OA\Post(
     *     operationId="signup",
     *     path="/api/user/signup",
     *     tags={"Пользователь"},
     *     summary="Зарегистрировать нового пользователя",
     *     description="Создаёт и возвращает нового пользователя в случае успеха",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(
     *                     required={"first_name", "last_name", "birthday", "email", "password"},
     *                     @OA\Property(property="first_name", type="string", example="Иван"),
     *                     @OA\Property(property="last_name", type="string", example="Иванов"),
     *                     @OA\Property(property="patronymic", type="string", example="Иванович"),
     *                     @OA\Property(property="birthday", type="string", example="01.01.2000"),
     *                     @OA\Property(property="email", type="string", example="foo@bar.com"),
     *                     @OA\Property(property="password", type="string", example="password")
     *                 )
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Успешная регистрация",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example="true"),
     *             @OA\Property(property="message", type="string", example="User created"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="first_name", type="string", example="Иван"),
     *                 @OA\Property(property="last_name", type="string", example="Иванов"),
     *                 @OA\Property(property="patronymic", type="string", example="Иванович"),
     *                 @OA\Property(property="email", type="string", example="foo@bar.com"),
     *                 @OA\Property(property="birthday", type="string", example="2000-01-01T00:00:00.000000Z"),
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
     *                 @OA\Property(property="first_name", type="array", example={"The first name field is required."},
     *                     @OA\Items(
     *                         type="string"
     *                     )
     *                 ),
     *                 @OA\Property(property="last_name", type="array", example={"The last name field is required."},
     *                     @OA\Items(
     *                         type="string"
     *                     )
     *                 ),
     *                 @OA\Property(property="email", type="array", example={"The email field is required."},
     *                     @OA\Items(
     *                         type="string"
     *                     )
     *                 ),
     *                 @OA\Property(property="password", type="array", example={"The password field is required"},
     *                     @OA\Items(
     *                         type="string"
     *                     )
     *                 ),
     *                 @OA\Property(property="birthday", type="array", example={"The birthday field is required"},
     *                     @OA\Items(
     *                         type="string"
     *                     )
     *                 ),
     *             ),
     *         )
     *     )
     * )
     */
    public function signup(SignupRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $data['birthday'] = date($data['birthday']);

        return response()->success(User::create($data), "User created", 201);
    }

    /**
     * @OA\Post(
     *     path="/api/user/signin",
     *     operationId="signin",
     *     tags={"Пользователь"},
     *     summary="Войти в аккаунт",
     *     description="Проверяет credentials и создаёт новый токен в случае успеха",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(
     *                     required={"email", "password"},
     *                     @OA\Property(property="email", type="string", example="foo@bar.com"),
     *                     @OA\Property(property="password", type="string", example="password")
     *                 )
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Успешный вход",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example="true"),
     *             @OA\Property(property="message", type="string", example="Logged in"),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(type="string"),
     *                 example={}
     *             ),
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Неверные credentials",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example="false"),
     *             @OA\Property(property="message", type="string", example="Wrong credentials"),
     *             @OA\Property(property="errors", type="array",
     *                 @OA\Items(type="string"),
     *                 example={}
     *             ),
     *         )
     *     )
     * )
     */
    public function signin(SigninRequest $request): JsonResponse
    {
        $credentials = $request->validated();

        if (Auth::attempt($credentials, true)) {
            $request->session()->regenerate();

            return response()->success([], "Logged in");
        }

        return response()->error([], "Wrong credentials", 401);
    }

    /**
     * @OA\Get(
     *     operationId="getUser",
     *     path="/api/user/me",
     *     tags={"Пользователь"},
     *     summary="Получить пользователя по сессии",
     *     description="Возвращает пользователя из сессии если это возможно",
     *     @OA\Response(
     *         response=200,
     *         description="Успешный вывод пользователя",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example="true"),
     *             @OA\Property(property="message", type="string", example="Session active"),
     *             @OA\Property(property="data", type="object",
     *                 required={"first_name", "last_name", "birthday", "email", "role"},
     *                 @OA\Property(property="first_name", type="string", example="Иван"),
     *                 @OA\Property(property="last_name", type="string", example="Иванов"),
     *                 @OA\Property(property="patronymic", type="string", example="Иванович"),
     *                 @OA\Property(property="birthday", type="string", example="2000-01-01T00:00:00.000000Z"),
     *                 @OA\Property(property="email", type="string", example="foo@bar.com"),
     *                 @OA\Property(property="role", type="string", example="user"),
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
    public function me(Request $request): JsonResponse {
        $user = $request->user();

        return response()->success([
            "first_name" => $user->first_name,
            "last_name" => $user->last_name,
            "patronymic" => $user->patronymic,
            "birthday" => $user->birthday,
            "email" => $user->email,
            "role" => $user->role,
        ], "Session active", 200);
    }
}
