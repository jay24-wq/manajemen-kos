<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
    'name',
    'address',
    'notes',
    ];

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }
}
