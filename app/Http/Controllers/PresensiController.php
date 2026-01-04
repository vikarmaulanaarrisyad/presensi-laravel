<?php

namespace App\Http\Controllers;

use App\Models\KonfigurasiLokasi;
use App\Models\Presensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PresensiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
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

        return view('presensi.index', compact(
            'namaBulan',
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $hariIni = date('Y-m-d');
        $user = Auth::user()->load('guru');
        $cekPresensi = Presensi::where('tgl_presensi', $hariIni)->where('user_id', $user->id)->count();

        $guru = $user->guru;

        if (!$guru) {
            return response()->json([
                'status' => 'error',
                'message' => 'Akun belum terhubung dengan data guru'
            ], 422);
        }

        $konfigurasi = KonfigurasiLokasi::where('departemen_id', $guru->departemen_id)->first();

        if (!$konfigurasi) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lokasi kantor belum dikonfigurasi'
            ], 422);
        }

        // 🔹 pecah lokasi kantor
        [$latKantor, $lngKantor] = array_map(
            'floatval',
            explode(',', $konfigurasi->lokasi_kantor)
        );

        return view('presensi.create', compact(
            'cekPresensi',
            'konfigurasi',
            'latKantor',
            'lngKantor',
            'guru'
        ));
    }

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
        $user = Auth::user()->load('guru');
        $guru = $user->guru;

        if (!$guru) {
            return response()->json([
                'status' => 'error',
                'message' => 'Akun belum terhubung dengan data guru'
            ], 422);
        }

        $userId      = $user->id;
        $tglPresensi = date('Y-m-d');
        $jamSekarang = date('H:i:s');

        // =====================
        // KONFIGURASI LOKASI
        // =====================
        $konfigurasi = KonfigurasiLokasi::where('departemen_id', $guru->departemen_id)->first();

        if (!$konfigurasi) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lokasi kantor belum dikonfigurasi'
            ], 422);
        }

        // =====================
        // PARSE LOKASI
        // =====================
        [$latKantor, $lngKantor] = array_map(
            'floatval',
            explode(',', $konfigurasi->lokasi_kantor)
        );

        [$latUser, $lngUser] = array_map(
            'floatval',
            explode(',', $request->lokasi)
        );

        // =====================
        // HITUNG JARAK
        // =====================
        $jarak = $this->distance(
            $latKantor,
            $lngKantor,
            $latUser,
            $lngUser
        );

        $jarakMeter = round($jarak['meters']);
        $radiusMax  = $konfigurasi->radius;

        if ($jarakMeter > $radiusMax) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Anda berada di luar radius absensi',
                'jarak'   => $jarakMeter . ' meter'
            ], 403);
        }

        // =====================
        // CEK PRESENSI HARI INI
        // =====================
        $presensi = Presensi::where('user_id', $userId)
            ->where('tgl_presensi', $tglPresensi)
            ->first();

        // =====================
        // ABSENSI PULANG
        // =====================
        if ($presensi) {

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

    //Menghitung Jarak
    function distance($lat1, $lon1, $lat2, $lon2)
    {
        $theta = $lon1 - $lon2;
        $miles = (sin(deg2rad($lat1)) * sin(deg2rad($lat2))) + (cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)));
        $miles = acos($miles);
        $miles = rad2deg($miles);
        $miles = $miles * 60 * 1.1515;
        $feet = $miles * 5280;
        $yards = $feet / 3;
        $kilometers = $miles * 1.609344;
        $meters = $kilometers * 1000;
        return compact('meters');
    }

    public function search(Request $request)
    {
        // Validasi
        $request->validate([
            'bulan' => 'required|numeric|min:1|max:12',
            'tahun' => 'required|numeric|min:2000'
        ]);

        $userId = Auth::id();

        // Ambil data presensi
        $data = Presensi::where('user_id', $userId)
            ->whereMonth('tgl_presensi', $request->bulan)
            ->whereYear('tgl_presensi', $request->tahun)
            ->orderBy('tgl_presensi', 'asc')
            ->get();
        // Kembalikan ke view (AJAX)
        return view('presensi.result', compact('data'));
    }
}
