<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Referral extends Model
{
    protected $fillable = [
        'owner_id',
        'code',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(
            Payment::class,
            'referral_code',
            'code'
        );
    }
    
    public function rewards(): HasMany
{
    return $this->hasMany(
        ReferralReward::class,
        'referral_id'
    );
}
}