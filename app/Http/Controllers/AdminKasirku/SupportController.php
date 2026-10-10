<?php

namespace App\Http\Controllers\AdminKasirku;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use App\Models\AppNotification;

class SupportController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | DAFTAR TIKET
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $query = SupportTicket::with([
            'owner',
            'store',
            'assignedTo',
        ]);

        /*
        |--------------------------------------------------------------------------
        | FILTER STATUS
        |--------------------------------------------------------------------------
        */

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        /*
        |--------------------------------------------------------------------------
        | FILTER KATEGORI
        |--------------------------------------------------------------------------
        */

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        /*
        |--------------------------------------------------------------------------
        | PENCARIAN
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = trim($request->search);

            $query->where(function ($q) use ($search) {

                $q->where('ticket_number', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhereHas('owner', function ($owner) use ($search) {

                        $owner->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");

                    });

            });
        }

        $tickets = $query
            ->orderByRaw("
                CASE priority
                    WHEN 'urgent' THEN 1
                    WHEN 'high' THEN 2
                    WHEN 'normal' THEN 3
                    WHEN 'low' THEN 4
                    ELSE 5
                END
            ")
            ->latest()
            ->paginate(20)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | STATISTIK
        |--------------------------------------------------------------------------
        */

        $stats = [
            'total' => SupportTicket::count(),

            'open' => SupportTicket::where(
                'status',
                'open'
            )->count(),

            'processing' => SupportTicket::where(
                'status',
                'processing'
            )->count(),

            'waiting' => SupportTicket::where(
                'status',
                'waiting'
            )->count(),

            'resolved' => SupportTicket::where(
                'status',
                'resolved'
            )->count(),
        ];

        /*
        |--------------------------------------------------------------------------
        | ADMIN / STAFF SUPPORT
        |--------------------------------------------------------------------------
        */

        $admins = User::where(function ($query) {

            $query->where('is_platform_admin', true)
                ->orWhere('role', 'admin');

        })
        ->orderBy('name')
        ->get();

        /*
|--------------------------------------------------------------------------
| NOTIFIKASI PUSAT BANTUAN SUPER ADMIN
|--------------------------------------------------------------------------
*/

$notifications = AppNotification::whereIn('type', [
    'support_ticket_created',
    'support_owner_reply',
])
->whereNull('read_at')
->where('user_id', session('user_id'))
->latest()
->take(10)
->get();

return view(
    'admin-kasirku.support.index',
    compact(
        'tickets',
        'stats',
        'admins',
        'notifications'
    )
);
    }


    /*
    |--------------------------------------------------------------------------
    | DETAIL TIKET
    |--------------------------------------------------------------------------
    */

    public function show(SupportTicket $ticket)
    {
        $ticket->load([
            'owner',
            'store',
            'assignedTo',
            'messages.user',
        ]);
        
        /*
|--------------------------------------------------------------------------
| TANDAI NOTIFIKASI TIKET SEBAGAI SUDAH DIBACA
|--------------------------------------------------------------------------
*/

\App\Models\AppNotification::where(
    'user_id',
    session('user_id')
)
->whereIn('type', [
    'support_ticket_created',
    'support_owner_reply',
])
->where(
    'url',
    '/admin-kasirku/bantuan/' . $ticket->id
)
->whereNull('read_at')
->update([
    'read_at' => now(),
]);

        $admins = User::where(function ($query) {

            $query->where('is_platform_admin', true)
                ->orWhere('role', 'admin');

        })
        ->orderBy('name')
        ->get();

        return view(
            'admin-kasirku.support.show',
            compact(
                'ticket',
                'admins'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE STATUS
    |--------------------------------------------------------------------------
    */

public function updateStatus(
Request $request,
SupportTicket $ticket
) {
$validated = $request->validate([
'status' => [
'required',
'in:open,processing,waiting,resolved,closed',
],
]);

$oldStatus = $ticket->status;
$newStatus = $validated['status'];

// Jika status tidak berubah, tidak perlu memproses ulang.
if ($oldStatus === $newStatus) {
    return back()->with(
        'success',
        'Status tiket tidak mengalami perubahan.'
    );
}

$ticket->update([
    'status' => $newStatus,
    'closed_at' => in_array(
        $newStatus,
        ['resolved', 'closed'],
        true
    ) ? now() : null,
]);

// Catat perubahan status ke audit log.
AuditLogService::log(
    'support_status_updated',
    'Status tiket bantuan ' . $ticket->ticket_number
        . ' diubah dari ' . $oldStatus
        . ' menjadi ' . $newStatus,
    $ticket,
    session('user_id'),
    $ticket->store_id,
    ['status' => $oldStatus],
    ['status' => $newStatus]
);

// Beri tahu pemilik toko bahwa status tiket berubah.
\App\Models\AppNotification::create([
    'user_id' => $ticket->owner_id,
    'type' => 'support_status_updated',
    'title' => 'Status Tiket Diperbarui',
    'message' => 'Status tiket '
        . $ticket->ticket_number
        . ' berubah menjadi '
        . ucfirst($newStatus)
        . '.',
    'url' => route('support.show', $ticket, false),
]);

return back()->with(
    'success',
    'Status tiket berhasil diperbarui.'
);

}


    /*
    |--------------------------------------------------------------------------
    | ASSIGN ADMIN
    |--------------------------------------------------------------------------
    */

    public function assign(
    Request $request,
    SupportTicket $ticket
) {
    $validated = $request->validate([
        'assigned_to' => [
            'nullable',
            'integer',
            'exists:users,id',
        ],
    ]);

    $newAssigneeId = $validated['assigned_to'] ?? null;

    // Pastikan pengguna yang dipilih benar-benar admin.
    if (
        $newAssigneeId !== null &&
        !User::where('id', $newAssigneeId)
            ->where(function ($query) {
                $query->where('is_platform_admin', true)
                    ->orWhere('role', 'admin');
            })
            ->exists()
    ) {
        return back()
            ->withErrors([
                'assigned_to' => 'Penanggung jawab harus merupakan admin yang valid.',
            ])
            ->withInput();
    }

    $oldAssigneeId = $ticket->assigned_to;

    // Tidak perlu memproses jika penanggung jawab tidak berubah.
    if ((string) $oldAssigneeId === (string) $newAssigneeId) {
        return back()->with(
            'success',
            'Penanggung jawab tiket tidak mengalami perubahan.'
        );
    }

    $ticket->update([
        'assigned_to' => $newAssigneeId,
    ]);

    // Catat perubahan penanggung jawab pada audit log.
    AuditLogService::log(
        'support_assigned',
        'Penanggung jawab tiket '
            . $ticket->ticket_number
            . ' berhasil diperbarui.',
        $ticket,
        session('user_id'),
        $ticket->store_id,
        ['assigned_to' => $oldAssigneeId],
        ['assigned_to' => $newAssigneeId]
    );

    // Beri tahu pemilik toko bahwa tiketnya ditugaskan.
    \App\Models\AppNotification::create([
        'user_id' => $ticket->owner_id,
        'type' => 'support_assigned',
        'title' => 'Penanggung Jawab Tiket Diperbarui',
        'message' => $newAssigneeId === null
            ? 'Penugasan tiket ' . $ticket->ticket_number
                . ' telah dihapus.'
            : 'Penanggung jawab tiket '
                . $ticket->ticket_number
                . ' telah diperbarui.',
        'url' => route('support.show', $ticket, false),
    ]);

    return back()->with(
        'success',
        'Penanggung jawab tiket berhasil diperbarui.'
    );
}


    /*
    |--------------------------------------------------------------------------
    | BALAS TIKET
    |--------------------------------------------------------------------------
    */

public function reply(
Request $request,
SupportTicket $ticket
) {
// Tiket yang sudah ditutup tidak bisa dibalas.
if ($ticket->status === 'closed') {
return back()->with(
'error',
'Tiket sudah ditutup. Silakan buka tiket baru jika membutuhkan bantuan kembali.'
);
}

$validated = $request->validate([
    'message' => [
        'required',
        'string',
        'max:5000',
    ],
]);

$userId = session('user_id');

$message = $ticket->messages()->create([
    'user_id' => $userId,
    'message' => $validated['message'],
]);

// Catat balasan admin pada audit log.
AuditLogService::log(
    'support_replied',
    'Admin membalas tiket bantuan ' . $ticket->ticket_number,
    $message,
    $userId,
    $ticket->store_id,
    null,
    [
        'ticket_id' => $ticket->id,
        'ticket_number' => $ticket->ticket_number,
    ]
);

// Kirim notifikasi kepada pemilik tiket.
\App\Models\AppNotification::create([
    'user_id' => $ticket->owner_id,
    'type' => 'support_reply',
    'title' => 'Balasan Pusat Bantuan',
    'message' => 'Admin telah membalas tiket '
        . $ticket->ticket_number
        . ': '
        . $ticket->subject,
    'url' => route('support.show', $ticket, false),
]);

// Tiket baru otomatis menjadi sedang diproses.
if ($ticket->status === 'open') {
    $ticket->update([
        'status' => 'processing',
    ]);
}

return back()->with(
    'success',
    'Balasan berhasil dikirim.'
);

}
}