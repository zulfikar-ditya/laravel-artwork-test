<?php

use App\Http\Controllers\Auth\AuthenticateController;
use Illuminate\Support\Facades\Route;

Route::post("login", [AuthenticateController::class, "login"]);

Route::middleware("auth:sanctum")->group(function () {
    Route::post("logout", [AuthenticateController::class, "logout"]);
});
