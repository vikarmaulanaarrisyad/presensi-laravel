@extends('layouts.app')

@section('title', 'Monitoring Presensi Guru')

@section('breadcrumb')
    @parent
    <li class="breadcrumb-item active">Manajemen Presensi</li>
    <li class="breadcrumb-item active">@yield('title')</li>
@endsection

@push('css_vendor')
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
@endpush


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
                            <th>Jabatan</th>
                            <th>Jam Masuk</th>
                            <th>Foto</th>
                            <th>Jam Pulang</th>
                            <th>Foto</th>
                            <th>Keterangan</th>
                            <th>Aksi</th>
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

    <div class="modal fade" id="modal-map" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Lokasi Presensi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <!-- MAP -->
                        <div class="col-md-7">
                            <div id="map" style="height: 400px;"></div>
                        </div>

                        <!-- INFO -->
                        <div class="col-md-5">
                            <table class="table table-sm table-bordered">
                                <tr>
                                    <th width="40%">Nama Guru</th>
                                    <td id="info-nama"></td>
                                </tr>
                                <tr>
                                    <th>Tanggal</th>
                                    <td id="info-tanggal"></td>
                                </tr>
                                <tr>
                                    <th>Jam Masuk</th>
                                    <td id="info-jam-in"></td>
                                </tr>
                                <tr>
                                    <th>Jam Pulang</th>
                                    <td id="info-jam-out"></td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td id="info-keterangan"></td>
                                </tr>
                                <tr>
                                    <th>Koordinat</th>
                                    <td id="info-koordinat"></td>
                                </tr>
                                <tr>
                                    <th>Alamat</th>
                                    <td id="info-alamat">Memuat...</td>
                                </tr>
                            </table>

                            <a id="btn-gmaps" target="_blank" class="btn btn-success btn-sm w-100">
                                <i class="fa fa-map"></i> Buka di Google Maps
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

@endsection

@include('includes.datatable')
@include('includes.datepicker')

@push('scripts')
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
@endpush


@push('scripts')
    <script>
        $(function() {
            let table = $('#table-presensi').DataTable({
                processing: true,
                serverSide: true,
                ordering: false, // ❌ matikan sorting
                searching: false, // ❌ matikan search
                lengthChange: false, // ❌ matikan dropdown show entries
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
                        data: 'jabatan',
                        name: 'jabatan'
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
                    {
                        data: 'aksi'
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

    <script>
        let map, marker;

        function showMap(data) {

            $('#modal-map').modal('show');

            // Isi informasi
            $('#info-nama').text(data.nama);
            $('#info-tanggal').text(data.tanggal);
            $('#info-jam-in').text(data.jam_in ?? '-');
            $('#info-jam-out').text(data.jam_out ?? '-');
            $('#info-keterangan').html(data.status);
            $('#info-koordinat').text(data.lat + ', ' + data.lng);

            $('#btn-gmaps').attr(
                'href',
                `https://www.google.com/maps?q=${data.lat},${data.lng}`
            );

            $('#modal-map').on('shown.bs.modal', function() {

                if (!map) {
                    map = L.map('map').setView([data.lat, data.lng], 17);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; OpenStreetMap'
                    }).addTo(map);

                    marker = L.marker([data.lat, data.lng]).addTo(map);
                } else {
                    map.invalidateSize();
                    map.setView([data.lat, data.lng], 17);
                    marker.setLatLng([data.lat, data.lng]);
                }

                marker.bindPopup(`
                <strong>${data.nama}</strong><br>
                ${data.tanggal}<br>
                Jam Masuk: ${data.jam_in}<br>
                Jam Pulang: ${data.jam_out}
            `).openPopup();

                // Reverse Geocoding
                fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${data.lat}&lon=${data.lng}`)
                    .then(res => res.json())
                    .then(res => {
                        $('#info-alamat').text(res.display_name ?? '-');
                    })
                    .catch(() => {
                        $('#info-alamat').text('-');
                    });
            });
        }
    </script>
@endpush
