<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnnouncementPublicController extends Controller
{
    /**
     * Tampilkan pengumuman kepada pengguna yang berhak.
     */
    public function show(Request $request, Announcement $announcement)
    {
        $userId = session('user_id');

        if (!$userId) {
            abort(403, 'Silakan masuk terlebih dahulu.');
        }

        // Hanya pengumuman yang sudah diterbitkan yang bisa dibaca.
        if (!$announcement->isPublished()) {
            abort(404);
        }

        // Ambil pengguna yang sedang login.
        $user = \App\Models\User::findOrFail($userId);

        // Admin platform tidak menggunakan halaman pengumuman pengguna toko.
        if ($user->is_platform_admin) {
            abort(403, 'Halaman ini ditujukan untuk pengguna toko.');
        }

        // Semua Pengguna: pemilik toko dan staf kasir yang terhubung ke toko.
        if ($announcement->target_audience === 'all') {
            $hasStore = $user->stores()->exists();

            abort_unless($hasStore, 403, 'Kamu tidak memiliki akses ke pengumuman ini.');
        }

        // Pemilik Toko: akun pemilik yang mempunyai toko.
        elseif ($announcement->target_audience === 'owners') {
            abort_unless(
                $user->role === 'admin' && $user->ownedStores()->exists(),
                403,
                'Pengumuman ini hanya untuk pemilik toko.'
            );
        }

        // Pengumuman khusus paket.
        elseif (in_array($announcement->target_audience, ['free', 'pro', 'premium'], true)) {
            abort_unless(
                $user->role === 'admin' && $user->ownedStores()->exists(),
                403,
                'Pengumuman paket hanya dapat dibaca oleh pemilik toko.'
            );

            $hasActivePlan = Subscription::query()
                ->where('owner_id', $user->id)
                ->where('status', 'active')
                ->where(function ($query) {
                    $query->whereNull('ends_at')
                        ->orWhere('ends_at', '>=', now());
                })
                ->whereHas('plan', function ($query) use ($announcement) {
                    $query->where('slug', $announcement->target_audience);
                })
                ->exists();

            abort_unless(
                $hasActivePlan,
                403,
                'Pengumuman ini tidak tersedia untuk paket langganan kamu.'
            );
        } else {
            abort(404);
        }

        // Tandai notifikasi terkait sebagai sudah dibaca.
        DB::table('app_notifications')
            ->where('user_id', $user->id)
            ->where('type', 'announcement_published')
            ->where('url', route(
                'announcements.show',
                ['announcement' => $announcement->id],
                false
            ))
            ->whereNull('read_at')
            ->update([
                'read_at' => now(),
                'updated_at' => now(),
            ]);

        return view(
            'announcements.show',
            compact('announcement')
        );
    }
}