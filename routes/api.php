<?php
use App\Http\Controllers\API\V1\RiderController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:sanctum', 'prefix' => 'v1'], function () {
    Route::apiResource('/rider', RiderController::class);
});