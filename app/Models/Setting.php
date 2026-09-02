<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'store_name',
        'store_address',
        'store_phone',
        'store_email',
        'currency',
        'allow_discount',
        'max_discount',
        'minimum_stock',
        'receipt_footer',
        'show_cashier',
        'show_payment_method',
        'show_discount',
    ];

    protected $casts = [
        'allow_discount' => 'boolean',
        'max_discount' => 'decimal:2',
        'minimum_stock' => 'integer',
        'show_cashier' => 'boolean',
        'show_payment_method' => 'boolean',
        'show_discount' => 'boolean',
    ];
}