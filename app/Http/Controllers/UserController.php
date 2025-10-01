<?php

namespace App\Http\Controllers;

use App\Http\Requests\SigninRequest;
use App\Http\Requests\SignupRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use OpenApi\Annotations as OA;

class UserController extends Controller
{
    /**
     * @OA\Post(
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
    public function signup(SignupRequest $request): JsonResponse {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $data['birthday'] = date($data['birthday']);

        return response()->success(User::create($data), "User created", 201);
    }

    public function signin(SigninRequest $request): JsonResponse {
        $credentials = $request->validated();

        if (Auth::attempt($credentials, true)) {
            $request->session()->regenerate();

            return response()->success([], "Logged in");
        }

        return response()->error([], "Wrong credentials", 401);
    }
}
