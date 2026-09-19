<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;


#[Fillable([
    'name',
    'email',
    'password',
    'role',
    'is_platform_admin',
])]

#[Hidden([
    'password',
    'remember_token',
])]

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Relasi ke riwayat aktivitas produk.
     */
    public function productHistories()
    {
        return $this->hasMany(ProductHistory::class);
    }
    /**
     * Relasi ke transaksi penjualan.
     */
    public function transactions()
    {
    return $this->hasMany(Transaction::class);
    }
    
          /**
       * Relasi user dengan toko.
       */
      public function stores()
      {
          return $this->belongsToMany(Store::class)
              ->withPivot('role')
              ->withTimestamps();
      }
      
      public function subscription(): HasOne
{
    return $this->hasOne(Subscription::class, 'owner_id');
}

public function ownedStores()
{
    return $this->hasMany(Store::class, 'owner_id');
}

public function referral(): HasOne
{
    return $this->hasOne(Referral::class, 'owner_id');
}
    /**
     * Attribute casting.
     */
    protected function casts(): array
{
    return [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_platform_admin' => 'boolean',
    ];
}
}