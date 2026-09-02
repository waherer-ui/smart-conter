<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductHistory extends Model
{
    protected $fillable = [
        'product_id',
        'user_id',
        'sku',
        'name',
        'added_stock',
        'status_type',
        'description',
    ];

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