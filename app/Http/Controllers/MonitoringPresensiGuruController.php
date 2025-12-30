<?php

namespace App\Http\Controllers;

use App\Models\Presensi;
use App\Models\User;
use Illuminate\Http\Request;

class MonitoringPresensiGuruController extends Controller
{
    public function index(Request $request)
    {
        $end   = now()->format('Y-m-d');          // hari ini
        $start = now()->subDays(30)->format('Y-m-d'); // 30 hari ke belakang

        if ($request->filled('start') && $request->filled('end')) {
            $start = $request->start;
            $end   = $request->end;
        }
        $users = User::orderBy('name')->get();
        return view('monitoring.presensi.guru.index', compact('start', 'end', 'users'));
    }

    public function data(Request $request)
    {
        $start = $request->start;
        $end   = $request->end;
        $user_id = $request->user_id;

        $data = $this->getData($start, $end, $user_id);

        return datatables()
            ->of($data)
            ->addIndexColumn()
            ->addColumn(
                'tanggal',
                fn($row) =>
                \Carbon\Carbon::parse($row->tgl_presensi)->format('d-m-Y')
            )
            ->addColumn(
                'nama_guru',
                fn($row) =>
                $row->user->name ?? '-'
            )
            ->addColumn(
                'departemen',
                fn($row) =>
                $row->departemen ?? '-'
            )
            ->addColumn(
                'jam_in',
                fn($row) =>
                $row->jam_in ?? '-'
            )
            ->addColumn('foto_in', function ($row) {
                if ($row->foto_in) {
                    return '<img src="' . asset('storage/' . $row->foto_in) . '"
                     class="img-thumbnail"
                     width="70"
                     style="cursor:pointer"
                     onclick="previewImage(this.src)">';
                }

                return '-';
            })

            ->addColumn(
                'jam_out',
                fn($row) =>
                $row->jam_out ?? '-'
            )
            ->addColumn('foto_out', function ($row) {
                if ($row->foto_out) {
                    return '<img src="' . asset('storage/' . $row->foto_out) . '"
                     class="img-thumbnail"
                     width="70"
                     style="cursor:pointer"
                     onclick="previewImage(this.src)">';
                }

                return '-';
            })
            ->addColumn('keterangan', function ($row) {

                if (!$row->jam_in) {
                    return '-';
                }

                // batas maksimal absen
                $batas = \Carbon\Carbon::createFromTime(7, 0, 0);
                $jamIn = \Carbon\Carbon::parse($row->jam_in);

                if ($jamIn->greaterThan($batas)) {
                    return '<span class="badge bg-danger">Terlambat</span>';
                }

                return '<span class="badge bg-success">Tepat Waktu</span>';
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function getData($start, $end, $user_id = null)
    {
        return Presensi::with('user')
            ->when($user_id, function ($q) use ($user_id) {
                $q->where('user_id', $user_id); // 👈 FILTER GURU
            })
            ->whereBetween('tgl_presensi', [
                $start . ' 00:00:00',
                $end   . ' 23:59:59'
            ])
            ->orderBy('tgl_presensi', 'desc');
    }
}
