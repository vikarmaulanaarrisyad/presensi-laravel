<?php

namespace App\Http\Controllers;

use App\Models\PengajuanIzin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PengajuanIzinController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = PengajuanIzin::where('user_id', Auth::id())
            ->orderBy('tgl_presensi', 'desc')
            ->get();

        return view('pengajuan.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pengajuan.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'tanggal'     => 'required|date',
            'jenis_izin'  => 'required|in:izin,sakit',
            'keterangan'  => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        // Mapping jenis izin ke ENUM status
        $status = $request->jenis_izin === 'izin' ? '1' : '2';

        // Simpan data
        PengajuanIzin::create([
            'user_id'          => Auth::id(),
            'kode_izin'        => 'IZ-' . date('Ymd') . '-' . strtoupper(Str::random(4)),
            'tgl_presensi'     => $request->tanggal,
            'status'           => $status,
            'keterangan'       => $request->keterangan,
            'status_approved'  => '0', // pending
            'alasan'           => '-'
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Pengajuan izin berhasil dikirim'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(PengajuanIzin $pengajuanIzin)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PengajuanIzin $pengajuanIzin)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PengajuanIzin $pengajuanIzin)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PengajuanIzin $pengajuanIzin)
    {
        //
    }
}
