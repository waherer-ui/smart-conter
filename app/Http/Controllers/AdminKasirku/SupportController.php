<?php

namespace App\Http\Controllers\AdminKasirku;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Http\Request;

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

        return view(
            'admin-kasirku.support.index',
            compact(
                'tickets',
                'stats',
                'admins'
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

        $ticket->update([
            'status' => $validated['status'],
            'closed_at' => in_array(
                $validated['status'],
                ['resolved', 'closed']
            )
                ? now()
                : null,
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
                'exists:users,id',
            ],
        ]);

        $ticket->update([
            'assigned_to' => $validated['assigned_to'] ?? null,
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
        $validated = $request->validate([
            'message' => [
                'required',
                'string',
                'max:5000',
            ],
        ]);

        $ticket->messages()->create([
            'user_id' => session('user_id'),
            'message' => $validated['message'],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Jika admin membalas tiket yang masih baru,
        | otomatis ubah menjadi processing.
        |--------------------------------------------------------------------------
        */

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