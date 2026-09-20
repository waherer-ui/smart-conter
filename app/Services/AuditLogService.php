<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Store;
use Illuminate\Database\Eloquent\Model;

class AuditLogService
{
    public static function log(
        string $action,
        string $description,
        ?Model $subject = null,
        ?int $ownerId = null,
        ?int $storeId = null,
        ?array $oldValues = null,
        ?array $newValues = null
    ): AuditLog {
        $userId = session('user_id');

        // Jika store_id tidak diberikan, gunakan toko aktif
        $storeId = $storeId ?? session('active_store_id');

        // Jika owner belum diberikan, ambil dari pemilik toko
        if (!$ownerId && $storeId) {
            $store = Store::find($storeId);

            if ($store) {
                $ownerId = $store->owner_id;
            }
        }

        return AuditLog::create([
            'user_id' => $userId,
            'owner_id' => $ownerId,
            'store_id' => $storeId,

            'action' => $action,
            'description' => $description,

            'subject_type' => $subject
                ? get_class($subject)
                : null,

            'subject_id' => $subject?->getKey(),

            'old_values' => $oldValues,
            'new_values' => $newValues,

            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}