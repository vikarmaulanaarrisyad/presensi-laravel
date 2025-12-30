<?php

namespace App\Models;

class Presensi extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
