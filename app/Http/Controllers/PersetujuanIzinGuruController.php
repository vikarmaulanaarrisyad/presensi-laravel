<?php

namespace App\Http\Controllers;

use App\Models\PengajuanIzin;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PersetujuanIzinGuruController extends Controller
{
    public function index()
    {
        return view('persetujuanizinguru.index');
    }

    public function data()
    {
        $query = PengajuanIzin::query()
            ->select([
                'id',
                'user_id',
                'status',
                'tgl_presensi',
                'keterangan',
                'status_approved',
                'alasan'
            ])
            ->with([
                'user:id',
                'user.guru:id,user_id,nama_guru,departemen_id',
                'user.guru.departemen:id,nama_dept'
            ])->orderBy('id', 'desc');

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('tgl_presensi', function ($row) {
                return tanggal_indonesia($row->tgl_presensi);
            })
            ->addColumn('nama_guru', function ($row) {
                return $row->user->guru->nama_guru ?? '-';
            })
            ->addColumn('departemen', function ($row) {
                return $row->user->guru->departemen->nama_dept ?? '-';
            })
            ->editColumn('status', function ($row) {
                return match ((int) $row->status) {
                    1 => '<span class="badge bg-info">IZIN</span>',
                    2 => '<span class="badge bg-warning">SAKIT</span>',
                    default => '-',
                };
            })
            ->editColumn('keterangan', function ($row) {
                return $row->keterangan ?? '-';
            })
            ->addColumn('status_approved', function ($row) {
                return match ($row->status_approved) {
                    '0' => '<span class="badge bg-warning">Menunggu</span>',
                    '1' => '<span class="badge bg-success">Disetujui</span>',
                    default => '<span class="badge bg-danger">Ditolak</span>',
                };
            })
            ->addColumn('action', function ($row) {

                if ($row->status_approved === '1' || $row->status_approved === '2') {
                    return '
            <button class="btn btn-warning btn-sm"
                onclick="batalApprove(' . $row->id . ')">
                <i class="fa fa-undo"></i> Batal Approve
            </button>
        ';
                }

                return '
                <button class="btn btn-primary btn-sm"
                    onclick="openApproveModal(' . $row->id . ')">
                    <i class="fa fa-check"></i> Persetujuan
                </button>
            ';
            })

            ->rawColumns(['status_approved', 'action', 'status'])
            ->make(true);
    }

    public function detail()
    {
        //
    }

    public function approve(Request $request, $id)
    {
        $izin = PengajuanIzin::findOrFail($id);

        $izin->status_approved = $request->status_approved;
        $izin->alasan = $request->status_approved === '2'
            ? $request->alasan
            : null;

        $izin->save();

        return response()->json([
            'message' => $request->status_approved === '1'
                ? 'Permohonan berhasil disetujui'
                : 'Permohonan berhasil ditolak'
        ]);
    }

    public function batalApprove($id)
    {
        $izin = PengajuanIzin::findOrFail($id);

        $izin->status_approved = '0';
        $izin->alasan = null;

        $izin->save();

        return response()->json([
            'message' => 'Persetujuan berhasil dibatalkan'
        ]);
    }


    public function tolak(Request $request, $id)
    {
        //
    }

    public function alasan($id)
    {
        $izin = PengajuanIzin::query()
            ->select('id', 'alasan', 'status_approved')
            ->where('id', $id)
            ->firstOrFail();

        if ($izin->status_approved != 2) {
            return response()->json([
                'message' => 'Data tidak valid'
            ], 422);
        }

        return response()->json([
            'alasan' => $izin->alasan
        ]);
    }
}
