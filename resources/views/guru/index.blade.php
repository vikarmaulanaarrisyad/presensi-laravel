@extends('layouts.app')

@section('title', 'Data Guru')

@section('breadcrumb')
    @parent
    <li class="breadcrumb-item active">Manajemen Guru</li>
    <li class="breadcrumb-item active">@yield('title')</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-12 col-md-12">
            <x-card>
                <x-slot name="header">
                    <button onclick="addForm(`{{ route('guru.store') }}`)" class="btn btn-sm btn-info">
                        <i class="fas fa-plus-circle"></i>
                        Tambah Data
                    </button>

                    <button onclick="confirmImport()" type="button" class="btn btn-success btn-sm">
                        <i class="fas fa-file-excel"></i>
                        Import Excel
                    </button>
                </x-slot>

                <x-table>
                    <x-slot name="thead">
                        <th width="5%">NO</th>
                        <th>Nama Lengkap</th>
                        <th>Nama Departemen</th>
                        <th>Jabatan</th>
                        <th>L/P</th>
                        <th>TTL</th>
                        <th>No Hp</th>
                        <th>TMT</th>
                        <th width="13%">Aksi</th>
                    </x-slot>
                </x-table>
            </x-card>
        </div>
    </div>

    @include('guru.form')
    @include('guru.import-excel')
    @include('guru.penempatan-modal')
@endsection

@include('includes.datatable')

@push('scripts')
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
                url: '{{ route('guru.data') }}',
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
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
                    data: 'jabatan',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'jenis_kelamin',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'ttl',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'no_hp',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'tgl_tmt',
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

        function addForm(url, title = 'Form Guru') {
            $(modal).modal('show');
            $(`${modal} .modal-title`).text(title);
            $(`${modal} form`).attr('action', url);
            $(`${modal} [name=_method]`).val('post');

            resetForm(`${modal} form`);
        }

        function editForm(url, title = 'Form Guru') {
            Swal.fire({
                title: "Memuat...",
                text: "Mohon tunggu sebentar...",
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading(); // Menampilkan spinner loading
                }
            });

            $.get(url)
                .done(response => {
                    Swal.close(); // Tutup loading setelah sukses
                    $(modal).modal('show');
                    $(`${modal} .modal-title`).text(title);
                    $(`${modal} form`).attr('action', url);
                    $(`${modal} [name=_method]`).val('put');

                    resetForm(`${modal} form`);
                    loopForm(response.data);
                })
                .fail(errors => {
                    Swal.close(); // Tutup loading jika terjadi error
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops! Gagal',
                        text: errors.responseJSON?.message || 'Terjadi kesalahan saat memuat data.',
                        showConfirmButton: true,
                    });

                    if (errors.status == 422) {
                        loopErrors(errors.responseJSON.errors);
                    }
                });
        }

        function submitForm(originalForm) {
            $(button).prop('disabled', true);

            // Menampilkan Swal loading
            Swal.fire({
                title: 'Mohon Tunggu...',
                text: 'Sedang memproses data',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading(); // Menampilkan animasi loading
                }
            });

            $.ajax({
                url: $(originalForm).attr('action'),
                type: $(originalForm).attr('method') || 'POST', // Gunakan method dari form
                data: new FormData(originalForm),
                dataType: 'JSON',
                contentType: false,
                cache: false,
                processData: false,
                success: function(response, textStatus, xhr) {
                    Swal.close(); // Tutup Swal Loading

                    if (xhr.status === 201 || xhr.status === 200) {
                        $(modal).modal('hide');

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 3000
                        }).then(() => {
                            $(button).prop('disabled', false);
                            table.ajax.reload(); // Reload DataTables
                        });
                    }
                },
                error: function(xhr) {
                    Swal.close(); // Tutup Swal Loading
                    $(button).prop('disabled', false);

                    let errorMessage = "Terjadi kesalahan!";
                    if (xhr.responseJSON?.message) {
                        errorMessage = xhr.responseJSON.message;
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Oops! Gagal',
                        text: errorMessage,
                        showConfirmButton: false,
                        timer: 3000,
                    });

                    if (xhr.status === 422) {
                        loopErrors(xhr.responseJSON.errors);
                    }
                }
            });
        }

        function deleteData(url, name) {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: true,
            });

            swalWithBootstrapButtons.fire({
                title: 'Delete Data!',
                text: 'Apakah Anda yakin ingin menghapus ' + name +
                    ' ? Data yang dihapus tidak dapat dikembalikan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#aaa',
                confirmButtonText: 'Iya!',
                cancelButtonText: 'Batalkan',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Tampilkan Swal loading sebelum menghapus
                    Swal.fire({
                        title: 'Menghapus...',
                        text: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        type: "DELETE",
                        url: url,
                        dataType: "json",
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 3000
                            }).then(() => {
                                table.ajax.reload(); // Reload DataTables setelah penghapusan
                            });
                        },
                        error: function(xhr, status, error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops! Gagal',
                                text: xhr.responseJSON ? xhr.responseJSON.message :
                                    'Terjadi kesalahan!',
                                showConfirmButton: true,
                            }).then(() => {
                                table.ajax.reload(); // Reload tabel jika terjadi error
                            });
                        }
                    });
                }
            });
        }

        function confirmImport() {
            $(importExcel).modal('show');
        }
    </script>

    <script>
        let modalPenempatan = '#modal-penempatan';

        function penempatanGuru(url) {
            Swal.fire({
                title: 'Memuat...',
                text: 'Mengambil data penempatan',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            $.get(url)
                .done(res => {
                    Swal.close();

                    $(modalPenempatan).modal('show');
                    $('#form-penempatan').attr('action', url);

                    // isi data
                    $('#namaGuru').val(res.data.user.name);
                    $('[name=departemen_id]').val(res.data.departemen_id);
                    $('[name=jabatan_id]').val(res.data.jabatan_id);
                })
                .fail(() => {
                    Swal.fire('Error', 'Gagal memuat data', 'error');
                });
        }

        $('#form-penempatan').on('submit', function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Menyimpan...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: $(this).serialize(),
                success: function(res) {
                    Swal.close();

                    // ✅ tutup modal
                    $(modalPenempatan).modal('hide');

                    // ✅ reload DataTable (tetap di halaman)
                    table.ajax.reload(null, false);

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: res.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                },
                error: function(xhr) {
                    Swal.close();
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: xhr.responseJSON?.message || 'Terjadi kesalahan'
                    });
                }
            });
        });
    </script>

    <script>
        function settingJamKerja(url) {
            Swal.fire({
                title: 'Setting Jam Kerja',
                text: 'Anda akan membuka pengaturan jam kerja guru ini',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, lanjutkan',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33'
            }).then((result) => {
                if (result.isConfirmed) {

                    Swal.fire({
                        title: 'Memuat...',
                        text: 'Menyiapkan pengaturan jam kerja',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    setTimeout(() => {
                        window.location.href = url;
                    }, 600);
                }
            });
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', () => Swal.close());
        window.addEventListener('pageshow', () => Swal.close());
    </script>
@endpush
