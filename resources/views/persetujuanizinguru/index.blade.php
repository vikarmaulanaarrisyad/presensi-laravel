@extends('layouts.app')

@section('title', 'Data Izin / Sakit')

@section('breadcrumb')
    @parent
    <li class="breadcrumb-item active">Manajemen Presensi</li>
    <li class="breadcrumb-item active">@yield('title')</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <x-card>
                <x-table>
                    <x-slot name="thead">
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Nama Guru</th>
                        <th>Departemen</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                        <th>Status Approve</th>
                        <th>Aksi</th>
                    </x-slot>
                </x-table>
            </x-card>
        </div>
    </div>

    <div class="modal fade" id="modalApprove" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Persetujuan Izin / Sakit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="izin_id">

                    <div class="mb-3">
                        <label class="form-label">Keputusan</label>
                        <select class="form-select form-control" id="status_approved">
                            <option value="">-- Pilih Keputusan --</option>
                            <option value="1">Setujui</option>
                            <option value="2">Tolak</option>
                        </select>
                    </div>

                    <div class="mb-3 d-none" id="alasanWrapper">
                        <label class="form-label">Alasan Penolakan</label>
                        <textarea class="form-control" id="alasan" rows="3" placeholder="Masukkan alasan penolakan secara profesional"></textarea>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-primary" onclick="submitApprove()">Simpan</button>
                </div>

            </div>
        </div>
    </div>


    {{--  <!-- Modal Alasan Penolakan -->
    <div class="modal fade" id="modalAlasan" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Alasan Penolakan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <p id="isiAlasan" class="mb-0 text-muted">
                        Memuat alasan...
                    </p>
                </div>

            </div>
        </div>
    </div>  --}}

@endsection

@include('includes.datatable')

@push('scripts')
    <script>
        function lihatAlasan(id) {
            $('#isiAlasan').text('Memuat alasan...');

            fetch(`/persetujuan/izin-guru/${id}/alasan`)
                .then(response => response.json())
                .then(data => {
                    $('#isiAlasan').text(data.alasan ?? '-');
                    $('#modalAlasan').modal('show');
                })
                .catch(() => {
                    $('#isiAlasan').text('Gagal memuat alasan');
                });
        }
    </script>

    <script>
        let table;
        let modal = '#modal-form';
        let importExcel = '#importExcelModal';
        let button = '#submitBtn';

        table = $('.table').DataTable({
            processing: false,
            serverSide: true,
            autoWidth: false,
            responsive: true,
            ajax: {
                url: '{{ route('persetujuan.data') }}',
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'tgl_presensi',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'nama_guru',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'departemen',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'status',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'keterangan',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'status_approved',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'action',
                    orderable: false,
                    searchable: false
                },
            ]
        })
    </script>

    <script>
        function openApproveModal(id) {
            $('#izin_id').val(id);
            $('#status_approved').val('');
            $('#alasan').val('');
            $('#alasanWrapper').addClass('d-none');

            $('#modalApprove').modal('show');
        }

        $('#status_approved').on('change', function() {
            if ($(this).val() === '2') {
                $('#alasanWrapper').removeClass('d-none');
            } else {
                $('#alasan').val('');
                $('#alasanWrapper').addClass('d-none');
            }
        });

        function submitApprove() {
            const id = $('#izin_id').val();
            const status = $('#status_approved').val();
            const alasan = $('#alasan').val();

            if (!status) {
                Swal.fire('Peringatan', 'Silakan pilih keputusan', 'warning');
                return;
            }

            if (status === '2' && alasan.trim() === '') {
                Swal.fire('Peringatan', 'Alasan penolakan wajib diisi', 'warning');
                return;
            }

            Swal.fire({
                title: 'Memproses...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            $.ajax({
                url: `/persetujuan/izin-guru/${id}/approve`,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    status_approved: status,
                    alasan: alasan
                },
                success: function(res) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false
                    });

                    $('#modalApprove').modal('hide');
                    table.ajax.reload();
                },
                error: function() {
                    Swal.fire('Error', 'Terjadi kesalahan', 'error');
                }
            });
        }

        function batalApprove(id) {
            Swal.fire({
                title: 'Batalkan Persetujuan?',
                text: 'Status akan dikembalikan ke pending',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Batalkan',
                cancelButtonText: 'Tidak',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {

                    Swal.fire({
                        title: 'Memproses...',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });

                    $.ajax({
                        url: `/persetujuan/izin-guru/${id}/batal`,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(res) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: res.message,
                                timer: 1500,
                                showConfirmButton: false
                            });

                            table.ajax.reload(null, false);
                        },
                        error: function() {
                            Swal.fire('Gagal', 'Tidak dapat membatalkan persetujuan', 'error');
                        }
                    });
                }
            });
        }
    </script>
@endpush
