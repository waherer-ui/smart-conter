<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    protected $fillable = [
        'owner_id',
        'plan_id',
        'starts_at',
        'ends_at',
        'status',
        'duration_months',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'duration_months' => 'integer',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Cek apakah subscription masih aktif.
     */
    public function isActive(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        // Jika ends_at null, berarti tidak memiliki batas waktu.
        if ($this->ends_at === null) {
            return true;
        }

        return now()->lessThanOrEqualTo($this->ends_at);
    }
}