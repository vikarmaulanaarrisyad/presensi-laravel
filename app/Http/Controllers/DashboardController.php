<?php

namespace App\Http\Controllers;

use App\Models\PengajuanIzin;
use App\Models\Presensi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $user = Auth::user();
        $tglSekarang = Carbon::today();

        $bulanIni = $tglSekarang->month;
        $tahunIni = $tglSekarang->year;

        $namaBulan = [
            "",
            "Januari",
            "Februari",
            "Maret",
            "April",
            "Mei",
            "Juni",
            "Juli",
            "Agustus",
            "September",
            "Oktober",
            "November",
            "Desember"
        ];

        $namaBulanIni = $namaBulan[$bulanIni];

        // Presensi hari ini
        $presensiHariIni = Presensi::where('user_id', $userId)
            ->whereDate('tgl_presensi', $tglSekarang)
            ->first();

        // History bulan ini
        $presensiBulanIni = Presensi::where('user_id', $userId)
            ->whereMonth('tgl_presensi', $bulanIni)
            ->whereYear('tgl_presensi', $tahunIni)
            ->orderBy('tgl_presensi', 'desc')
            ->get();

        // Jumlah presensi bulan ini
        $jumlahPresensi = Presensi::where('user_id', $userId)
            ->whereMonth('tgl_presensi', $bulanIni)
            ->whereYear('tgl_presensi', $tahunIni)
            ->count();

        $rekapIzin = PengajuanIzin::where('user_id', $userId)
            ->whereMonth('tgl_presensi', $bulanIni)
            ->whereYear('tgl_presensi', $tahunIni)
            ->where('status_approved', '1')
            ->selectRaw('
            SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as jumlah_sakit,
             SUM(CASE WHEN status = 2 THEN 1 ELSE 0 END) as jumlah_izin
        ')->first();

        $jumlahSakit = $rekapIzin->jumlah_sakit;
        $jumlahIzin  = $rekapIzin->jumlah_izin;

        // Jumlah TERLAMBAT bulan ini (jam_in > 07:00)
        $jumlahTerlambat = Presensi::where('user_id', $userId)
            ->whereMonth('tgl_presensi', $bulanIni)
            ->whereYear('tgl_presensi', $tahunIni)
            ->whereNotNull('jam_in')
            ->whereTime('jam_in', '>', '07:00:00')
            ->count();

        if ($user->hasRole('admin')) {
            return view('dashboard.admin.index', compact(
                'jumlahPresensi',
                'jumlahTerlambat',
                'jumlahSakit',
                'jumlahIzin'
            ));
        }

        return view('dashboard.user.index', compact(
            'presensiHariIni',
            'presensiBulanIni',
            'bulanIni',
            'tahunIni',
            'namaBulanIni',
            'jumlahPresensi',
            'jumlahTerlambat',
            'jumlahSakit',
            'jumlahIzin'
        ));
    }
}
