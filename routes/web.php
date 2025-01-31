<?php

use App\Http\Controllers\IndexController;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

Route::get('/', IndexController::class);

Route::get(
    '/login',
    fn() => response()->json('Unauthorised user Authorization header', Response::HTTP_UNAUTHORIZED)
)->name('login');
