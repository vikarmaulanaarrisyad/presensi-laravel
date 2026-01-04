@extends('layouts.app')

@section('title', 'Laporan Presensi Guru')

@section('content')
    <x-card>
        <x-slot name="header">
            <form method="GET">
                <div class="row g-2">
                    <div class="col-md-3">
                        <select name="tahun" class="form-control">
                            @for ($y = now()->year; $y >= 2020; $y--)
                                <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div class="col-md-3">
                        <select name="bulan" class="form-control">
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                                    {{ Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div class="col-md-4">
                        <select name="user_id" class="form-control">
                            <option value="">-- Semua Guru --</option>
                            @foreach ($users as $u)
                                <option value="{{ $u->id }}" {{ $userId == $u->id ? 'selected' : '' }}>
                                    {{ $u->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <button class="btn btn-primary w-100">
                            <i class="fa fa-search"></i> Tampilkan
                        </button>
                    </div>
                </div>
            </form>

            <div class="mt-3">
                <a href="{{ url('/laporan/presensi-guru/pdf?' . request()->getQueryString()) }}"
                    class="btn btn-danger btn-sm">
                    <i class="fa fa-file-pdf"></i> PDF
                </a>

                <a href="{{ url('/laporan/presensi-guru/excel?' . request()->getQueryString()) }}"
                    class="btn btn-success btn-sm">
                    <i class="fa fa-file-excel"></i> Excel
                </a>
            </div>
        </x-slot>

        <div class="table-responsive mt-3">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Nama Guru</th>
                        <th>Jam Masuk</th>
                        <th>Jam Pulang</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $i => $row)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ Carbon\Carbon::parse($row->tgl_presensi)->format('d-m-Y') }}</td>
                            <td>{{ $row->user->name }}</td>
                            <td>{{ $row->jam_in }}</td>
                            <td>{{ $row->jam_out }}</td>
                            <td>
                                @if (Carbon\Carbon::parse($row->jam_in)->lte(Carbon\Carbon::createFromTime(7, 0)))
                                    Tepat Waktu
                                @else
                                    Terlambat
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-card>
@endsection
