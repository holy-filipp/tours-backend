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
use Symfony\Component\Console\Output\ConsoleOutput;

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
            ], 'Route, points and trip created', 200);
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

    public function GetTrips()
    {
        $trips = Trip::all();
        $output = [];

        foreach($trips as $trip) {
            $route = Route::find($trip->route_id);
            $output[] = [
                'trip' => $trip,
                'route' => $route,
                'points' => $route->points()->get()
            ];
        }

        return response()->success($output, 'Fetched trips');
    }

    public function FindTrips(FindTripsRequest $request): JsonResponse
    {
        $search = $request->validated()['search'];
        $results = [];

        $pointsByDescription = Point::where('description', 'ILIKE', '%' . $search . '%')->get();
        $pointsByName =  Point::where('name', 'ILIKE', '%' . $search . '%')->get();
        $routesByStartLocation = Route::where('start_location', 'ILIKE', '%' . $search . '%')->get();

        foreach ($routesByStartLocation as $route) {
            $results[] = [
                'trip' => Trip::where('route_id', $route->id)->first(),
                'route' => $route,
                'points' => $route->points()->get()
            ];
        }

        foreach ($pointsByDescription as $point) {
            foreach ($point->routes()->get() as $route) {
                $results[] = [
                    'trip' => Trip::where('route_id', $route->id)->first(),
                    'route' => $route,
                    'points' => $route->points()->get()
                ];
            }
        }

        foreach ($pointsByName as $point) {
            foreach ($point->routes()->get() as $route) {
                $results[] = [
                    'trip' => Trip::where('route_id', $route->id)->first(),
                    'route' => $route,
                    'points' => $route->points()->get()
                ];
            }
        }

        return response()->success($results, 'Search results');
    }
}
