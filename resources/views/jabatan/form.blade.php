<x-modal data-backdrop="static" data-keyboard="false" size="modal-md">
    <x-slot name="title">
        Tambah Data Guru
    </x-slot>

    @method('POST')

    <div class="row">
        <div class="col-lg-12">
            <div class="form-group">
                <label>Nama Jabatan <span class="text-danger">*</span></label>
                <input type="text" name="nama_jab" class="form-control">
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
