<?php

namespace App\Http\Controllers;

use App\Models\Departemen;
use App\Models\KonfigurasiLokasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class KonfigurasiLokasiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $departemen = Departemen::all();
        return view('lokasikantor.index', compact('departemen'));
    }

    public function data()
    {
        $query = KonfigurasiLokasi::with('departemen')->get();
        return datatables($query)
            ->addIndexColumn()
            ->addColumn('nama_dept', function ($q) {
                return '<span class="badge badge-info">'
                    . ($q->departemen->nama_dept ?? '-')
                    . '</span>';
            })
            ->addColumn('action', function ($q) {
                return '
                    <button onclick="showMap(`' . route('kantor.show', $q->id) . '`)" class="btn btn-sm btn-info" title="Lihat Lokasi">
                        <i class="fa fa-map-marker-alt"></i>
                    </button>

                    <button onclick="editForm(`' . route('kantor.show', $q->id) . '`)" class="btn btn-sm" style="background-color:#6755a5; color:#fff;" title="Edit">
                        <i class="fa fa-pencil-alt"></i>
                    </button>
                    <button onclick="deleteData(`' . route('kantor.destroy', $q->id) . '`,`' . $q->departemen->nama_dept . '`)" class="btn btn-sm" style="background-color:#d81b60; color:#fff;" title="Delete">
                        <i class="fa fa-trash"></i>
                    </button>
                    ';
            })
            ->escapeColumns([])
            ->make(true);
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
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'departemen_id' => [
                'required',
                Rule::unique('konfigurasi_lokasis', 'departemen_id')
            ],
            'latitude'  => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius'    => 'required|integer|min:1',
        ], [
            'departemen_id.unique' => 'Departemen ini sudah memiliki konfigurasi lokasi.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'errors'  => $validator->errors(),
                'message' => 'Maaf, inputan tidak valid.'
            ], 422);
        }

        DB::beginTransaction();
        try {
            // 2️⃣ Simpan data guru
            KonfigurasiLokasi::create([
                'departemen_id' => $request->departemen_id,
                'lokasi_kantor' => $request->latitude . ', ' . $request->longitude,
                'radius'        => $request->radius,
            ]);

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Data berhasil disimpan.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal menyimpan data.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $kantor = KonfigurasiLokasi::findOrfail($id);
        $kantor['latitude']  = explode(',', $kantor['lokasi_kantor'])[0];
        $kantor['longitude'] = explode(',', $kantor['lokasi_kantor'])[1];
        return response()->json([
            'data' => $kantor->load('departemen')
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // 🔍 ambil data
        $konfigurasi = KonfigurasiLokasi::find($id);

        if (!$konfigurasi) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data konfigurasi tidak ditemukan.'
            ], 404);
        }

        // ✅ validasi
        $validator = Validator::make($request->all(), [
            'departemen_id' => [
                'required',
                Rule::unique('konfigurasi_lokasis', 'departemen_id')->ignore($id)
            ],
            'latitude'  => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius'    => 'required|integer|min:1',
        ], [
            'departemen_id.unique' => 'Departemen ini sudah memiliki konfigurasi lokasi.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'errors'  => $validator->errors(),
                'message' => 'Maaf, inputan tidak valid.'
            ], 422);
        }

        DB::beginTransaction();
        try {
            // 📍 gabung lokasi
            $lokasiKantor = "{$request->latitude}, {$request->longitude}";

            // 💾 update data
            $konfigurasi->update([
                'departemen_id' => $request->departemen_id,
                'lokasi_kantor' => $lokasiKantor,
                'radius'        => $request->radius,
            ]);

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Data berhasil diperbarui.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal memperbarui data.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $konfigurasi = KonfigurasiLokasi::find($id);

        if (!$konfigurasi) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data konfigurasi tidak ditemukan.'
            ], 404);
        }

        DB::beginTransaction();
        try {
            $konfigurasi->delete();

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Data berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal menghapus data.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
