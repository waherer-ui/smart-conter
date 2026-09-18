<?php

namespace App\Models;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Purchase;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Store extends Model
{
    protected $fillable = [
        'owner_id',
        'name',
        'address',
        'phone',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role')
            ->withTimestamps();
    }

public function currentPlan()
{
    $subscription = $this->owner?->subscription;

    if (!$subscription || !$subscription->isActive()) {
        return Plan::where('slug', 'free')->first();
    }

    return $subscription->plan;
}

public function hasFeature(string $featureSlug): bool
{
    $plan = $this->currentPlan();

    if (!$plan) {
        return false;
    }

    return $plan->features()
        ->where('slug', $featureSlug)
        ->where('is_active', true)
        ->exists();
}

public function getLimit(string $key): ?int
{
    $plan = $this->currentPlan();

    if (!$plan) {
        return 0;
    }

    return $plan->limits()
        ->where('key', $key)
        ->value('value');
}

public function products(): HasMany
{
    return $this->hasMany(Product::class);
}

public function canAddProduct(): bool
{
    $limit = $this->getLimit('max_products');

    // NULL berarti unlimited
    if ($limit === null) {
        return true;
    }

    return $this->products()->count() < $limit;
}

public function canAddStaff(): bool
{
    $limit = $this->getLimit('max_staff');

    // NULL berarti unlimited
    if ($limit === null) {
        return true;
    }

    return $this->users()->count() < $limit;
}

public function customers()
{
    return $this->hasMany(Customer::class);
}

public function suppliers()
{
    return $this->hasMany(Supplier::class);
}

public function purchases(): HasMany
{
    return $this->hasMany(Purchase::class);
}

public function canAddCustomer(): bool
{
    $limit = $this->getLimit('max_customers');

    // NULL berarti unlimited
    if ($limit === null) {
        return true;
    }

    return $this->customers()->count() < $limit;
}
}