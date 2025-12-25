<x-modal data-backdrop="static" data-keyboard="false" size="modal-md">
    <x-slot name="title">
        Tambah Data Guru
    </x-slot>

    @method('POST')

    <div class="row">
        <div class="col-lg-12">
            <div class="form-group">
                <label>Nama Guru <span class="text-danger">*</span></label>
                <input type="text" name="nama_guru" class="form-control" required>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="form-group">
                <label>Email <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control" placeholder="contoh@email.com" required>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="form-group">
                <label>Gelar Depan</label>
                <input type="text" name="gelar_depan" class="form-control" placeholder="Contoh: Dr.">
            </div>
        </div>

        <div class="col-lg-6">
            <div class="form-group">
                <label>Gelar Belakang</label>
                <input type="text" name="gelar_belakang" class="form-control" placeholder="Contoh: M.Pd">
            </div>
        </div>

        <div class="col-lg-12">
            <div class="form-group">
                <label>Jenis Kelamin <span class="text-danger">*</span></label>
                <select name="jenis_kelamin" class="form-control" required>
                    <option value="">-- Pilih --</option>
                    <option value="L">Laki-laki</option>
                    <option value="P">Perempuan</option>
                </select>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="form-group">
                <label>Tempat Lahir <span class="text-danger">*</span></label>
                <input type="text" name="tempat_lahir" class="form-control" required>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="form-group">
                <label>No. HP <span class="text-danger">*</span></label>
                <input type="text" name="no_hp" class="form-control" placeholder="08xxxxxxxxxx" required>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="form-group">
                <label>Tanggal Lahir <span class="text-danger">*</span></label>
                <input type="date" name="tgl_lahir" class="form-control" required>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="form-group">
                <label>Tanggal TMT <span class="text-danger">*</span></label>
                <input type="date" name="tgl_tmt" class="form-control" required>
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
