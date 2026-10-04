<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionReturnItem extends Model
{
    protected $fillable = [
        'transaction_return_id',
        'transaction_item_id',
        'product_id',
        'product_name',
        'sku',
        'price',
        'quantity',
        'subtotal',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'quantity' => 'integer',
        'subtotal' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | RETUR PENJUALAN
    |--------------------------------------------------------------------------
    */

    public function transactionReturn(): BelongsTo
    {
        return $this->belongsTo(TransactionReturn::class);
    }

    /*
    |--------------------------------------------------------------------------
    | ITEM TRANSAKSI ASAL
    |--------------------------------------------------------------------------
    */

    public function transactionItem(): BelongsTo
    {
        return $this->belongsTo(TransactionItem::class);
    }

    /*
    |--------------------------------------------------------------------------
    | PRODUK
    |--------------------------------------------------------------------------
    */

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}