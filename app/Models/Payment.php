<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    protected $fillable = [
        'rental_id', 'periode', 'jumlah_dibayar',
        'tanggal_bayar', 'metode', 'jenis_pembayaran', 'catatan', 'recorded_by',
    ];

    protected $casts = [
        'tanggal_bayar'  => 'date',
        'jumlah_dibayar' => 'decimal:2',
    ];

    public function rental()
    {
        return $this->belongsTo(Rental::class);
    }

    public function recordedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'recorded_by');
    }
}
