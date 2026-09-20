<?php

namespace App\Http\Controllers;

use App\Models\SupportMessage;
use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Store;
use App\Services\AuditLogService;

class SupportController extends Controller
{
    /**
     * Daftar tiket milik owner yang sedang login.
     */
    public function index()
    {
        $userId = session('user_id');

        $tickets = SupportTicket::with('store')
            ->where('owner_id', $userId)
            ->latest()
            ->get();

        return view(
            'support.index',
            compact('tickets')
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
                'string',
                'max:100',
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