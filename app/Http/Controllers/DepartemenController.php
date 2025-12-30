<?php

namespace App\Http\Controllers;

use App\Models\Departemen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DepartemenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('departemen.index');
    }

    public function data()
    {
        $query = Departemen::all();

        return datatables($query)
            ->addIndexColumn()
            ->addColumn('action', function ($q) {
                return '
            <button onclick="editForm(`' . route('departemen.show', $q->id) . '`)" class="btn btn-sm" style="background-color:#6755a5; color:#fff;" title="Edit">
                <i class="fa fa-pencil-alt"></i>
            </button>
            <button onclick="deleteData(`' . route('departemen.destroy', $q->id) . '`,`' . $q->nama_dept . '`)" class="btn btn-sm" style="background-color:#d81b60; color:#fff;" title="Delete">
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
            'kode_dept'      => 'required',
            'nama_dept'    => 'required',
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
            Departemen::create([
                'kode_dept'        => $request->kode_dept,
                'nama_dept'        => $request->nama_dept,
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
        $query = Departemen::findOrfail($id);
        return response()->json(['data' => $query]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Departemen $departemen)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'kode_dept'      => 'required',
            'nama_dept'    => 'required',
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
                'kode_dept' => 'required',
                'nama_dept' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => 'error',
                    'errors'  => $validator->errors(),
                    'message' => 'Maaf, inputan tidak valid.'
                ], 422);
            }

            $departemen = Departemen::findOrFail($id);

            // Cegah duplikasi kode_dept
            $cek = Departemen::where('kode_dept', $request->kode_dept)
                ->where('id', '!=', $id)
                ->exists();

            if ($cek) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Kode departemen sudah digunakan.'
                ], 422);
            }

            $departemen->update([
                'kode_dept' => strtoupper(trim($request->kode_dept)),
                'nama_dept' => trim($request->nama_dept),
            ]);

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Departemen berhasil diperbarui.'
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
        $departemen = Departemen::findOrfail($id);
        $departemen->delete();
        return response()->json([
            'status'  => 'success',
            'message' => 'Data berhasil dihapus.'
        ]);
    }
}
