<?php

namespace App\Imports;

use App\Models\Guru;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class GuruImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        DB::beginTransaction();

        try {

            /**
             * 1️⃣ NORMALISASI HEADER
             */
            $data = [];
            foreach ($row as $key => $value) {
                $key = strtolower(trim($key));
                $key = str_replace([" ", "-", "\n", "\r"], "_", $key);
                $data[$key] = is_string($value) ? trim($value) : $value;
            }

            /**
             * 2️⃣ SKIP BARIS KOSONG (INI KUNCINYA 🔑)
             */
            $nama  = $data['nama_lengkap'] ?? null;
            $email = $data['email'] ?? null;

            if (empty($nama) && empty($email)) {
                // baris kosong → skip → lanjut ke baris berikutnya
                DB::rollBack();
                return null;
            }

            /**
             * 3️⃣ VALIDASI MINIMAL
             */
            if (!$nama || !$email) {
                DB::rollBack();
                return null; // skip baris tidak lengkap
            }

            /**
             * 4️⃣ NORMALISASI JENIS KELAMIN
             */
            $jkRaw = $data['jenis_kelamin'] ?? null;
            if (!$jkRaw) {
                DB::rollBack();
                return null;
            }

            $jk = strtoupper(trim($jkRaw));
            if (in_array($jk, ['L', 'LAKI', 'LAKI-LAKI'])) {
                $jk = 'L';
            } elseif (in_array($jk, ['P', 'PEREMPUAN'])) {
                $jk = 'P';
            } else {
                DB::rollBack();
                return null;
            }

            /**
             * 5️⃣ USER
             */
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name'     => $nama,
                    'username' => $email,
                    'password' => Hash::make('password123'),
                ]
            );

            $user->assignRole('guru');

            /**
             * 6️⃣ GURU
             */
            Guru::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nama_guru'      => $nama,
                    'gelar_depan'    => $data['gelar_depan'] ?? null,
                    'gelar_belakang' => $data['gelar_belakang'] ?? null,
                    'jenis_kelamin'  => $jk,
                    'tempat_lahir'   => $data['tempat_lahir'] ?? null,
                    'no_hp'          => $data['no_hp'] ?? null,
                    'tgl_lahir'      => $data['tanggal_lahir'] ?? null,
                    'tgl_tmt'        => $data['tanggal_tmt'] ?? null,
                ]
            );

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
