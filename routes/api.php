<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController; 
Route::group(['middleware' => ['api']], function () {
    Route::post('/login', [AuthController::class, 'loginMobile']);
    Route::get('/users', [AuthController::class, 'getAllUsers']);
});