<?php

use App\Http\Controllers\PageController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\POIController;
use App\Http\Controllers\PointController;
use App\Http\Controllers\TripController;
use Illuminate\Support\Facades\Route;

// Авторизация
Route::post('/user/signup', [UserController::class, 'signup']);
Route::post('/user/signin', [UserController::class, 'signin']);

// Контент по Удмуртии
Route::get('/content/udmurtia', [PageController::class, 'GetUdmurtia']);

Route::middleware('auth:sanctum')->group(function() {
    Route::get('/user/me', [UserController::class, 'me']);

    // Достопримечательности
    Route::post('/poi/create', [POIController::class, 'CreatePOI']);
    Route::get('/poi/list', [POIController::class, 'GetPOIs']);

    // Экскурсии
    Route::get('/trip/search', [TripController::class, 'FindTrips']);
    Route::post('/trip/image', [PointController::class, 'UploadImage'])->middleware('checkRole:admin');
    Route::post('/trip/complex', [TripController::class, 'ComplexCreateTrip'])->middleware('checkRole:admin');
    Route::get('/trip/list', [TripController::class, 'GetTrips']);
});
