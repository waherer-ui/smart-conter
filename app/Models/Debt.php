<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Debt extends Model
{
    protected $fillable = [
        'store_id',
        'customer_id',
        'user_id',
        'description',
        'amount',
        'paid_amount',
        'debt_date',
        'due_date',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'debt_date' => 'date',
        'due_date' => 'date',
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payments(): HasMany
{
    return $this->hasMany(DebtPayment::class);
}

    public function getRemainingAmountAttribute(): float
    {
        return max(
            0,
            (float) $this->amount - (float) $this->paid_amount
        );
    }
}