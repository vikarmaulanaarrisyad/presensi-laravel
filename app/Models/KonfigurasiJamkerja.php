<?php

namespace App\Models;

class KonfigurasiJamkerja extends Model
{
    protected $casts = [
        'libur' => 'boolean',
    ];

    /* ================= RELATION ================= */

    // 🔗 ke User (Guru)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 🔗 ke Jam Kerja
    public function jamKerja()
    {
        return $this->belongsTo(JamKerja::class);
    }
}
