<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
    'branch_id',
    'room_number',
    'floor',
    'room_type',
    'price_monthly',
    'status',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function rentals(): HasMany
    {
        return $this->hasMany(Rental::class);
    }

    public function tenant()
    {
        return $this->hasOneThrough(Tenant::class, Rental::class, 'room_id', 'id', 'id', 'tenant_id');
    }
}
