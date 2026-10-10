<?php

namespace App\Http\Controllers\AdminKasirku;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\AppNotification;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AnnouncementController extends Controller
{
    /**
     * Daftar pengumuman.
     */
    public function index()
    {
        $announcements = Announcement::with('creator')
            ->latest()
            ->paginate(15);

        return view(
            'admin-kasirku.announcements.index',
            compact('announcements')
        );
    }

    /**
     * Form membuat pengumuman.
     */
    public function create()
    {
        return view('admin-kasirku.announcements.create');
    }

    /**
     * Simpan pengumuman sebagai draf.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string', 'max:50000'],
            'target_audience' => [
                'required',
                Rule::in(['all', 'owners', 'free', 'pro', 'premium']),
            ],
        ]);

        $announcement = Announcement::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'target_audience' => $validated['target_audience'],
            'status' => 'draft',
            'created_by' => session('user_id'),
        ]);

        return redirect()
            ->route('admin-kasirku.announcements.index')
            ->with(
                'success',
                'Pengumuman berhasil disimpan sebagai draf.'
            );
    }

    /**
     * Form mengedit pengumuman.
     */
    public function edit(Announcement $announcement)
    {
        if ($announcement->status === 'archived') {
            return redirect()
                ->route('admin-kasirku.announcements.index')
                ->with(
                    'error',
                    'Pengumuman yang diarsipkan tidak dapat diedit.'
                );
        }

        return view(
            'admin-kasirku.announcements.edit',
            compact('announcement')
        );
    }

    /**
     * Perbarui pengumuman.
     */
    public function update(
        Request $request,
        Announcement $announcement
    ) {
        if ($announcement->status === 'archived') {
            return redirect()
                ->route('admin-kasirku.announcements.index')
                ->with(
                    'error',
                    'Pengumuman yang diarsipkan tidak dapat diubah.'
                );
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string', 'max:50000'],
            'target_audience' => [
                'required',
                Rule::in(['all', 'owners', 'free', 'pro', 'premium']),
            ],
        ]);

        $announcement->update($validated);

        return redirect()
            ->route('admin-kasirku.announcements.index')
            ->with('success', 'Pengumuman berhasil diperbarui.');
    }

/**
 * Terbitkan pengumuman dan buat notifikasi penerima.
 */
public function publish(Announcement $announcement)
{
    if ($announcement->status === 'archived') {
        return redirect()
            ->route('admin-kasirku.announcements.index')
            ->with(
                'error',
                'Pengumuman yang diarsipkan tidak dapat diterbitkan.'
            );
    }

    if ($announcement->status === 'published') {
        return redirect()
            ->route('admin-kasirku.announcements.index')
            ->with(
                'info',
                'Pengumuman ini sudah diterbitkan.'
            );
    }

    DB::transaction(function () use ($announcement) {
        $announcement->update([
            'status' => 'published',
            'published_at' => now(),
        ]);

        $recipients = $this->getRecipients($announcement);

        $url = route(
            'announcements.show',
            ['announcement' => $announcement->id],
            false
        );

        $now = now();

        $recipients->chunk(500)->each(
            function ($chunk) use ($announcement, $url, $now) {
                $notifications = $chunk->map(
                    function ($recipient) use ($announcement, $url, $now) {
                        return [
                            'user_id' => $recipient->id,
                            'type' => 'announcement_published',
                            'title' => '📢 ' . $announcement->title,
                            'message' => \Illuminate\Support\Str::limit(
                                strip_tags($announcement->content),
                                180
                            ),
                            'url' => $url,
                            'read_at' => null,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                )->all();

                if (!empty($notifications)) {
                    AppNotification::insert($notifications);
                }
            }
        );
    });

    return redirect()
        ->route('admin-kasirku.announcements.index')
        ->with(
            'success',
            'Pengumuman berhasil diterbitkan dan notifikasi telah dibuat.'
        );
}

    /**
     * Arsipkan pengumuman.
     */
    public function archive(Announcement $announcement)
    {
        $announcement->update([
            'status' => 'archived',
        ]);

        return redirect()
            ->route('admin-kasirku.announcements.index')
            ->with('success', 'Pengumuman berhasil diarsipkan.');
    }

/**
 * Tentukan penerima berdasarkan target.
 *
 * Semua Pengguna:
 * Pemilik toko dan staf kasir yang terhubung ke toko.
 *
 * Pemilik Toko:
 * Akun pemilik yang memiliki toko.
 *
 * Target paket:
 * Pemilik toko dengan langganan aktif dan belum kedaluwarsa.
 */
private function getRecipients(Announcement $announcement)
{
    $baseUsers = User::query()
        ->where(function ($query) {
            $query->whereNull('is_platform_admin')
                ->orWhere('is_platform_admin', false);
        });

    // Semua pengguna toko, termasuk staf kasir.
    if ($announcement->target_audience === 'all') {
        return (clone $baseUsers)
            ->whereIn('role', ['admin', 'kasir'])
            ->whereHas('stores')
            ->select('id')
            ->get();
    }

    // Akun pemilik toko saja.
    $owners = (clone $baseUsers)
        ->where('role', 'admin')
        ->whereHas('ownedStores');

    if ($announcement->target_audience === 'owners') {
        return $owners->select('id')->get();
    }

    // Target paket Free, Pro, atau Premium.
    $plan = Plan::where(
        'slug',
        $announcement->target_audience
    )->firstOrFail();

    $ownerIds = Subscription::query()
        ->where('plan_id', $plan->id)
        ->where('status', 'active')
        ->where(function ($query) {
            $query->whereNull('ends_at')
                ->orWhere('ends_at', '>=', now());
        })
        ->pluck('owner_id');

    return $owners
        ->whereIn('id', $ownerIds)
        ->select('id')
        ->get();
}
}