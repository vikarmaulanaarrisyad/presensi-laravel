<?php

namespace App\Http\Controllers;

use App\Models\Jabatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class JabatanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('jabatan.index');
    }

    public function data()
    {
        $query = Jabatan::all();
        return datatables($query)
            ->addIndexColumn()
            ->addColumn('action', function ($q) {
                return '
                    <button onclick="editForm(`' . route('jabatan.show', $q->id) . '`)" class="btn btn-sm" style="background-color:#6755a5; color:#fff;" title="Edit">
                        <i class="fa fa-pencil-alt"></i>
                    </button>
                    <button onclick="deleteData(`' . route('jabatan.destroy', $q->id) . '`,`' . $q->nama_jab . '`)" class="btn btn-sm" style="background-color:#d81b60; color:#fff;" title="Delete">
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
            'nama_jab'    => 'required',
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
            Jabatan::create([
                'nama_jab'        => $request->nama_jab,
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
        $query = Jabatan::findOrfail($id);
        return response()->json(['data' => $query]);
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
        $validator = Validator::make($request->all(), [
            'nama_jab'    => 'required',
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
            $validator = Validator::make($request->all(), [
                'nama_jab' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => 'error',
                    'errors'  => $validator->errors(),
                    'message' => 'Maaf, inputan tidak valid.'
                ], 422);
            }

            $jabatan = Jabatan::findOrFail($id);

            // Cegah duplikasi kode_dept
            $cek = Jabatan::where('nama_jab', $request->nama_jab)
                ->where('id', '!=', $id)
                ->exists();

            if ($cek) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Jabatan sudah digunakan.'
                ], 422);
            }

            $jabatan->update([
                'nama_jab' => trim($request->nama_jab),
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
        $query = Jabatan::findOrfail($id);
        $query->delete();
        return response()->json([
            'status'  => 'success',
            'message' => 'Data berhasil dihapus.'
        ]);
    }
}
