<?php

use App\Http\Controllers\Api\V1\SupplierController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:sanctum', 'prefix' => 'v1'], function () {
    Route::apiResource('/supplier', SupplierController::class);
});