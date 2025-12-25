<?php

namespace App\Models;

class Guru extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getEmailAttribute()
    {
        return $this->user->email ?? null;
    }
}
