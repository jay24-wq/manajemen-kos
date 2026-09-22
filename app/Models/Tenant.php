<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
    'name',
    'phone',
    'email',
    'ktp_number',
    'emergency_contact',
    ];

    protected $cast = [
    'emergency_contact' => 'array',
    ];

    public function rentals(): HasMany
    {
        return $this->hasMany(Rental::class);
    }
}
