<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    protected $fillable = [
        'store_id',
        'user_id',
        'category',
        'description',
        'amount',
        'expense_date',
    ];

    /**
     * Casting data.
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
    ];

    /**
     * Toko tempat pengeluaran dicatat.
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * User yang mencatat pengeluaran.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}