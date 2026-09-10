<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    protected $fillable = [
        'store_id',
        'invoice_number',
        'user_id',
        'subtotal',
        'discount',
        'total',
        'paid',
        'change',
        'payment_method',
    ];

    /**
     * Kasir yang melakukan transaksi.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Toko tempat transaksi dilakukan.
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Detail barang dalam transaksi.
     */
    public function items(): HasMany
    {
        return $this->hasMany(TransactionItem::class);
    }
}