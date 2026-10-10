<?php

namespace App\Http\Controllers;

use App\Models\SupportMessage;
use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Store;
use App\Services\AuditLogService;
use App\Models\AppNotification;
use App\Models\User;
use App\Models\Announcement;
use App\Models\TutorialVideo;

class SupportController extends Controller
{
/**
 * Halaman Pusat Bantuan.
 */
public function index()
{
    $userId = session('user_id');

    $user = User::findOrFail($userId);

    // Daftar tiket bantuan milik pengguna.
    $tickets = SupportTicket::with('store')
        ->where('owner_id', $userId)
        ->latest()
        ->get();

    // Pengumuman yang sudah diterbitkan.
    $announcements = Announcement::published()
        ->latest('published_at')
        ->get()
        ->filter(function ($announcement) use ($user) {

            // Semua pengguna toko.
            if ($announcement->target_audience === 'all') {
                return in_array($user->role, ['admin', 'kasir'], true)
                    && $user->stores()->exists();
            }

            // Khusus pemilik toko.
            if ($announcement->target_audience === 'owners') {
                return $user->role === 'admin'
                    && $user->ownedStores()->exists();
            }

            // Khusus paket Free, Pro, atau Premium.
            if (in_array(
                $announcement->target_audience,
                ['free', 'pro', 'premium'],
                true
            )) {
                if (
                    $user->role !== 'admin'
                    || !$user->ownedStores()->exists()
                ) {
                    return false;
                }

                return $user->subscription()
                    ->where('status', 'active')
                    ->where(function ($query) {
                        $query->whereNull('ends_at')
                            ->orWhere('ends_at', '>=', now());
                    })
                    ->whereHas('plan', function ($query) use ($announcement) {
                        $query->where(
                            'slug',
                            $announcement->target_audience
                        );
                    })
                    ->exists();
            }

            return false;
        })
        ->values();
        
        // Video tutorial aktif untuk Pusat Bantuan.
$tutorialVideos = TutorialVideo::query()
    ->where('is_active', true)
    ->orderBy('category')
    ->orderBy('sort_order')
    ->orderBy('title')
    ->get()
    ->groupBy('category');

    return view(
    'support.index',
    compact('tickets', 'announcements', 'tutorialVideos')
);
}

    /**
     * Form membuat tiket baru.
     */
    public function create()
    {
        $userId = session('user_id');

        $stores = Store::whereHas(
            'users',
            function ($query) use ($userId) {
                $query->where(
                    'users.id',
                    $userId
                );
            }
        )
        ->orderBy('id')
        ->get();

        return view(
            'support.create',
            compact('stores')
        );
    }

    /**
     * Simpan tiket baru.
     */
    public function store(Request $request)
    {
        $userId = session('user_id');

        $validated = $request->validate([
            'store_id' => [
                'required',
                'integer',
            ],

            'category' => [
                'required',
                'in:akun,teknis,pembayaran,langganan,fitur,lainnya',
            ],

            'subject' => [
                'required',
                'string',
                'max:255',
            ],

            'priority' => [
                'required',
                'in:low,normal,high,urgent',
            ],

            'description' => [
                'required',
                'string',
                 'max:5000',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | PASTIKAN TOKO MILIK / DAPAT DIAKSES OWNER
        |--------------------------------------------------------------------------
        */

        $store = Store::where(
            'id',
            $validated['store_id']
        )
        ->whereHas(
            'users',
            function ($query) use ($userId) {

                $query->where(
                    'users.id',
                    $userId
                );
            }
        )
        ->first();

        if (!$store) {

            return back()
                ->withInput()
                ->withErrors([
                    'store_id' =>
                        'Toko yang dipilih tidak valid.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | BUAT TIKET
        |--------------------------------------------------------------------------
        */

        $ticket = SupportTicket::create([
            'owner_id' => $userId,
            'store_id' => $store->id,
            'ticket_number' =>
                $this->generateTicketNumber(),
            'subject' =>
                $validated['subject'],
            'category' =>
                $validated['category'],
            'priority' =>
                $validated['priority'],
            'status' =>
                'open',
            'description' =>
                $validated['description'],
        ]);

        /*
        |--------------------------------------------------------------------------
        | PESAN PERTAMA
        |--------------------------------------------------------------------------
        |
        | Pesan pertama otomatis menjadi bagian
        | dari percakapan tiket.
        |--------------------------------------------------------------------------
        */

        SupportMessage::create([
            'ticket_id' =>
                $ticket->id,

            'user_id' =>
                $userId,

            'message' =>
                $validated['description'],
        ]);

        /*
        |--------------------------------------------------------------------------
        | AUDIT LOG - TIKET DIBUAT
        |--------------------------------------------------------------------------
        */

        AuditLogService::log(
            'support_created',
            'Membuat tiket bantuan "' .
            $ticket->ticket_number .
            '" dengan subjek "' .
            $ticket->subject .
            '".',
            $ticket,
            $userId,
            $store->id,
            null,
            [
                'ticket_number' =>
                    $ticket->ticket_number,

                'subject' =>
                    $ticket->subject,

                'category' =>
                    $ticket->category,

                'priority' =>
                    $ticket->priority,

                'status' =>
                    $ticket->status,

                'description' =>
                    $ticket->description,
            ]
        );
        
        /*
|--------------------------------------------------------------------------
| NOTIFIKASI SUPER ADMIN - TIKET BARU
|--------------------------------------------------------------------------
*/

$platformAdmins = User::where(
    'is_platform_admin',
    true
)->get(['id']);

foreach ($platformAdmins as $admin) {
    AppNotification::create([
        'user_id' => $admin->id,
        'type' => 'support_ticket_created',
        'title' => 'Tiket Bantuan Baru',
        'message' => 'Tiket '
            . $ticket->ticket_number
            . ' dibuat oleh pemilik toko. Subjek: '
            . $ticket->subject,
        'url' => '/admin-kasirku/bantuan/'
            . $ticket->id,
    ]);
}

        return redirect()
            ->route(
                'support.show',
                $ticket
            )
            ->with(
                'success',
                'Tiket bantuan berhasil dibuat.'
            );
    }

    /**
     * Detail tiket milik owner.
     */
    public function show(SupportTicket $ticket)
    {
        $userId = session('user_id');

        abort_unless(
            $ticket->owner_id == $userId,
            403
        );

        $ticket->load([
            'store',
            'messages.user',
        ]);

        return view(
            'support.show',
            compact('ticket')
        );
    }

    /**
     * Owner mengirim pesan tambahan.
     */
    public function message(
        Request $request,
        SupportTicket $ticket
    ) {
        $userId = session('user_id');

        abort_unless(
            $ticket->owner_id == $userId,
            403
        );
        
        if ($ticket->status === 'closed') {
    return back()->with(
        'error',
        'Tiket sudah ditutup. Silakan buat tiket baru jika membutuhkan bantuan kembali.'
    );
}

        $validated = $request->validate([
            'message' => [
                'required',
                'string',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | SIMPAN PESAN
        |--------------------------------------------------------------------------
        */

        $message = SupportMessage::create([
            'ticket_id' =>
                $ticket->id,

            'user_id' =>
                $userId,

            'message' =>
                $validated['message'],
        ]);

        /*
        |--------------------------------------------------------------------------
        | JIKA STATUS WAITING
        |--------------------------------------------------------------------------
        |
        | Ketika owner membalas tiket yang sedang menunggu,
        | status kembali menjadi open.
        |--------------------------------------------------------------------------
        */

        if ($ticket->status === 'waiting') {

            $oldStatus =
                $ticket->status;

            $ticket->update([
                'status' =>
                    'open',

                'closed_at' =>
                    null,
            ]);

            /*
            |--------------------------------------------------------------------------
            | AUDIT LOG - STATUS KEMBALI OPEN
            |--------------------------------------------------------------------------
            */

            AuditLogService::log(
                'support_status_updated',
                'Mengubah status tiket "' .
                $ticket->ticket_number .
                '" dari "' .
                ucfirst($oldStatus) .
                '" menjadi "Open" setelah owner mengirim pesan.',
                $ticket,
                $userId,
                $ticket->store_id,
                [
                    'status' =>
                        $oldStatus,
                ],
                [
                    'status' =>
                        'open',
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | AUDIT LOG - OWNER MEMBALAS
        |--------------------------------------------------------------------------
        */

        AuditLogService::log(
            'support_replied',
            'Mengirim pesan pada tiket bantuan "' .
            $ticket->ticket_number .
            '".',
            $message,
            $userId,
            $ticket->store_id,
            null,
            [
                'ticket_id' =>
                    $ticket->id,

                'ticket_number' =>
                    $ticket->ticket_number,

                'message' =>
                    $message->message,
            ]
        );
        
        /*
|--------------------------------------------------------------------------
| NOTIFIKASI SUPER ADMIN - BALASAN OWNER
|--------------------------------------------------------------------------
*/

$platformAdmins = User::where(
    'is_platform_admin',
    true
)->get(['id']);

foreach ($platformAdmins as $admin) {
    AppNotification::create([
        'user_id' => $admin->id,
        'type' => 'support_owner_reply',
        'title' => 'Balasan Pemilik Toko',
        'message' => 'Ada pesan baru pada tiket '
            . $ticket->ticket_number
            . ': '
            . $ticket->subject,
        'url' => '/admin-kasirku/bantuan/'
            . $ticket->id,
    ]);
}

        return back()
            ->with(
                'success',
                'Pesan berhasil dikirim.'
            );
    }

    /**
     * Generate nomor tiket.
     */
    private function generateTicketNumber(): string
    {
        do {

            $number =
                'TK-' .
                now()->format('Ymd') .
                '-' .
                strtoupper(
                    Str::random(5)
                );

        } while (
            SupportTicket::where(
                'ticket_number',
                $number
            )->exists()
        );

        return $number;
    }
}