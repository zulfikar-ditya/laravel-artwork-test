<?php

use App\Http\Controllers\Admin\ArtworkController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => ['auth:sanctum', 'admin'],
    'prefix' => 'admin',
    'as' => 'admin.',
], function () {
    Route::resource('user', UserController::class);
    Route::resource('role', RoleController::class)->only('index');
    Route::resource('artwork', ArtworkController::class);
});
