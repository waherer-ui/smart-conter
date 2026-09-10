<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductHistory extends Model
{
    protected $fillable = [
        'store_id',
        'product_id',
        'user_id',
        'sku',
        'name',
        'added_stock',
        'status_type',
        'description',
    ];

    /**
     * Toko yang terkait dengan riwayat.
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Produk yang terkait dengan riwayat.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * User yang melakukan aktivitas.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}