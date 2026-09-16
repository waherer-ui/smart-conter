<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = [
        'store_id',
        'name',
        'phone',
        'address',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function debts(): HasMany
    {
        return $this->hasMany(Debt::class);
    }
}