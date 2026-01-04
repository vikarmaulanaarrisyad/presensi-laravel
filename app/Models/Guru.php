<?php

namespace App\Models;

class Guru extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function departemen()
    {
        return $this->belongsTo(Departemen::class);
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class);
    }

    public function getEmailAttribute()
    {
        return $this->user->email ?? null;
    }
}
