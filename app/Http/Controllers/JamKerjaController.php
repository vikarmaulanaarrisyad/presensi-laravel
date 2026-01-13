<?php

namespace App\Http\Controllers;

use App\Models\JamKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class JamKerjaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('jamkerja.index');
    }

    public function data()
    {
        $query = JamKerja::orderBy('id', 'DESC');

        return datatables($query)
            ->addIndexColumn()
            ->addColumn('action', function ($q) {
                return '
                    <button onclick="editForm(`' . route('jamkerja.show', $q->id) . '`)" class="btn btn-sm" style="background-color:#6755a5; color:#fff;" title="Edit">
                        <i class="fa fa-pencil-alt"></i>
                    </button>
                    <button onclick="deleteData(`' . route('jamkerja.destroy', $q->id) . '`,`' . $q->nama_jam_kerja . '`)" class="btn btn-sm" style="background-color:#d81b60; color:#fff;" title="Delete">
                        <i class="fa fa-trash"></i>
                    </button>
                ';
            })
            ->escapeColumns([])
            ->make(true);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_jam_kerja'   => 'required',
            'nama_jam_kerja'   => 'required',
            'jam_masuk'        => 'required',
            'jam_pulang'       => 'required|after:jam_masuk',
            'awal_jam_masuk'   => 'nullable',
            'akhir_jam_masuk'  => 'nullable',
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
            // Cegah duplikasi kode_jam_kerja
            $cek = JamKerja::where('kode_jam_kerja', $request->kode_jam_kerja)
                ->exists();

            if ($cek) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Kode jam kerja sudah digunakan.'
                ], 422);
            }

            JamKerja::create([
                'kode_jam_kerja'  => $request->kode_jam_kerja,
                'nama_jam_kerja'  => $request->nama_jam_kerja,
                'awal_jam_masuk'  => $request->awal_jam_masuk,
                'jam_masuk'       => $request->jam_masuk,
                'akhir_jam_masuk' => $request->akhir_jam_masuk,
                'jam_pulang'      => $request->jam_pulang,
            ]);

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Data jam kerja berhasil disimpan.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal menyimpan data jam kerja.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $data = $this->getDataById($id);

        return response(['data' => $data]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'kode_jam_kerja'   => 'required',
            'nama_jam_kerja'   => 'required',
            'jam_masuk'        => 'required',
            'jam_pulang'       => 'required|after:jam_masuk',
            'awal_jam_masuk'   => 'nullable',
            'akhir_jam_masuk'  => 'nullable',
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

            $data = $this->getDataById($id);

            // Cegah duplikasi kode_jam_kerja
            $cek = JamKerja::where('kode_jam_kerja', $request->kode_jam_kerja)
                ->where('id', '!=', $id)
                ->exists();

            if ($cek) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Kode jam kerja sudah digunakan.'
                ], 422);
            }

            $data->update([
                'kode_jam_kerja' => $request->kode_jam_kerja,
                'nama_jam_kerja' => $request->nama_jam_kerja,
                'jam_masuk' => $request->jam_masuk,
                'jam_pulang' => $request->jam_pulang,
                'awal_jam_masuk' => $request->awal_jam_masuk,
                'akhir_jam_masuk' => $request->akhir_jam_masuk,
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
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data = $this->getDataById($id);
        $data->delete();
        return response()->json([
            'status'  => 'success',
            'message' => 'Data berhasil dihapus.'
        ]);
    }

    private function getDataById($id)
    {
        $query = JamKerja::findOrfail($id);

        return $query;
    }
}
