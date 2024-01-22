<?php

use App\Http\Controllers\Response\ResponseController;
use Illuminate\Support\Facades\Route;

Route::get("files/{path}", [ResponseController::class, "handleResponseFile"])->name("response.file");
