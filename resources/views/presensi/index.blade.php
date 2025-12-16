@extends('layouts.presensi.app')

@section('header')
    <div class="appHeader bg-primary text-light">
        <div class="left">
            <a href="javascript:;" class="headerButton goBack">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </a>
        </div>
        <div class="pageTitle">E-Presensi</div>
        <div class="right"></div>
    </div>
@endsection

@section('content')
    <div class="row" style="margin-top:70px">
        <div class="col">
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <select name="bulan" id="bulan" class="form-control">
                            <option value="" disabled selected>Bulan</option>
                            @for ($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ date('m') == $i ? 'selected' : '' }}>
                                    {{ $namaBulan[$i] }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <select name="tahun" id="tahun" class="form-control">
                            @php
                                $tahunMulai = 2022;
                                $tahunSekarang = date('Y');
                            @endphp
                            <option value="" disabled selected>Tahun</option>
                            @for ($tahun = $tahunMulai; $tahun <= $tahunSekarang; $tahun++)
                                <option value="{{ $tahun }}" {{ date('Y') == $tahun ? 'selected' : '' }}>
                                    {{ $tahun }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <button class="btn btn-primary btn-block" onclick="search()">
                            <ion-icon name="search-outline"></ion-icon>
                            Search
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col" id="result-presensi"></div>
    </div>
@endsection

@push('scripts')
    <script>
        function search() {
            let bulan = $('#bulan').val();
            let tahun = $('#tahun').val();

            if (!bulan || !tahun) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian',
                    text: 'Bulan dan tahun harus dipilih'
                });
                return;
            }

            Swal.fire({
                title: 'Memuat data',
                text: 'Mohon tunggu...',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: "{{ route('presensi.search') }}",
                type: "POST",
                data: {
                    bulan: bulan,
                    tahun: tahun
                },
                success: function(response) {
                    Swal.close(); // tutup loading
                    $('#result-presensi').html(response);
                },
                error: function() {
                    Swal.close();
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Gagal mengambil data presensi'
                    });
                }
            });
        }
    </script>
@endpush
