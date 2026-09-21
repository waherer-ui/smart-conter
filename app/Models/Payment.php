<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Payment extends Model
{
    protected $fillable = [
        'owner_id',
        'plan_id',
        'amount',
        'payment_method',
        'status',
        'reference',
        'paid_at',
        'duration_months',
        'referral_code',
'referral_discount_percent',
'referral_discount_amount',
'midtrans_order_id',
'midtrans_snap_token',
'midtrans_transaction_status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'duration_months' => 'integer',
        'referral_discount_percent' => 'integer',
'referral_discount_amount' => 'decimal:2',
    ];

    /**
     * Owner yang melakukan pembayaran.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Paket yang dibayar.
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }
    
    public function referralReward(): HasOne
{
    return $this->hasOne(
        ReferralReward::class,
        'payment_id'
    );
}
}