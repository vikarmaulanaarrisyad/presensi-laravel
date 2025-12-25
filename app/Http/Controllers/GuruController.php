<?php

namespace App\Http\Controllers;

use App\Imports\GuruImport;
use App\Models\Guru;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class GuruController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('guru.index');
    }

    public function data()
    {
        $query = Guru::all();
        return datatables($query)
            ->addIndexColumn()
            ->editColumn('nama_guru', function ($q) {
                $nama = trim(
                    ($q->gelar_depan ? $q->gelar_depan . ' ' : '') .
                        $q->nama_guru .
                        ($q->gelar_belakang ? ', ' . $q->gelar_belakang : '')
                );

                return $nama;
            })
            ->editColumn('ttl', function ($q) {
                return $q->tempat_lahir . ', ' . tanggal_indonesia($q->tgl_lahir);
            })
            ->editColumn('tgl_tmt', function ($q) {
                return tanggal_indonesia($q->tgl_tmt);
            })
            ->addColumn('action', function ($q) {
                return '
            <button onclick="editForm(`' . route('guru.show', $q->id) . '`)" class="btn btn-sm" style="background-color:#6755a5; color:#fff;" title="Edit">
                <i class="fa fa-pencil-alt"></i>
            </button>
            <button onclick="deleteData(`' . route('guru.destroy', $q->id) . '`,`' . $q->phase_name . '`)" class="btn btn-sm" style="background-color:#d81b60; color:#fff;" title="Delete">
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
            'nama_guru'      => 'required|string|max:255',
            'gelar_depan'    => 'nullable|string|max:50',
            'gelar_belakang' => 'nullable|string|max:50',
            'jenis_kelamin'  => 'required|in:L,P',
            'tempat_lahir'   => 'required|string|max:255',
            'no_hp'          => 'required|string|max:20|unique:users,username',
            'tgl_lahir'      => 'required|date',
            'tgl_tmt'        => 'required|date',
            'email' => 'required|email|unique:users,email',
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
            // 1️⃣ Buat akun user
            $user = User::create([
                'name'     => $request->nama_guru,
                'username' => $request->no_hp,
                'password' => Hash::make('guru123'), // password default
                'email'    => $request->email,
            ]);

            $user->assignRole('guru');

            // 2️⃣ Simpan data guru
            Guru::create([
                'user_id'        => $user->id, // jika pakai relasi
                'nama_guru'      => $request->nama_guru,
                'gelar_depan'    => $request->gelar_depan,
                'gelar_belakang' => $request->gelar_belakang,
                'jenis_kelamin'  => $request->jenis_kelamin,
                'tempat_lahir'   => $request->tempat_lahir,
                'no_hp'          => $request->no_hp,
                'tgl_lahir'      => $request->tgl_lahir,
                'tgl_tmt'        => $request->tgl_tmt,
            ]);

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Data guru & akun berhasil dibuat.'
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
        $guru = Guru::with('user')->findOrFail($id);
        $guru->email = $guru->email;
        return response()->json(['data' => $guru]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Guru $guru)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $guru = Guru::findOrfail($id);
        $validator = Validator::make($request->all(), [
            'nama_guru'      => 'required|string|max:255',
            'email'          => 'required|email|unique:users,email,' . $guru->user_id,
            'gelar_depan'    => 'nullable|string|max:50',
            'gelar_belakang' => 'nullable|string|max:50',
            'jenis_kelamin'  => 'required|in:L,P',
            'tempat_lahir'   => 'required|string|max:255',
            'no_hp'          => 'required|string|max:20|unique:users,username,' . $guru->user_id,
            'tgl_lahir'      => 'required|date',
            'tgl_tmt'        => 'required|date',
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
            // 1️⃣ Update akun user
            $user = User::findOrFail($guru->user_id);
            $user->update([
                'name'     => $request->nama_guru,
                'email'    => $request->email,
                'username' => $request->no_hp,
            ]);

            // 2️⃣ Update data guru
            $guru->update([
                'nama_guru'      => $request->nama_guru,
                'gelar_depan'    => $request->gelar_depan,
                'gelar_belakang' => $request->gelar_belakang,
                'jenis_kelamin'  => $request->jenis_kelamin,
                'tempat_lahir'   => $request->tempat_lahir,
                'no_hp'          => $request->no_hp,
                'tgl_lahir'      => $request->tgl_lahir,
                'tgl_tmt'        => $request->tgl_tmt,
            ]);

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Data guru berhasil diperbarui.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal memperbarui data.',
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $guru = Guru::findOrfail($id);
            // Ambil akun user (jika ada)
            $user = $guru->user;

            // Hapus data guru
            $guru->delete();

            // Hapus akun user
            if ($user) {
                $user->delete();
            }

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Data guru dan akun berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal menghapus data.'
            ], 500);
        }
    }

    public function importEXCEL(Request $request)
    {
        // Validasi file
        $validator = Validator::make($request->all(), [
            'excelFile' => 'required|file|mimes:xlsx,xls|max:2048', // Maks 2MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            // Proses import menggunakan Laravel Excel
            Excel::import(new GuruImport, $request->file('excelFile'), null, \Maatwebsite\Excel\Excel::XLSX);

            return response()->json([
                'status' => 'success',
                'message' => 'File berhasil diupload dan diproses!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
