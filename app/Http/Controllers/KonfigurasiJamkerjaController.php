<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\JamKerja;
use App\Models\KonfigurasiJamkerja;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class KonfigurasiJamkerjaController extends Controller
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
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $guruId)
    {
        $validator = Validator::make($request->all(), [
            'hari' => 'required|array',
            'jam_kerja_id' => 'nullable|array',
            'libur' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            foreach ($request->hari as $hari) {

                $isLibur = isset($request->libur[$hari]);

                KonfigurasiJamkerja::updateOrCreate(
                    // 🔑 key pencarian
                    [
                        'user_id' => $guruId,
                        'hari' => $hari,
                    ],
                    // ✏️ data update / insert
                    [
                        'libur' => $isLibur,
                        'jam_kerja_id' => $isLibur
                            ? null
                            : ($request->jam_kerja_id[$hari] ?? null),
                    ]
                );
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Konfigurasi jam kerja berhasil disimpan'
            ]);
        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(KonfigurasiJamkerja $konfigurasiJamkerja)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $guru = Guru::with('user')->where('id', $id)->first();
        $jamKerja = JamKerja::all();
        return view('jamkerja.setjamkerja', compact('guru', 'jamKerja'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, KonfigurasiJamkerja $konfigurasiJamkerja)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(KonfigurasiJamkerja $konfigurasiJamkerja)
    {
        //
    }
}
