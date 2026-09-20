<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupportTicket extends Model
{
    protected $fillable = [
        'owner_id',
        'store_id',
        'assigned_to',
        'ticket_number',
        'subject',
        'category',
        'priority',
        'status',
        'description',
        'closed_at',
    ];

    protected $casts = [
        'closed_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | OWNER
    |--------------------------------------------------------------------------
    */

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /*
    |--------------------------------------------------------------------------
    | TOKO
    |--------------------------------------------------------------------------
    */

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    /*
    |--------------------------------------------------------------------------
    | ADMIN YANG MENANGANI
    |--------------------------------------------------------------------------
    */

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /*
    |--------------------------------------------------------------------------
    | PESAN / PERCAKAPAN
    |--------------------------------------------------------------------------
    */

    public function messages(): HasMany
    {
        return $this->hasMany(SupportMessage::class, 'ticket_id');
    }
}