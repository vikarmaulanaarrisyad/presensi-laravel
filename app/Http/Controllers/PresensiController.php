<?php

namespace App\Http\Controllers;

use App\Models\Presensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PresensiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $hariIni = date('Y-m-d');
        $userId = Auth::user()->id;
        $cekPresensi = Presensi::where('tgl_presensi', $hariIni)->where('user_id', $userId)->count();
        return view('presensi.create', compact('cekPresensi'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // =====================
        // VALIDASI
        // =====================
        $validator = Validator::make($request->all(), [
            'lokasi' => 'required',
            'image'  => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'errors'  => $validator->errors(),
                'message' => 'Maaf, inputan tidak valid.'
            ], 422);
        }

        // =====================
        // DATA DASAR
        // =====================
        $userId       = Auth::id();
        $tglPresensi  = date('Y-m-d');
        $jamSekarang  = date('H:i:s');

        // =====================
        // CEK PRESENSI HARI INI
        // =====================
        $presensi = Presensi::where('user_id', $userId)
            ->where('tgl_presensi', $tglPresensi)
            ->first();

        // =====================
        // ABSENSI KELUAR
        // =====================
        if ($presensi) {

            // Cegah absen keluar dobel
            if ($presensi->jam_out) {
                return response()->json([
                    'message' => 'Anda sudah melakukan absensi pulang hari ini.'
                ], 400);
            }

            $fotoOut = uploadBase64(
                'uploads/absensi',
                $request->image,
                $userId . '_' . $tglPresensi . '_out'
            );

            $presensi->update([
                'jam_out'      => $jamSekarang,
                'location_out' => $request->lokasi,
                'foto_out'     => $fotoOut
            ]);

            return response()->json([
                'message' => 'Absensi pulang berhasil disimpan'
            ]);
        }

        // =====================
        // ABSENSI MASUK
        // =====================
        $fotoIn = uploadBase64(
            'uploads/absensi',
            $request->image,
            $userId . '_' . $tglPresensi . '_in'
        );

        Presensi::create([
            'user_id'      => $userId,
            'tgl_presensi' => $tglPresensi,
            'jam_in'       => $jamSekarang,
            'location_in'  => $request->lokasi,
            'foto_in'      => $fotoIn
        ]);

        return response()->json([
            'message' => 'Absensi masuk berhasil disimpan'
        ]);
    }



    /**
     * Display the specified resource.
     */
    public function show(Presensi $presensi)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Presensi $presensi)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Presensi $presensi)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Presensi $presensi)
    {
        //
    }
}
