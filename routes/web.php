<?php

use App\Http\Controllers\{
    DashboardController,
    DepartemenController,
    GuruController,
    MonitoringPresensiGuruController,
    PengajuanIzinController,
    PresensiController
};
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.user.login');
});

Route::get('/admin', function () {
    return view('auth.login');
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
    Route::controller(GuruController::class)->group(function () {
        Route::get('guru/data', 'data')->name('guru.data');
        Route::get('guru', 'index')->name('guru.index');
        Route::get('guru/create', 'create')->name('guru.create');
        Route::post('/guru/import-excel', 'importEXCEL')->name('guru.import_excel');
        Route::post('guru', 'store')->name('guru.store');
        Route::get('guru/{id}', 'show')->name('guru.show');
        Route::get('guru/{id}/edit', 'edit')->name('guru.edit');
        Route::put('guru/{id}', 'update')->name('guru.update');
        Route::delete('guru/{id}', 'destroy')->name('guru.destroy');
    });
    Route::controller(DepartemenController::class)->group(function () {
        Route::get('departemen/data', 'data')->name('departemen.data');
        Route::get('departemen', 'index')->name('departemen.index');
        Route::get('departemen/create', 'create')->name('departemen.create');
        Route::post('/departemen/import-excel', 'importEXCEL')->name('departemen.import_excel');
        Route::post('departemen', 'store')->name('departemen.store');
        Route::get('departemen/{id}', 'show')->name('departemen.show');
        Route::get('departemen/{id}/edit', 'edit')->name('departemen.edit');
        Route::put('departemen/{id}', 'update')->name('departemen.update');
        Route::delete('departemen/{id}', 'destroy')->name('departemen.destroy');
    });

    //Monitoring Presensi Guru
    Route::get('/monitoring/presensi/guru/data', [MonitoringPresensiGuruController::class, 'data'])->name('monitoring.presensi_guru.data');
    Route::get('/monitoring/presensi/guru', [MonitoringPresensiGuruController::class, 'index'])->name('monitoring.presensi_guru');
});
