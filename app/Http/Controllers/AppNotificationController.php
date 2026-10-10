<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use Illuminate\Http\Request;

class AppNotificationController extends Controller
{
    /**
     * Daftar notifikasi milik pengguna yang sedang login.
     */
    public function index()
    {
        $userId = session('user_id');

        $notifications = AppNotification::where('user_id', $userId)
            ->latest()
            ->paginate(20);

        $unreadCount = AppNotification::where('user_id', $userId)
            ->whereNull('read_at')
            ->count();

        return view('notifications.index', compact(
            'notifications',
            'unreadCount'
        ));
    }

    /**
     * Tandai notifikasi milik pengguna sebagai sudah dibaca.
     */
    public function read(AppNotification $notification)
    {
        abort_unless(
            $notification->user_id == session('user_id'),
            403
        );

        if ($notification->read_at === null) {
            $notification->update([
                'read_at' => now(),
            ]);
        }

        if (
            $notification->url &&
            str_starts_with($notification->url, '/') &&
            !str_starts_with($notification->url, '//')
        ) {
            return redirect($notification->url);
        }

        return redirect()->route('notifications.index');
    }

    /**
     * Tandai seluruh notifikasi pengguna sebagai sudah dibaca.
     */
    public function readAll()
    {
        AppNotification::where('user_id', session('user_id'))
            ->whereNull('read_at')
            ->update([
                'read_at' => now(),
            ]);

        return redirect()
            ->route('notifications.index')
            ->with('success', 'Semua notifikasi telah dibaca.');
    }
}