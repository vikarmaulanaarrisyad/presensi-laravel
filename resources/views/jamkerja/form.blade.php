<x-modal data-backdrop="static" data-keyboard="false" size="modal-lg">
    <x-slot name="title">
        Tambah Data Jam Kerja
    </x-slot>

    @method('POST')

    <div class="row">
        <div class="col-lg-6">
            <div class="form-group">
                <label>Kode Jam Kerja <span class="text-danger">*</span></label>
                <input type="text" name="kode_jam_kerja" class="form-control" required>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="form-group">
                <label>Nama Jam Kerja <span class="text-danger">*</span></label>
                <input type="text" name="nama_jam_kerja" class="form-control" required>
            </div>
        </div>

        {{-- Awal Jam Masuk --}}
        <div class="col-lg-6">
            <div class="form-group">
                <label>Awal Jam Masuk</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="far fa-clock"></i></span>
                    </div>
                    <input type="text" name="awal_jam_masuk" id="awal_jam_masuk" class="form-control jam-picker">
                </div>
                <small class="text-muted">Format 24 Jam (WIB)</small>
            </div>
        </div>

        {{-- Jam Masuk --}}
        <div class="col-lg-6">
            <div class="form-group">
                <label>Jam Masuk <span class="text-danger">*</span></label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="far fa-clock"></i></span>
                    </div>
                    <input type="text" name="jam_masuk" id="jam_masuk" class="form-control jam-picker" required>
                </div>
                <small class="text-muted">Contoh: 07:00 WIB</small>
            </div>
        </div>

        {{-- Akhir Jam Masuk --}}
        <div class="col-lg-6">
            <div class="form-group">
                <label>Akhir Jam Masuk</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="far fa-clock"></i></span>
                    </div>
                    <input type="text" name="akhir_jam_masuk" id="akhir_jam_masuk" class="form-control jam-picker">
                </div>
            </div>
        </div>

        {{-- Jam Pulang --}}
        <div class="col-lg-6">
            <div class="form-group">
                <label>Jam Pulang <span class="text-danger">*</span></label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="far fa-clock"></i></span>
                    </div>
                    <input type="text" name="jam_pulang" id="jam_pulang" class="form-control jam-picker" required>
                </div>
                <small class="text-muted">Jam pulang harus lebih besar dari jam masuk</small>
            </div>
        </div>
    </div>

    <x-slot name="footer">
        <button type="button" onclick="submitForm(this.form)" class="btn btn-sm btn-outline-primary">
            <i class="fas fa-save mr-1"></i> Simpan
        </button>
        <button type="button" data-dismiss="modal" class="btn btn-sm btn-outline-danger">
            <i class="fas fa-times"></i> Batal
        </button>
    </x-slot>
</x-modal>
