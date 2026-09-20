<?php

namespace App\Http\Controllers\AdminKasirku;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with([
            'user',
            'owner',
            'store',
        ])->latest();

        // Pencarian
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('action', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($user) use ($search) {
                        $user->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        // Filter owner
        if ($request->filled('owner_id')) {
            $query->where('owner_id', $request->owner_id);
        }

        // Filter toko
        if ($request->filled('store_id')) {
            $query->where('store_id', $request->store_id);
        }

        // Filter jenis aktivitas
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter tanggal
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $logs = $query
            ->paginate(25)
            ->withQueryString();

        $owners = User::whereHas('stores')
            ->orderBy('name')
            ->get();

        $stores = Store::with('owner')
            ->orderBy('name')
            ->get();

        $actions = AuditLog::query()
            ->select('action')
            ->whereNotNull('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        return view('admin-kasirku.audit-log.index', compact(
            'logs',
            'owners',
            'stores',
            'actions'
        ));
    }
}