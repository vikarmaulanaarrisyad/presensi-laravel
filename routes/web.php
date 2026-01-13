<?php

use App\Http\Controllers\{
    DashboardController,
    DepartemenController,
    GuruController,
    JabatanController,
    KonfigurasiLokasiController,
    LaporanPresensiGuruController,
    MonitoringPresensiGuruController,
    PengajuanIzinController,
    PersetujuanIzinGuruController,
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

    Route::get('guru/{guru}/penempatan', [GuruController::class, 'edit'])
        ->name('guru.penempatan.edit');
    Route::put('guru/{id}/penempatan', [GuruController::class, 'updatePenempatan'])->name('guru.penempatan.update');

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
    Route::get('/monitoring/presensi/guru/data', [MonitoringPresensiGuruController::class, 'data'])->name('monitoring.presensi_guru.data');
    Route::get('/monitoring/presensi/guru', [MonitoringPresensiGuruController::class, 'index'])->name('monitoring.presensi_guru');
    Route::controller(JabatanController::class)->group(function () {
        Route::get('jabatan/data', 'data')->name('jabatan.data');
        Route::get('jabatan', 'index')->name('jabatan.index');
        Route::get('jabatan/create', 'create')->name('jabatan.create');
        Route::post('/jabatan/import-excel', 'importEXCEL')->name('jabatan.import_excel');
        Route::post('jabatan', 'store')->name('jabatan.store');
        Route::get('jabatan/{id}', 'show')->name('jabatan.show');
        Route::get('jabatan/{id}/edit', 'edit')->name('jabatan.edit');
        Route::put('jabatan/{id}', 'update')->name('jabatan.update');
        Route::delete('jabatan/{id}', 'destroy')->name('jabatan.destroy');
    });
    Route::get('/laporan/presensi-guru', [LaporanPresensiGuruController::class, 'index'])->name('laporan.presensi_guru');
    Route::get('/laporan/presensi-guru/pdf', [LaporanPresensiGuruController::class, 'pdf']);
    Route::get('/laporan/presensi-guru/excel', [LaporanPresensiGuruController::class, 'excel']);

    // Konfigurasi Lokasi Kantor
    Route::controller(KonfigurasiLokasiController::class)->group(function () {
        Route::get('lokasi/kantor/data', 'data')->name('kantor.data');
        Route::get('lokasi/kantor', 'index')->name('kantor.index');
        Route::get('lokasi/kantor/create', 'create')->name('kantor.create');
        Route::post('lokasi/kantor', 'store')->name('kantor.store');
        Route::get('lokasi/kantor/{id}', 'show')->name('kantor.show');
        Route::get('lokasi/kantor/{id}/edit', 'edit')->name('kantor.edit');
        Route::put('lokasi/kantor/{id}', 'update')->name('kantor.update');
        Route::delete('lokasi/kantor/{id}', 'destroy')->name('kantor.destroy');
    });

    // Persetujuan Izin Sakit Guru
    Route::controller(PersetujuanIzinGuruController::class)->group(function () {
        Route::get('persetujuan/izin-guru', 'index')->name('persetujuan.index');
        Route::get('persetujuan/izin-guru/data', 'data')->name('persetujuan.data');
        Route::get('persetujuan/izin-guru/{id}', 'show')->name('persetujuan.show');
        Route::get('persetujuan/izin-guru/{id}/alasan', 'alasan')->name('persetujuan.alasan');
        Route::post('/persetujuan/izin-guru/{id}/approve', 'approve')->name('persetujuan.setujui');
        Route::post('/persetujuan/izin-guru/{id}/batal', 'batalApprove');
        // Route::post('persetujuan/izin-guru/{id}/setujui', 'setujui')->name('persetujuan.setujui');
        Route::post('persetujuan/izin-guru/{id}/tolak', 'tolak')->name('persetujuan.tolak');
    });
});
