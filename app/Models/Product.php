<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'sku',
        'name',
        'category',
        'brand',
        'capital_price',
        'price',
        'stock',
        'image',
    ];

    /**
     * Relasi produk dengan toko.
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function purchaseItems(): HasMany
{
    return $this->hasMany(PurchaseItem::class);
}
}