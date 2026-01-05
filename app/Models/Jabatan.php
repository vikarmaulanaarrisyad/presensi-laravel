<?php

namespace App\Models;

class Jabatan extends Model
{
    public function guru()
    {
        return $this->hasMany(Guru::class);
    }
}
