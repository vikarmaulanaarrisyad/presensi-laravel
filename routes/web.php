<?php

use App\Http\Controllers\{
    DashboardController,
    PresensiController
};
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.user.login');
});


Route::group(['middleware' => ['auth']], function () {
    Route::get('/dashboard',[DashboardController::class, 'index'])->name('dashboard');

    Route::resource('/presensi', PresensiController::class);
});