<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RentalRenewal extends Model
{
    protected $fillable = [
        'rental_id', 
        'durasi_before', 
        'durasi_after', 
        'catatan', 
        'tanggal'
    ];

    protected $casts = [
        'tanggal' => 'datetime',
    ];

    public function rental()
    {
        return $this->belongsTo(Rental::class);
    }
}
