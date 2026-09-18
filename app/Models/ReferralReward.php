<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReferralReward extends Model
{
    protected $fillable = [
        'referral_id',
        'payment_id',
        'owner_id',
        'reward_months',
        'status',
    ];

    protected $casts = [
        'reward_months' => 'integer',
    ];

    /**
     * Referral yang menghasilkan reward.
     */
    public function referral(): BelongsTo
    {
        return $this->belongsTo(
            Referral::class,
            'referral_id'
        );
    }

    /**
     * Payment yang menghasilkan reward.
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(
            Payment::class,
            'payment_id'
        );
    }

    /**
     * Owner penerima reward.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'owner_id'
        );
    }
}