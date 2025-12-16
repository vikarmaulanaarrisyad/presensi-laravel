@if ($data->count() > 0)
    <div style="padding-bottom:40px">
        @foreach ($data as $d)
            <div class="card mb-1">
                <div class="card-body p-2">

                    <!-- HEADER TANGGAL -->
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <strong>
                            <ion-icon name="calendar-outline"></ion-icon>
                            {{ date('d M Y', strtotime($d->tgl_presensi)) }}
                        </strong>

                        <span class="badge {{ $d->jam_in < '07:00' ? 'badge-success' : 'badge-danger' }}">
                            {{ $d->jam_in < '07:00' ? 'Tepat Waktu' : 'Terlambat' }}
                        </span>
                    </div>

                    <div class="row align-items-center">
                        <!-- FOTO MASUK -->
                        <div class="col-3 text-center">
                            <img src="{{ Storage::url($d->foto_in) }}" class="img-fluid rounded"
                                style="height:60px; object-fit:cover">
                            <small class="text-muted d-block mt-1">Masuk</small>
                        </div>

                        <!-- INFO JAM -->
                        <div class="col-6 text-center">
                            <div>
                                <span class="badge badge-success mb-1">
                                    <ion-icon name="log-in-outline"></ion-icon>
                                    {{ $d->jam_in ?? '-' }}
                                </span>
                            </div>

                            <div class="mt-1">
                                <span class="badge badge-primary">
                                    <ion-icon name="log-out-outline"></ion-icon>
                                    {{ $d->jam_out ?? 'Belum Pulang' }}
                                </span>
                            </div>
                        </div>

                        <!-- FOTO PULANG -->
                        <div class="col-3 text-center">
                            @if ($d->foto_out)
                                <img src="{{ Storage::url($d->foto_out) }}" class="img-fluid rounded"
                                    style="height:60px; object-fit:cover">
                            @else
                                <div class="text-muted" style="font-size:12px">
                                    Belum Absen
                                </div>
                            @endif
                            <small class="text-muted d-block mt-1">Pulang</small>
                        </div>
                    </div>

                </div>
            </div>
        @endforeach
    </div>
@else
    <!-- EMPTY STATE PROFESIONAL -->
    <div class="card mt-3">
        <div class="card-body text-center">
            <ion-icon name="document-text-outline" style="font-size:48px" class="text-warning"></ion-icon>
            <h4 class="mt-2">Data Presensi Kosong</h4>
            <p class="text-muted mb-0">
                Tidak ditemukan data presensi pada periode yang dipilih
            </p>
        </div>
    </div>

@endif
