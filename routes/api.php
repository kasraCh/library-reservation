<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::group(['prefix' => 'auth', 'as' => 'auth'], function () {
    Route::post('register', [AuthController::class, 'register']);

    Route::post('login', [AuthController::class, 'login']);

    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});

Route::group(['prefix' => 'books', 'as' => 'books', 'middleware' => 'auth:sanctum'], function () {
    Route::get('', [\App\Http\Controllers\Books\BookController::class, 'index']);
    Route::post('', [\App\Http\Controllers\Books\BookController::class, 'store'])->middleware('admin');
    Route::get('{book}', [\App\Http\Controllers\Books\BookController::class, 'show']);
    Route::put('{book}', [\App\Http\Controllers\Books\BookController::class, 'update'])->middleware('admin');
    Route::delete('{book}', [\App\Http\Controllers\Books\BookController::class, 'destroy'])->middleware('admin');
});
