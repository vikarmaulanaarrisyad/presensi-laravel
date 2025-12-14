<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.user.login');
});
Route::get('/dashboard', function () {
    return view('welcome');
})->name('dashboard');
