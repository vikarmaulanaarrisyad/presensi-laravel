<?php

use App\Http\Controllers\{
    DashboardController,
    PengajuanIzinController,
    PresensiController
};
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.user.login');
});


Route::group(['middleware' => ['auth']], function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('/presensi', PresensiController::class);
    Route::post('/presensi/search', [PresensiController::class, 'search'])->name('presensi.search');
    Route::controller(PengajuanIzinController::class)->group(function () {
        Route::get('pengajuan/izin', 'index')->name('pengajuan.izin.index');
        Route::get('pengajuan/izin/create', 'create')->name('pengajuan.izin.create');
        Route::post('pengajuan/izin', 'store')->name('pengajuan.izin.store');
        Route::get('pengajuan/izin/{izin}', 'show')->name('pengajuan.izin.show');
        Route::get('pengajuan/izin/{izin}/edit', 'edit')->name('pengajuan.izin.edit');
        Route::put('pengajuan/izin/{izin}', 'update')->name('pengajuan.izin.update');
        Route::delete('pengajuan/izin/{izin}', 'destroy')->name('pengajuan.izin.destroy');
    });
});
