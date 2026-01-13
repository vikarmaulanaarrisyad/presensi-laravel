@extends('layouts.app')

@section('title', 'Seting Jam Kerja')

@section('breadcrumb')
    @parent
    <li class="breadcrumb-item active">Konfigurasi Jam Kerja</li>
    <li class="breadcrumb-item active">@yield('title')</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-md-12 col-12">
            <x-card>

                {{-- ================= HEADER ================= --}}
                <x-slot name="header">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>
                            <i class="fas fa-user-clock"></i>
                            Setting Jam Kerja Guru
                        </span>
                        <button type="button" onclick="submitForm()" class="btn btn-sm btn-success">
                            <i class="fas fa-save"></i> Simpan Setting
                        </button>
                    </div>
                </x-slot>

                <form id="formSettingJamKerja" action="{{ route('konfigurasi-jamkerja.store', $guru->user->id) }}"
                    method="POST">
                    @csrf
                    {{-- ================= SETTING JAM KERJA ================= --}}
                    <div class="d-flex justify-content-end mb-2">
                        <button type="button" class="btn btn-sm btn-secondary" onclick="copySenin()">
                            <i class="fas fa-copy"></i> Copy Senin ke Semua Hari
                        </button>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="row mb-3">

                                <div class="col-md-3 text-center">
                                    <img src="{{ $guru->foto ?? asset('assets/img/avatar.png') }}"
                                        class="img-thumbnail mb-2" style="width:120px;height:120px;object-fit:cover;">
                                </div>
                                {{-- ================= INFO GURU ================= --}}
                                <div class="col-md-9">
                                    <x-table>
                                        <tr>
                                            <th width="30%">Nama Guru</th>
                                            <td>: {{ $guru->nama_guru }}</td>
                                        </tr>
                                        <tr>
                                            <th>NIP / NIK</th>
                                            <td>: {{ $guru->nip ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Mata Pelajaran</th>
                                            <td>: {{ $guru->mapel->nama_mapel ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Status</th>
                                            <td>: <span class="badge bg-success">Aktif</span></td>
                                        </tr>
                                    </x-table>

                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <x-table>
                                <x-slot name="thead">
                                    <th width="5%">No</th>
                                    <th>Hari</th>
                                    <th width="12%" class="text-center">Libur</th>
                                    <th>Jam Kerja</th>
                                </x-slot>

                                <tbody>
                                    @php
                                        $hari = [
                                            'senin' => 'Senin',
                                            'selasa' => 'Selasa',
                                            'rabu' => 'Rabu',
                                            'kamis' => 'Kamis',
                                            'jumat' => 'Jumat',
                                            'sabtu' => 'Sabtu',
                                            'minggu' => 'Minggu',
                                        ];
                                    @endphp

                                    @foreach ($hari as $key => $label)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>

                                            <td>
                                                <strong>{{ $label }}</strong>
                                                <input type="hidden" name="hari[]" value="{{ $key }}">
                                            </td>

                                            <td class="text-center">
                                                <input type="checkbox" class="form-check-input libur-checkbox"
                                                    name="libur[{{ $key }}]" data-hari="{{ $key }}"
                                                    {{ $libur[$key] ?? false ? 'checked' : '' }}>
                                            </td>

                                            <td>
                                                <select name="jam_kerja_id[{{ $key }}]"
                                                    class="form-control jamkerja-select" data-hari="{{ $key }}"
                                                    {{ $libur[$key] ?? false ? 'disabled' : '' }}>
                                                    <option value="">-- Pilih Jam Kerja --</option>
                                                    @foreach ($jamKerja as $jk)
                                                        <option value="{{ $jk->id }}"
                                                            {{ ($existing[$key] ?? null) == $jk->id ? 'selected' : '' }}>
                                                            {{ $jk->nama_jam_kerja }}
                                                            ({{ $jk->jam_masuk }} - {{ $jk->jam_pulang }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </x-table>
                        </div>
                    </div>

                </form>
            </x-card>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        /* ================= COPY SENIN ================= */
        function copySenin() {
            const senin = document.querySelector('select[name="jam_kerja_id[senin]"]').value;

            if (!senin) {
                Swal.fire('Peringatan', 'Jam kerja hari Senin belum dipilih', 'warning');
                return;
            }

            document.querySelectorAll('.jamkerja-select').forEach(select => {
                if (!select.disabled) {
                    select.value = senin;
                }
            });

            Swal.fire('Berhasil', 'Jam kerja Senin disalin ke semua hari', 'success');
        }

        /* ================= LIBUR CHECKBOX ================= */
        document.querySelectorAll('.libur-checkbox').forEach(cb => {
            cb.addEventListener('change', function() {
                const hari = this.dataset.hari;
                const select = document.querySelector(`select[name="jam_kerja_id[${hari}]"]`);

                if (this.checked) {
                    select.value = '';
                    select.setAttribute('disabled', true);
                } else {
                    select.removeAttribute('disabled');
                }
            });
        });

        /* ================= AJAX SAVE ================= */
        function submitForm() {
            Swal.fire({
                title: 'Simpan Setting?',
                text: 'Pengaturan jam kerja akan disimpan',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Batal'
            }).then((res) => {
                if (res.isConfirmed) {

                    const form = document.getElementById('formSettingJamKerja');
                    const formData = new FormData(form);

                    Swal.fire({
                        title: 'Menyimpan...',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });

                    fetch(form.action, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: formData
                        })
                        .then(res => res.json())
                        .then(res => {
                            if (res.status) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: res.message ?? 'Jam kerja berhasil disimpan',
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    window.location.href = "{{ route('guru.index') }}";
                                });
                            } else {
                                Swal.fire('Gagal', res.message ?? 'Terjadi kesalahan', 'error');
                            }
                        })
                        .catch(() => {
                            Swal.fire('Error', 'Gagal menyimpan data', 'error');
                        });

                }
            });
        }
    </script>
@endpush
