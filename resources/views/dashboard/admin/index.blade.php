@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
    <div class="row">

        {{-- Guru Hadir --}}
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-success elevation-1">
                    <i class="fas fa-check-circle"></i>
                </span>

                <div class="info-box-content">
                    <span class="info-box-text">Guru Hadir</span>
                    <span class="info-box-number">
                        {{ $jumlahPresensi }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Guru Sakit --}}
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-danger elevation-1">
                    <i class="fas fa-procedures"></i>
                </span>

                <div class="info-box-content">
                    <span class="info-box-text">Guru Sakit</span>
                    <span class="info-box-number">
                        {{ $jumlahSakit != null ? $jumlahSakit : 0 }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Guru Izin --}}
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-warning elevation-1">
                    <i class="fas fa-envelope-open-text"></i>
                </span>

                <div class="info-box-content">
                    <span class="info-box-text">Guru Izin</span>
                    <span class="info-box-number">
                        {{ $jumlahIzin != null ? $jumlahIzin : 0 }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Guru Terlambat --}}
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-info elevation-1">
                    <i class="fas fa-clock"></i>
                </span>

                <div class="info-box-content">
                    <span class="info-box-text">Guru Terlambat</span>
                    <span class="info-box-number">
                        {{ $jumlahTerlambat != null ? $jumlahTerlambat : 0 }}
                    </span>
                </div>
            </div>
        </div>

    </div>
@endsection
