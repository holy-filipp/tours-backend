<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateRouteRequest;
use App\Models\Route;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RouteController extends Controller
{
    public function CreateRoute(CreateRouteRequest $request): JsonResponse
    {
        $route = Route::create($request->validated());

        return response()->success($route, 'Route created', 201);
    }
}
