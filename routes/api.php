<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Books\BookController;
use App\Http\Controllers\Reservtion\ReservationController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'auth', 'as' => 'auth'], function () {
    Route::post('register', [AuthController::class, 'register'])->middleware('throttle:5,1');
    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:5,1');
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});

Route::group(['prefix' => 'books', 'as' => 'books', 'middleware' => 'auth:sanctum'], function () {
    Route::get('', [BookController::class, 'index']);
    Route::get('find', [BookController::class, 'find']);
    Route::post('', [BookController::class, 'store'])->middleware('admin', 'throttle:10,1');
    Route::get('{book}', [BookController::class, 'show']);
    Route::put('{book}', [BookController::class, 'update'])->middleware('admin');
    Route::delete('{book}', [BookController::class, 'destroy'])->middleware('admin', 'throttle:reservation');
});

Route::group(['prefix' => 'reservations', 'as' => 'reservations', 'middleware' => 'auth:sanctum'], function () {
    Route::get('', [ReservationController::class, 'index']);
    Route::get('{reservation}', [ReservationController::class, 'reservationDetail']);
    Route::post('{book}', [ReservationController::class, 'reserveBook']);
    Route::patch('{reservation}/cancel', [ReservationController::class, 'cancelReservation']);
    Route::patch('{reservation}/return', [ReservationController::class, 'returnReservation']);
});
