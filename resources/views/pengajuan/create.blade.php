@extends('layouts.presensi.app')

@section('header')
    <div class="appHeader bg-primary text-light">
        <div class="left">
            <a href="javascript:;" class="headerButton goBack">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </a>
        </div>
        <div class="pageTitle">Form Pengajuan Izin</div>
        <div class="right"></div>
    </div>

    {{-- Flatpickr CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endsection

@section('content')
    <div class="row mt-3" style="padding-top:60px">
        <div class="col">
            <div class="card">
                <div class="card-body">

                    <form id="formIzin" action="{{ route('pengajuan.izin.store') }}" method="POST">
                        @csrf

                        {{-- Tanggal Izin --}}
                        <div class="form-group">
                            <label class="form-label">Tanggal Izin</label>
                            <input type="text" id="tanggal" name="tanggal" class="form-control"
                                placeholder="Pilih Tanggal" readonly>
                        </div>

                        {{-- Jenis Izin --}}
                        <div class="form-group mt-2">
                            <label class="form-label">Jenis Pengajuan</label>
                            <select name="jenis_izin" class="form-control">
                                <option value="">-- Pilih --</option>
                                <option value="izin">Izin</option>
                                <option value="sakit">Sakit</option>
                            </select>
                        </div>

                        {{-- Keterangan --}}
                        <div class="form-group mt-2">
                            <label class="form-label">Keterangan</label>
                            <textarea name="keterangan" class="form-control" rows="3" placeholder="Tuliskan alasan izin / sakit"></textarea>
                        </div>

                        {{-- Tombol Submit --}}
                        <div class="form-group mt-3">
                            <button type="submit" class="btn btn-primary btn-block">
                                <ion-icon name="send-outline"></ion-icon>
                                Kirim Pengajuan
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- Flatpickr --}}
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>

    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Datepicker
        flatpickr("#tanggal", {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "d F Y",
            locale: "id",
            minDate: "today", // mulai dari hari ini
            maxDate: new Date().fp_incr(2), // maksimal 2 hari ke depan
            disableMobile: true
        });


        // AJAX Submit
        $('#formIzin').on('submit', function(e) {
            e.preventDefault();

            const form = $(this);

            const tanggal = $('#tanggal').val();
            const jenisIzin = $('select[name="jenis_izin"]').val();
            const keterangan = $('textarea[name="keterangan"]').val();

            // Validasi
            if (!tanggal || !jenisIzin || !keterangan) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Lengkapi Data',
                    text: 'Semua field wajib diisi'
                });
                return;
            }

            Swal.fire({
                title: 'Kirim Pengajuan?',
                text: 'Pastikan data sudah benar',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Kirim',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {

                    Swal.fire({
                        title: 'Mengirim...',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });

                    $.ajax({
                        url: form.attr('action'),
                        type: 'POST',
                        data: form.serialize(),
                        success: function() {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: 'Pengajuan izin berhasil dikirim'
                            }).then(() => {
                                window.location.href =
                                    "{{ route('pengajuan.izin.index') }}";
                            });
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Terjadi kesalahan, silakan coba lagi'
                            });
                        }
                    });
                }
            });
        });
    </script>
@endpush
