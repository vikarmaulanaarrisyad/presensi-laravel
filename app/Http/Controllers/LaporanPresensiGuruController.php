<?php

namespace App\Http\Controllers;

use App\Models\Presensi;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class LaporanPresensiGuruController extends Controller
{
    public function index(Request $request)
    {
        $tahun  = $request->tahun ?? now()->year;
        $bulan  = $request->bulan ?? now()->month;
        $userId = $request->user_id;

        $users = User::orderBy('name')->get();

        $data = Presensi::with('user')
            ->when($userId, fn($q) => $q->where('user_id', $userId))
            ->whereYear('tgl_presensi', $tahun)
            ->whereMonth('tgl_presensi', $bulan)
            ->orderBy('tgl_presensi')
            ->get();

        return view('laporan.presensi.guru.index', compact(
            'data',
            'users',
            'tahun',
            'bulan',
            'userId'
        ));
    }

    public function pdf(Request $request)
    {
        $data = $this->getData($request);

        $pdf = Pdf::loadView('laporan.presensi.guru.pdf', $data)
            ->setPaper('A4', 'landscape');

        return $pdf->stream('laporan.presensi.guru.pdf');
    }

    public function excel(Request $request)
    {
        // return Excel::download(
        //     new PresensiGuruExport($request),
        //     'laporan-presensi-guru.xlsx'
        // );
    }

    private function getData($request)
    {
        return [
            'data' => Presensi::with('user')
                ->when($request->user_id, fn($q) => $q->where('user_id', $request->user_id))
                ->whereYear('tgl_presensi', $request->tahun)
                ->whereMonth('tgl_presensi', $request->bulan)
                ->orderBy('tgl_presensi')
                ->get(),
            'bulan' => $request->bulan,
            'tahun' => $request->tahun,
            'user'  => User::find($request->user_id)
        ];
    }
}
