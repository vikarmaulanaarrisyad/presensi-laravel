<x-modal data-backdrop="static" data-keyboard="false" size="modal-lg">
    <x-slot name="title">
        Tambah Data Guru
    </x-slot>

    @method('POST')

    <div class="row">
        <div class="col-md-12 mb-3">
            <label>Pilih Departemen / Kantor</label>
            <select name="departemen_id" class="form-control" required>
                <option disabled selected>Pilih Departemen</option>
                @foreach ($departemen as $d)
                    <option value="{{ $d->id }}">{{ $d->nama_dept }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group mb-2 col-md-12">
            <label>Cari Alamat</label>
            <div class="input-group">
                <input type="text" id="search-address" class="form-control"
                    placeholder="Contoh: MI Bustanul Huda Dawuhan">
                <button type="button" class="btn btn-primary" onclick="searchAddress()">
                    Cari
                </button>
            </div>
        </div>

        <div class="col-md-6">
            <label>Latitude</label>
            <input type="text" name="latitude" id="latitude" class="form-control" readonly>
        </div>

        <div class="col-md-6">
            <label>Longitude</label>
            <input type="text" name="longitude" id="longitude" class="form-control" readonly>
        </div>

        <div class="col-md-6 mt-2">
            <label>Radius Presensi (meter)</label>
            <input type="number" name="radius" id="radius" class="form-control" value="50" min="10"
                step="10" required>
        </div>

        <div class="col-md-12 mt-3">
            <label>Pilih Lokasi (Klik Peta)</label>
            <div id="map"></div>
        </div>
    </div>

    <x-slot name="footer">
        <button type="button" class="btn btn-sm btn-primary mt-2" onclick="getMyLocation()">
            <i class="fas fa-location-arrow"></i> Gunakan Lokasi Saya
        </button>
        <button type="button" onclick="submitForm(this.form)" class="btn btn-sm btn-outline-primary">
            <i class="fas fa-save mr-1"></i> Simpan
        </button>
        <button type="button" data-dismiss="modal" class="btn btn-sm btn-outline-danger">
            <i class="fas fa-times"></i> Batal
        </button>
    </x-slot>
</x-modal>
