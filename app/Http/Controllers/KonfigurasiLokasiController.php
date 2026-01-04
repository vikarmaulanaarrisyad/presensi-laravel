<?php

namespace App\Http\Controllers;

use App\Models\Departemen;
use App\Models\KonfigurasiLokasi;
use Illuminate\Http\Request;

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
                    <button onclick="deleteData(`' . route('kantor.destroy', $q->id) . '`,`' . $q->nama_jab . '`)" class="btn btn-sm" style="background-color:#d81b60; color:#fff;" title="Delete">
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $kantor = KonfigurasiLokasi::findOrfail($id);
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
    }
}
