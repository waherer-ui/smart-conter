<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportMessage extends Model
{
    protected $fillable = [
        'ticket_id',
        'user_id',
        'message',
    ];

    /*
    |--------------------------------------------------------------------------
    | TIKET
    |--------------------------------------------------------------------------
    */

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(
            SupportTicket::class,
            'ticket_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | PENGIRIM
    |--------------------------------------------------------------------------
    */

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }
}