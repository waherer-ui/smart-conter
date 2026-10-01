<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $fillable = [
        'store_id',
        'user_id',
        'date',
        'check_in',
        'check_out',
        'latitude',
        'longitude',
        'photo',
        'status',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'check_in' => 'datetime',
        'check_out' => 'datetime',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    /**
     * Toko tempat absensi dilakukan.
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * User/staf yang melakukan absensi.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}