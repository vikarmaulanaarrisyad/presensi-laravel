@extends('layouts.presensi.app')

@section('header')
    <div class="appHeader bg-primary text-light">
        <div class="left">
            <a href="javascript:;" class="headerButton goBack">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </a>
        </div>
        <div class="pageTitle">Pengajuan Izin & Sakit</div>
        <div class="right"></div>
    </div>
@endsection

@section('content')
    <div class="row" style="margin-top:70px">
        <div class="col">

            @if ($data->count() > 0)
                <ul class="listview image-listview">

                    @foreach ($data as $d)
                        <li>
                            <div class="item">

                                {{-- ICON STATUS --}}
                                <div
                                    class="icon-box
                                {{ $d->status_approved == '1' ? 'bg-success' : ($d->status_approved == '2' ? 'bg-danger' : 'bg-warning') }}">
                                    <ion-icon name="{{ $d->status == '1' ? 'walk-outline' : 'medkit-outline' }}">
                                    </ion-icon>
                                </div>

                                {{-- CONTENT --}}
                                <div class="in">
                                    <div>
                                        <b>
                                            {{ \Carbon\Carbon::parse($d->tgl_presensi)->translatedFormat('d F Y') }}
                                        </b>

                                        <div class="text-muted" style="font-size:12px">
                                            {{ $d->status == '1' ? 'Izin' : 'Sakit' }}
                                            • Kode: {{ $d->kode_izin }}
                                        </div>

                                        @if ($d->keterangan)
                                            <div style="font-size:12px" class="mt-1">
                                                <strong>Keterangan:</strong> {{ $d->keterangan }}
                                            </div>
                                        @endif

                                        @if ($d->status_approved == '2')
                                            <div class="text-danger mt-1" style="font-size:12px">
                                                <strong>Alasan Ditolak:</strong> {{ $d->alasan }}
                                            </div>
                                        @endif
                                    </div>

                                    {{-- BADGE STATUS --}}
                                    @if ($d->status_approved == '0')
                                        <span class="badge bg-warning">Menunggu</span>
                                    @elseif ($d->status_approved == '1')
                                        <span class="badge bg-success">Disetujui</span>
                                    @else
                                        <span class="badge bg-danger">Ditolak</span>
                                    @endif
                                </div>
                            </div>
                        </li>
                    @endforeach

                </ul>
            @else
                {{-- EMPTY STATE --}}
                <div class="alert alert-outline-warning text-center mt-3">
                    <ion-icon name="document-text-outline" style="font-size:40px"></ion-icon>
                    <h4 class="mt-1">Belum Ada Pengajuan</h4>
                    <p class="mb-0">
                        Pengajuan izin dan sakit Anda akan tampil di sini
                    </p>
                </div>
            @endif

        </div>
    </div>

    {{-- FLOATING ACTION BUTTON --}}
    <div class="fab-button bottom-right" style="margin-bottom:70px;">
        <a href="{{ route('pengajuan.izin.create') }}" class="fab">
            <ion-icon name="add-outline"></ion-icon>
        </a>
    </div>
@endsection
