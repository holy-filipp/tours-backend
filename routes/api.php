<?php

use App\Http\Controllers\PageController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\POIController;
use App\Http\Controllers\PointController;
use App\Http\Controllers\TripController;
use Illuminate\Support\Facades\Route;

Route::post('/user/signup', [UserController::class, 'signup']);
Route::post('/user/signin', [UserController::class, 'signin'])->middleware('web');

Route::middleware(['auth:sanctum', 'web'])->group(function() {
    Route::get('/protected', function() {
        return response()->success([], 'you are on protected endpoint', 200);
    })->middleware('checkRole:user');
    Route::get('/content/udmurtia', [PageController::class, 'GetUdmurtia']);
    Route::post('/poi/create', [POIController::class, 'CreatePOI']);
    Route::get('/poi/list', [POIController::class, 'GetPOIs']);
    Route::get('/trip/search', [TripController::class, 'FindTrips']);

    Route::post('/trip/image', [PointController::class, 'UploadImage'])->middleware('checkRole:admin');
    Route::post('/trip/complex', [TripController::class, 'ComplexCreateTrip'])->middleware('checkRole:admin');
    Route::get('/trip/list', [TripController::class, 'GetTrips'])->middleware('checkRole:admin');
});

