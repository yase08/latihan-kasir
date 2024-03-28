<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;


Route::get("/register", [AuthController::class, "registerView"]);
Route::post("/register", [AuthController::class, "register"])->name("auth.register");

Route::get("/login", [AuthController::class, "loginView"]);
Route::post("/login", [AuthController::class, "login"])->name("auth.login");

Route::get("/logout", [AuthController::class, "logout"])->name('auth.logout');
