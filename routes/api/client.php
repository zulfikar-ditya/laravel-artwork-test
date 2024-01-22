<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'namespace' => 'App\Http\Controllers\Client',
    'prefix' => 'client',
    'as' => 'client.',
], function () {
    Route::apiResource('artwork', 'ArtworkController')->only(['index']);
});
