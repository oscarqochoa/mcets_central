<?php

use Illuminate\Support\Facades\Route;

Route::
    namespace ('Authentication')->prefix('authentication')->group(function () {
        Route::post('login', [\App\Http\Controllers\Users\AuthenticationController::class, 'login']);
    });

Route::group(['middleware' => 'api.auth'], function ($router) {

    Route::namespace ('Users')->prefix('users')->group(function () {
        Route::get('get', [\App\Http\Controllers\Users\UsersController::class, 'getUsers']);
        Route::get('get-one/{id}', [\App\Http\Controllers\Users\UsersController::class, 'getUser']);
        Route::post('register', [\App\Http\Controllers\Users\UsersController::class, 'register']);
        Route::put('update/{id}', [\App\Http\Controllers\Users\UsersController::class, 'update']);
        Route::post('delete/{id}', [\App\Http\Controllers\Users\UsersController::class, 'delete']);
    }
    );

});
