<div class="modal fade" id="modal-penempatan" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">Penempatan Guru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-penempatan" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-body">

                    <div class="mb-3">
                        <label>Nama Guru</label>
                        <input type="text" id="namaGuru" class="form-control" disabled>
                    </div>

                    <div class="mb-3">
                        <label>Departemen</label>
                        <select name="departemen_id" class="form-control" required>
                            <option value="">-- Pilih Departemen --</option>
                            @foreach ($departemens as $d)
                                <option value="{{ $d->id }}">{{ $d->nama_dept }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Jabatan</label>
                        <select name="jabatan_id" class="form-control" required>
                            <option value="">-- Pilih Jabatan --</option>
                            @foreach ($jabatans as $j)
                                <option value="{{ $j->id }}">{{ $j->nama_jab }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-success" type="submit">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
