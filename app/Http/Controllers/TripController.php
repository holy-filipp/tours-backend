<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateComplexTripRequest;
use App\Http\Requests\CreateTripRequest;
use App\Http\Requests\FindTripsRequest;
use App\Models\Point;
use App\Models\Route;
use App\Models\Trip;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use OpenApi\Annotations as OA;
use Symfony\Component\Console\Output\ConsoleOutput;

class TripController extends Controller
{
    public function CreateTrip(CreateTripRequest $request): JsonResponse
    {
        $trip = Trip::create($request->validated());

        return response()->success($trip, 'Trip created', 201);
    }

    /**
     * @OA\Post(
     *     path="/api/trip/complex",
     *     tags={"Экскурсии"},
     *     summary="Создать экскурсию, маршрут и точки",
     *     description="Создаёт и возвращает новую экскурсию, маршрут и привязанные к нему точки. Всё в одном запросе",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(
     *                     required={"starts_at", "capacity", "min_age", "price", "start_location", "duration"},
     *                     @OA\Property(property="starts_at", type="string", example="2000-01-01 08:00"),
     *                     @OA\Property(property="capacity", type="number", example=5),
     *                     @OA\Property(property="min_age", type="number", example=16),
     *                     @OA\Property(property="price", type="number", example=9999),
     *                     @OA\Property(property="start_location", type="string", example="Ижевск"),
     *                     @OA\Property(property="duration", type="number", example=10),
     *                     @OA\Property(property="points", type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             required={"file_name", "description", "day_of_the_route", "name"},
     *                             @OA\Property(property="file_name", type="string", example="points/pCobXrHQoHbiI2JDiWMJj8zVKLEu26qFIMogpwcD.jpg"),
     *                             @OA\Property(property="description", type="string", example="Описание точки"),
     *                             @OA\Property(property="day_of_the_route", type="number", example=1),
     *                             @OA\Property(property="name", type="string", example="Название точки"),
     *                         )
     *                     )
     *                 )
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Успешное создание",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example="true"),
     *             @OA\Property(property="message", type="string", example="Route, points and trip created"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="route", type="object",
     *                     @OA\Property(property="start_location", type="string", example="Ижевск"),
     *                     @OA\Property(property="duration", type="number", example=10),
     *                     @OA\Property(property="updated_at", type="string", example="2000-01-01T00:00:00.000000Z"),
     *                     @OA\Property(property="created_at", type="string", example="2000-01-01T00:00:00.000000Z"),
     *                     @OA\Property(property="id", type="number", example=1),
     *                 ),
     *                 @OA\Property(property="trip", type="object",
     *                      @OA\Property(property="starts_at", type="string", example="2000-01-01 08:00"),
     *                      @OA\Property(property="capacity", type="number", example=5),
     *                      @OA\Property(property="min_age", type="number", example=16),
     *                      @OA\Property(property="price", type="number", example=9999),
     *                      @OA\Property(property="route_id", type="number", example=1),
     *                      @OA\Property(property="updated_at", type="string", example="2000-01-01T00:00:00.000000Z"),
     *                      @OA\Property(property="created_at", type="string", example="2000-01-01T00:00:00.000000Z"),
     *                      @OA\Property(property="id", type="number", example=1),
     *                 ),
     *                @OA\Property(property="points", type="array",
     *                    @OA\Items(
     *                        type="object",
     *                        required={"file_name", "description", "day_of_the_route", "name"},
     *                        @OA\Property(property="file_name", type="string", example="points/pCobXrHQoHbiI2JDiWMJj8zVKLEu26qFIMogpwcD.jpg"),
     *                        @OA\Property(property="description", type="string", example="Описание точки"),
     *                        @OA\Property(property="day_of_the_route", type="number", example=1),
     *                        @OA\Property(property="name", type="string", example="Название точки"),
     *                        @OA\Property(property="updated_at", type="string", example="2000-01-01T00:00:00.000000Z"),
     *                        @OA\Property(property="created_at", type="string", example="2000-01-01T00:00:00.000000Z"),
     *                    )
     *                )
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
     *                 @OA\Property(property="starts_at", type="array", example={"The starts at field is required."},
     *                     @OA\Items(
     *                         type="string"
     *                     )
     *                 ),
     *                 @OA\Property(property="capacity", type="array", example={"The capacity field is required."},
     *                     @OA\Items(
     *                         type="string"
     *                     )
     *                 ),
     *                 @OA\Property(property="min_age", type="array", example={"The min age field is required."},
     *                     @OA\Items(
     *                         type="string"
     *                     )
     *                 ),
     *                 @OA\Property(property="price", type="array", example={"The price field is required."},
     *                     @OA\Items(
     *                         type="string"
     *                     )
     *                 ),
     *                 @OA\Property(property="start_location", type="array", example={"The start location field is required."},
     *                     @OA\Items(
     *                         type="string"
     *                     )
     *                 ),
     *                 @OA\Property(property="duration", type="array", example={"The duration field is required."},
     *                     @OA\Items(
     *                         type="string"
     *                     )
     *                 ),
     *                 @OA\Property(property="points", type="array", example={"The points field is required."},
     *                     @OA\Items(
     *                         type="string"
     *                     )
     *                 ),
     *                 @OA\Property(property="points.n.file_name", type="array", example={"The points.n.file_name field is required."},
     *                     @OA\Items(
     *                         type="string"
     *                     )
     *                 ),
     *                 @OA\Property(property="points.n.description", type="array", example={"The points.n.description field is required."},
     *                     @OA\Items(
     *                         type="string"
     *                     )
     *                 ),
     *                 @OA\Property(property="points.n.day_of_the_route", type="array", example={"The points.n.day_of_the_route field is required."},
     *                     @OA\Items(
     *                         type="string"
     *                     )
     *                 ),
     *                 @OA\Property(property="points.n.name", type="array", example={"The points.n.name field is required."},
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
    public function ComplexCreateTrip(CreateComplexTripRequest $request): JsonResponse
    {
        $data = $request->validated();
        $disk = Storage::disk('public');
        $errors = [];
        $points = [];

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

        DB::beginTransaction();

        try {
            $route = Route::create([
                'start_location' => $data['start_location'],
                'duration' => $data['duration'],
            ]);

            foreach ($data['points'] as $point) {
                $point_instance = Point::create([
                    'file_name' => $point['file_name'],
                    'description' => $point['description'],
                    'name' => $point['name']
                ]);

                $point_instance->routes()->attach($route->id, ['day_of_the_route' => $point['day_of_the_route']]);
                $points[] = $point_instance;
            }

            $trip = Trip::create([
                'starts_at' => date($data['starts_at']),
                'capacity' => $data['capacity'],
                'min_age' => $data['min_age'],
                'price' => $data['price'],
                'route_id' => $route->id,
            ]);

            DB::commit();

            return response()->success([
                'route' => $route,
                'trip' => $trip,
                'points' => $points,
            ], 'Route, points and trip created', 201);
        } catch (\Exception $e) {
            DB::rollBack();
            $route->delete();
            return response()->error([], $e->getMessage(), 500);
        } catch (\Throwable $e) {
            DB::rollBack();
            $route->delete();
            return response()->error([], $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/trip/list",
     *     tags={"Экскурсии"},
     *     summary="Получить все экскурсии",
     *     description="Запрос для получения списка всех экскурсий вместе с точками и маршрутами (пагинация лень пока)",
     *     @OA\Response(
     *         response=200,
     *         description="Расширенный список эскурсий",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example="true"),
     *             @OA\Property(property="message", type="string", example="Fetched trips"),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(type="object",
     *                     required={"id", "duration", "price", "start_location", "archived"},
     *                     @OA\Property(property="id", type="number", example=1),
     *                     @OA\Property(property="duration", type="number", example=10),
     *                     @OA\Property(property="price", type="number", example=9999),
     *                     @OA\Property(property="start_location", type="string", example="Ижевск"),
     *                     @OA\Property(property="archived", type="boolean", example="true")
     *                 )
     *             ),
     *         )
     *     )
     * )
     */
    public function GetTrips()
    {
        $trips = Trip::all();
        $output = [];

        foreach($trips as $trip) {
            $route = Route::find($trip->route_id);
            $output[] = [
                'id' => $trip->id,
                'duration' => $route->duration,
                'price' => $trip->price,
                'start_location' => $route->start_location,
                'archived' => $trip->archived
            ];
        }

        return response()->success($output, 'Fetched trips');
    }

    /**
     * @OA\Get(
     *     path="/api/trip/search",
     *     tags={"Экскурсии"},
     *     summary="Поиск по экскурсиям, точкам и маршрутам",
     *     description="Запрос для поиска по всем маршрутам, точкам и экскурсиям. Ищет в полях description, name и start_location",
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             example="Ижевск"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Результаты поиска",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example="true"),
     *             @OA\Property(property="message", type="string", example="Search results"),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(type="object",
     *                     required={"id", "duration", "price", "start_location", "archived"},
     *                     @OA\Property(property="id", type="number", example=1),
     *                     @OA\Property(property="duration", type="number", example=10),
     *                     @OA\Property(property="price", type="number", example=9999),
     *                     @OA\Property(property="start_location", type="string", example="Ижевск"),
     *                     @OA\Property(property="archived", type="boolean", example="true")
     *                 )
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
     *                 @OA\Property(property="search", type="array", example={"The search field is required."},
     *                     @OA\Items(
     *                         type="string"
     *                     )
     *                 ),
     *             ),
     *         )
     *     )
     * )
     */
    public function FindTrips(FindTripsRequest $request): JsonResponse
    {
        $search = $request->validated()['search'];
        $results = [];

        $pointsByDescription = Point::where('description', 'ILIKE', '%' . $search . '%')->get();
        $pointsByName =  Point::where('name', 'ILIKE', '%' . $search . '%')->get();
        $routesByStartLocation = Route::where('start_location', 'ILIKE', '%' . $search . '%')->get();

        foreach ($routesByStartLocation as $route) {
            $trip = Trip::where('route_id', $route->id)->first();

            $results[$trip->id] = [
                'id' => $trip->id,
                'duration' => $route->duration,
                'price' => $trip->price,
                'start_location' => $route->start_location,
                'archived' => $trip->archived
            ];
        }

        foreach ($pointsByDescription as $point) {
            foreach ($point->routes()->get() as $route) {
                $trip = Trip::where('route_id', $route->id)->first();

                $results[$trip->id] = [
                    'id' => $trip->id,
                    'duration' => $route->duration,
                    'price' => $trip->price,
                    'start_location' => $route->start_location,
                    'archived' => $trip->archived
                ];
            }
        }

        foreach ($pointsByName as $point) {
            foreach ($point->routes()->get() as $route) {
                $trip = Trip::where('route_id', $route->id)->first();

                $results[$trip->id] = [
                    'id' => $trip->id,
                    'duration' => $route->duration,
                    'price' => $trip->price,
                    'start_location' => $route->start_location,
                    'archived' => $trip->archived
                ];
            }
        }

        return response()->success(array_values($results), 'Search results');
    }
}
