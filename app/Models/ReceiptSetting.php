<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReceiptSetting extends Model
{
    protected $fillable = [
        'store_id',
        'business_name',
        'address',
        'phone',
        'email',
        'header_text',
        'logo',
        'footer_text',
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
}