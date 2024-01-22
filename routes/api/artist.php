<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'artist',
    'as' => 'artist.',
    'namespace' => 'App\Http\Controllers\Artist',
    'middlware' => ['auth:asanctum', 'artist']
], function () {
    Route::apiResource('artwork', 'ArtworkController');
});
