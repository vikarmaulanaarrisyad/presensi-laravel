@if ($data->count() > 0)

    @foreach ($data as $d)
        <ul class="listview image-listview">
            <li>
                <div class="item">
                    <img src="{{ Storage::url($d->foto_in) }}" class="image" alt="">
                    <div class="in">
                        <div>
                            <b>{{ date('d-m-Y', strtotime($d->tgl_presensi)) }}</b>
                            <br>
                            {{-- <small>Keterangan</small> --}}
                        </div>

                        <span class="badge {{ $d->jam_in < '07:00' ? 'badge-success' : 'bg-danger' }}">
                            {{ $d->jam_in ?? '-' }}
                        </span>

                        <span class="badge badge-primary">
                            {{ $d->jam_out ?? '-' }}
                        </span>
                    </div>
                </div>
            </li>
        </ul>
    @endforeach
@else
    <div class="alert alert-outline-warning text-center mt-3">
        <ion-icon name="calendar-outline" style="font-size:40px"></ion-icon>
        <h4 class="mt-1">Belum Ada Presensi</h4>
        <p class="mb-0">
            Silakan pilih bulan dan tahun lainnya
        </p>
    </div>

@endif
