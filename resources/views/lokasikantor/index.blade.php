@extends('layouts.app')

@section('title', 'Lokasi Kantor')

@section('breadcrumb')
    @parent
    <li class="breadcrumb-item active">Konfigurasi</li>
    <li class="breadcrumb-item active">@yield('title')</li>
@endsection

@push('css_vendor')
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
@endpush

@push('css')
    <style>
        #map {
            height: 250px;
            width: 100%;
            border-radius: 10px;
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-lg-12 col-12 col-md-12">
            <x-card>
                <x-slot name="header">
                    <button onclick="addForm(`{{ route('kantor.store') }}`)" class="btn btn-sm btn-info">
                        <i class="fas fa-plus-circle"></i>
                        Tambah Data
                    </button>
                </x-slot>

                <x-table>
                    <x-slot name="thead">
                        <th width="5%">NO</th>
                        <th>Nama Departemen</th>
                        <th>Lokasi Kantor</th>
                        <th>Radius (Meter)</th>
                        <th width="15%">Aksi</th>
                    </x-slot>
                </x-table>
            </x-card>
        </div>
    </div>

    <x-modal id="modal-map" size="modal-lg">
        <x-slot name="title">
            Lokasi Kantor / Departemen
        </x-slot>

        <div class="mb-2">
            <strong>Departemen:</strong>
            <span id="map-departemen"></span>
        </div>

        <div id="map-view" style="height:400px;border-radius:10px;"></div>

        <x-slot name="footer">
            <button type="button" data-dismiss="modal" class="btn btn-sm btn-secondary">
                Tutup
            </button>
        </x-slot>
    </x-modal>


    @include('lokasikantor.form')
@endsection

@include('includes.datatable')

@push('scripts')
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
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
                url: '{{ route('kantor.data') }}',
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'nama_dept',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'lokasi_kantor',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'radius',
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

        function addForm(url, title = 'Form Konfigurasi Lokasi Kantor') {
            $(modal).modal('show');
            $(`${modal} .modal-title`).text(title);
            $(`${modal} form`).attr('action', url);
            $(`${modal} [name=_method]`).val('post');

            resetForm(`${modal} form`);

            setTimeout(() => {
                initMap();
            }, 400);
        }

        function editForm(url, title = 'Form Konfigurasi Lokasi Kantor') {
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

                    setTimeout(() => {
                        initMap(response.data.latitude, response.data.longitude);
                    }, 300);
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
        let map;
        let marker;
        let geocoder;

        function initMap(lat = -6.879704, lng = 109.125595) {

            if (map) {
                map.remove();
            }

            map = L.map('map').setView([lat, lng], 15);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap'
            }).addTo(map);

            marker = L.marker([lat, lng], {
                draggable: true
            }).addTo(map);

            updateLatLng(lat, lng);

            // Klik peta
            map.on('click', function(e) {
                marker.setLatLng(e.latlng);
                updateLatLng(e.latlng.lat, e.latlng.lng);
            });

            // Drag marker
            marker.on('dragend', function(e) {
                const pos = e.target.getLatLng();
                updateLatLng(pos.lat, pos.lng);
            });

            // Geocoder
            geocoder = L.Control.geocoder({
                    defaultMarkGeocode: false,
                    placeholder: 'Cari alamat...'
                })
                .on('markgeocode', function(e) {
                    const center = e.geocode.center;

                    map.setView(center, 17);
                    marker.setLatLng(center);

                    updateLatLng(center.lat, center.lng);
                    $('#alamat').val(e.geocode.name); // opsional simpan alamat
                })
                .addTo(map);

            // FIX ukuran map di modal
            setTimeout(() => {
                map.invalidateSize();
            }, 300);
        }

        function updateLatLng(lat, lng) {
            $('#latitude').val(lat.toFixed(7));
            $('#longitude').val(lng.toFixed(7));
        }

        function getMyLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;

                    map.setView([lat, lng], 17);
                    marker.setLatLng([lat, lng]);
                    updateLatLng(lat, lng);
                });
            } else {
                alert("Geolocation tidak didukung browser ini");
            }
        }

        // ============================
        // SEARCH ADDRESS (CUSTOM INPUT)
        // ============================
        function searchAddress() {
            const query = $('#search-address').val();

            if (!query) {
                Swal.fire('Info', 'Masukkan alamat terlebih dahulu', 'info');
                return;
            }

            Swal.fire({
                title: 'Mencari lokasi...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            $.get('https://nominatim.openstreetmap.org/search', {
                    q: query,
                    format: 'json',
                    limit: 1
                })
                .done(res => {
                    Swal.close();

                    if (res.length === 0) {
                        Swal.fire('Tidak ditemukan', 'Alamat tidak ditemukan', 'warning');
                        return;
                    }

                    const lat = parseFloat(res[0].lat);
                    const lng = parseFloat(res[0].lon);

                    map.setView([lat, lng], 17);
                    marker.setLatLng([lat, lng]);
                    updateLatLng(lat, lng);

                    // Simpan alamat hasil
                    $('#alamat').val(res[0].display_name);
                })
                .fail(() => {
                    Swal.close();
                    Swal.fire('Error', 'Gagal mencari alamat', 'error');
                });
        }

        // ENTER untuk search
        $('#search-address').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                searchAddress();
            }
        });
    </script>
    <script>
        let viewMap;
        let viewMarker;
        let viewCircle;

        function showMap(url) {

            Swal.fire({
                title: 'Memuat peta...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            $.get(url)
                .done(res => {
                    Swal.close();
                    $('#modal-map').modal('show');

                    $('#map-departemen').text(res.data.departemen.nama_dept);

                    const coord = parseLatLng(res.data.lokasi_kantor);

                    setTimeout(() => {
                        initViewMap(
                            coord.lat,
                            coord.lng,
                            res.data.radius
                        );
                    }, 300);
                })
                .fail(() => {
                    Swal.fire('Error', 'Gagal memuat lokasi', 'error');
                });
        }

        function parseLatLng(lokasi) {
            let split = lokasi.split(',');
            return {
                lat: parseFloat(split[0].trim()),
                lng: parseFloat(split[1].trim())
            };
        }

        function initViewMap(lat, lng, radius) {

            if (viewMap) {
                viewMap.remove();
            }

            viewMap = L.map('map-view', {
                zoomControl: true,
                dragging: true,
                scrollWheelZoom: true
            }).setView([lat, lng], 16);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap'
            }).addTo(viewMap);

            viewMarker = L.marker([lat, lng]).addTo(viewMap);

            viewCircle = L.circle([lat, lng], {
                radius: radius,
                color: '#28a745',
                fillColor: '#28a745',
                fillOpacity: 0.25
            }).addTo(viewMap);

            setTimeout(() => {
                viewMap.invalidateSize();
            }, 300);
        }
    </script>
@endpush
