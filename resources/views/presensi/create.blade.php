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

    <style>
        .webcam-capture,
        .webcam-capture video {
            display: inline-block;
            width: 100% !important;
            margin: auto;
            height: auto !important;
            border-radius: 15px;
        }

        #map {
            height: 200px;
        }
    </style>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
@endsection

@section('content')
    <div class="row" style="margin-top: 70px">
        <div class="col">
            <input type="hidden" id="lokasi">
            <div class="webcam-capture"></div>
        </div>
    </div>

    <div class="row">
        <div class="col">
            @if ($cekPresensi > 0)
                <button id="takeabsen" class="btn btn-danger btn-block">
                    <ion-icon name="camera-outline"></ion-icon>
                    Absen Pulang
                </button>
            @else
                <button id="takeabsen" class="btn btn-primary btn-block">
                    <ion-icon name="camera-outline"></ion-icon>
                    Absen Masuk
                </button>
            @endif

        </div>
    </div>

    <div class="row mt-2">
        <div class="col">
            <div id="map"></div>
        </div>
    </div>
@endsection

@push('script_vendor')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endpush

@push('scripts')
    <script>
        let LAT_KANTOR = {{ $latKantor }};
        let LNG_KANTOR = {{ $lngKantor }};
        let RADIUS_MAX = {{ $konfigurasi->radius }}; // meter

        Webcam.set({
            height: 480,
            width: 640,
            image_format: 'jpeg',
            jpeg_quality: 80
        });

        Webcam.attach('.webcam-capture');

        let lokasi = document.getElementById('lokasi');

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(successCallback, errorCallback);
        }

        function successCallback(position) {
            let latitude = position.coords.latitude;
            let longitude = position.coords.longitude;

            lokasi.value = latitude + ',' + longitude;

            // init map (fokus ke kantor)
            let map = L.map('map').setView([LAT_KANTOR, LNG_KANTOR], 16);

            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap'
            }).addTo(map);

            // 🏢 marker kantor
            L.marker([LAT_KANTOR, LNG_KANTOR])
                .addTo(map)
                .bindPopup("Lokasi Kantor")
                .openPopup();

            // 🔴 radius kantor (dari konfigurasi)
            L.circle([LAT_KANTOR, LNG_KANTOR], {
                color: 'green',
                fillColor: '#00ff00',
                fillOpacity: 0.1,
                radius: RADIUS_MAX
            }).addTo(map);

            // 👤 marker user
            L.marker([latitude, longitude], {
                    icon: L.icon({
                        iconUrl: 'https://cdn-icons-png.flaticon.com/512/149/149071.png',
                        iconSize: [16, 16]
                    })
                }).addTo(map)
                .bindPopup("Posisi Anda")
                .openPopup();
        }

        function errorCallback(error) {
            alert('Gagal mengambil lokasi');
        }

        $('#takeabsen').on('click', function(e) {
            e.preventDefault();

            const btn = $(this);
            btn.prop('disabled', true);

            Swal.fire({
                title: 'Menyimpan Data...',
                text: 'Mohon tunggu sebentar.',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            Webcam.snap(function(image) {

                let lokasi = $('#lokasi').val();

                $.ajax({
                    type: 'POST',
                    url: '{{ route('presensi.store') }}',
                    data: {
                        _token: "{{ csrf_token() }}",
                        image: image,
                        lokasi: lokasi
                    },
                    success: function(response) {
                        Swal.close();

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message ?? 'Postingan berhasil disimpan',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = response.redirect ??
                                '{{ route('dashboard') }}';
                        });
                    },

                    error: function(xhr) {
                        Swal.close();
                        btn.prop('disabled', false);

                        let msg = xhr.responseJSON?.message ??
                            'Terjadi kesalahan saat menyimpan data.';

                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: msg,
                            timer: 3000
                        });

                        if (xhr.status === 422) {
                            loopErrors(xhr.responseJSON.errors);
                        }
                    }
                });

            });
        });
    </script>
@endpush
