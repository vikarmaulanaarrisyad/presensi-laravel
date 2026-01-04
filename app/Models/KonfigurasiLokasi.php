<?php

namespace App\Models;

class KonfigurasiLokasi extends Model
{
    public function departemen()
    {
        return $this->belongsTo(Departemen::class);
    }

    public function getLatLngAttribute()
    {
        if (!$this->lokasi_kantor) return null;

        [$lat, $lng] = explode(',', $this->lokasi_kantor);
        return [
            'lat' => trim($lat),
            'lng' => trim($lng),
        ];
    }
}
