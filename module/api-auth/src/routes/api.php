<?php

use Cortexitsolution\ApiAuth\Http\Controllers\Api\Auth\LoginController;
use Cortexitsolution\ApiAuth\Http\Controllers\Api\Auth\PasswordResetController;
use Cortexitsolution\ApiAuth\Http\Controllers\Api\Auth\PasswordUpdateController;
use Cortexitsolution\ApiAuth\Http\Controllers\Api\Auth\ProfileController;

Route::group(['prefix'=> 'api/v1'], function () {
    Route::post("/login",[LoginController::class,"login"]);
    Route::post("/forgot-password",[PasswordResetController ::class,"sendPasswordResetOtpEmail"]);
    Route::put("/reset-password",[PasswordResetController::class,"resetPassword"]);
});

Route::group(['middleware' => 'auth:sanctum','prefix'=> 'api/v1'], function () {

    Route::get('/profile',[ProfileController ::class,'profile'])->name('profile.show');
    Route::put('/profile',[ProfileController::class,'update'])->name('profile.update');
    Route::put('/update-password',[PasswordUpdateController::class,'update'])->name('password.update');
    Route::post("/logout",[LoginController::class,"logout"]);
});

?>