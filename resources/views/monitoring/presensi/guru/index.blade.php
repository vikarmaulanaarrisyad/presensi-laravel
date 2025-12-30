@extends('layouts.app')

@section('title', 'Monitoring Presensi Guru')

@section('breadcrumb')
    @parent
    <li class="breadcrumb-item active">Manajemen Presensi</li>
    <li class="breadcrumb-item active">@yield('title')</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <x-card>
                <x-slot name="header">
                    <div class="row g-2">
                        {{-- Filter Tanggal --}}
                        <div class="col-md-3">
                            <input type="date" id="start" class="form-control" value="{{ $start }}">
                        </div>
                        <div class="col-md-3">
                            <input type="date" id="end" class="form-control" value="{{ $end }}">
                        </div>

                        {{-- Filter Guru --}}
                        <div class="col-md-4">
                            <select id="user_id" class="form-control">
                                <option value="">-- Semua Guru --</option>
                                @foreach ($users as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                                @endforeach
                            </select>

                        </div>

                        {{-- Tombol Filter --}}
                        <div class="col-md-2">
                            <button id="btn-filter" class="btn btn-primary w-100">
                                <i class="fa fa-search"></i> Filter
                            </button>
                        </div>
                    </div>
                </x-slot>

                <div class="table-responsive mt-3">
                    <x-table id="table-presensi">
                        <x-slot name="thead">
                            <th width="5%">No</th>
                            <th>Nama Guru</th>
                            <th>Departemen</th>
                            <th>Jam Masuk</th>
                            <th>Foto</th>
                            <th>Jam Pulang</th>
                            <th>Foto</th>
                            <th>Keterangan</th>
                        </x-slot>
                    </x-table>

                </div>
            </x-card>
        </div>
    </div>

    <div class="modal fade" id="modal-preview" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <img id="preview-img" class="img-fluid rounded">
                </div>
            </div>
        </div>
    </div>
@endsection

@include('includes.datatable')
@include('includes.datepicker')

@push('scripts')
    <script>
        $(function() {
            let table = $('#table-presensi').DataTable({
                processing: true,
                serverSide: true,
                ordering: false,
                ajax: {
                    url: "{{ url('/monitoring/presensi/guru/data') }}",
                    data: function(d) {
                        d.start = $('#start').val();
                        d.end = $('#end').val();
                        d.user_id = $('#user_id').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nama_guru',
                        name: 'guru.nama'
                    },
                    {
                        data: 'departemen',
                        name: 'departemen'
                    },
                    {
                        data: 'jam_in',
                        name: 'jam_in'
                    },
                    {
                        data: 'foto_in',
                        name: 'foto_in'
                    },
                    {
                        data: 'jam_out',
                        name: 'jam_out'
                    },
                    {
                        data: 'foto_out',
                        name: 'foto_out'
                    },
                    {
                        data: 'keterangan',
                        name: 'keterangan'
                    },
                ]
            });

            // Klik tombol filter
            $('#btn-filter').on('click', function() {
                table.ajax.reload();
            });

            // Auto reload saat ganti guru
            $('#user_id').on('change', function() {
                table.ajax.reload();
            });
        });

        function previewImage(src) {
            $('#preview-img').attr('src', src);
            $('#modal-preview').modal('show');
        }
    </script>
@endpush
