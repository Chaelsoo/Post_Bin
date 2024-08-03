<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name("index");
//These two are the same 
Route::get("/dashboard", function () {
    return view("dashboard.index");
})->name("home");



Route::view("/register", "auth.register")->name("registration");
Route::post("/register", [AuthController::class, "register"]);

Route::view("/login", "auth.login")->name("login");
Route::post("/login", [AuthController::class, "login"]);


Route::post("/logout", [AuthController::class, "logout"])->name("logout");
