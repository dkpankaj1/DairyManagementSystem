<?php

use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\SupplierController;
use App\Http\Controllers\API\V1\RiderController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:sanctum', 'prefix' => 'v1'], function () {
    Route::apiResource('/user', UserController::class);
    Route::apiResource('/supplier', SupplierController::class);
    Route::apiResource('/rider', RiderController::class);
});