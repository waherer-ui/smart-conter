@extends('layouts.app')

@section('title', 'Detail Tiket')

@section('content')

<div class="max-w-3xl mx-auto px-4 py-6">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">

        <a
            href="{{ route('support.index') }}"
            class="w-9 h-9 shrink-0 flex items-center justify-center
                   rounded-xl bg-gray-800 border border-gray-700
                   text-gray-300 hover:text-white hover:border-gray-600 transition"
        >
            ←
        </a>

        <div class="min-w-0">
            <h1 class="text-xl sm:text-2xl font-bold text-white">
                Detail Tiket
            </h1>

            <p class="text-sm text-gray-400 mt-1 truncate">
                #{{ $ticket->ticket_number }}
            </p>
        </div>

    </div>


    {{-- Success --}}
    @if(session('success'))
        <div class="mb-5 px-4 py-3 rounded-xl
                    bg-emerald-500/10 border border-emerald-500/20
                    text-emerald-400 text-sm">
            {{ session('success') }}
        </div>
    @endif


    {{-- Error --}}
    @if(session('error'))
        <div class="mb-5 px-4 py-3 rounded-xl
                    bg-red-500/10 border border-red-500/20
                    text-red-400 text-sm">
            {{ session('error') }}
        </div>
    @endif


    {{-- Ticket Info --}}
    <div class="bg-gray-800/80 border border-gray-700 rounded-2xl p-4 sm:p-6 mb-5">

        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">

            <div class="min-w-0">

                <h2 class="text-base sm:text-lg font-semibold text-white">
                    {{ $ticket->subject }}
                </h2>

                @if($ticket->store)
                    <p class="text-xs text-gray-400 mt-2">
                        🏪 {{ $ticket->store->name }}
                    </p>
                @endif

            </div>


            @php
                $status = [
                    'open' => [
                        'label' => 'Baru',
                        'class' => 'bg-blue-500/10 text-blue-400 border-blue-500/20'
                    ],
                    'processing' => [
                        'label' => 'Diproses',
                        'class' => 'bg-yellow-500/10 text-yellow-400 border-yellow-500/20'
                    ],
                    'waiting' => [
                        'label' => 'Menunggu',
                        'class' => 'bg-orange-500/10 text-orange-400 border-orange-500/20'
                    ],
                    'resolved' => [
                        'label' => 'Selesai',
                        'class' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20'
                    ],
                    'closed' => [
                        'label' => 'Ditutup',
                        'class' => 'bg-gray-500/10 text-gray-400 border-gray-500/20'
                    ],
                ];

                $statusData = $status[$ticket->status] ?? [
                    'label' => ucfirst($ticket->status),
                    'class' => 'bg-gray-500/10 text-gray-400 border-gray-500/20'
                ];
            @endphp

            <span class="inline-flex self-start items-center px-3 py-1.5 rounded-lg
                         border text-xs font-medium whitespace-nowrap
                         {{ $statusData['class'] }}">
                {{ $statusData['label'] }}
            </span>

        </div>


        <div class="flex flex-wrap gap-2 mt-4">

            <span class="px-2.5 py-1 rounded-lg
                         bg-gray-900 border border-gray-700
                         text-xs text-gray-400">
                {{ ucfirst($ticket->category) }}
            </span>

            <span class="px-2.5 py-1 rounded-lg
                         bg-gray-900 border border-gray-700
                         text-xs text-gray-400">
                Prioritas: {{ ucfirst($ticket->priority) }}
            </span>

            <span class="px-2.5 py-1 rounded-lg
                         bg-gray-900 border border-gray-700
                         text-xs text-gray-500">
                {{ $ticket->created_at?->format('d M Y, H:i') }}
            </span>

        </div>

    </div>


    {{-- Conversation --}}
    <div class="mb-5">

        <h2 class="text-sm font-semibold text-white mb-3">
            Percakapan
        </h2>

        <div class="space-y-3">

            @forelse($ticket->messages as $message)

                @php
                    $isOwner = $message->user_id == session('user_id');
                @endphp

                <div class="flex {{ $isOwner ? 'justify-end' : 'justify-start' }}">

                    <div class="max-w-[88%] sm:max-w-[75%]">

                        <div class="rounded-2xl px-4 py-3
                            {{ $isOwner
                                ? 'bg-emerald-600 text-white rounded-br-md'
                                : 'bg-gray-800 border border-gray-700 text-gray-200 rounded-bl-md'
                            }}">

                            <p class="text-sm whitespace-pre-line break-words">
                                {{ $message->message }}
                            </p>

                        </div>

                        <div class="mt-1 px-1 text-[11px] text-gray-500
                                    {{ $isOwner ? 'text-right' : 'text-left' }}">

                            {{ $isOwner ? 'Anda' : ($message->user->name ?? 'Tim KasirKU') }}
                            ·
                            {{ $message->created_at?->format('d M Y, H:i') }}

                        </div>

                    </div>

                </div>

            @empty

                <div class="bg-gray-800/80 border border-gray-700
                            rounded-2xl p-6 text-center">

                    <p class="text-sm text-gray-400">
                        Belum ada percakapan.
                    </p>

                </div>

            @endforelse

        </div>

    </div>


    {{-- Reply --}}
    @if(!in_array($ticket->status, ['closed']))

        <div class="bg-gray-800/80 border border-gray-700 rounded-2xl p-4 sm:p-5">

            <h2 class="text-sm font-semibold text-white mb-3">
                Balas Tiket
            </h2>

            <form
                action="{{ route('support.message', $ticket) }}"
                method="POST"
            >
                @csrf

                <textarea
                    name="message"
                    rows="4"
                    required
                    placeholder="Tulis pesan..."
                    class="w-full rounded-xl bg-gray-900 border border-gray-700
                           text-white placeholder-gray-600 text-sm px-4 py-3
                           resize-y
                           focus:outline-none focus:ring-2 focus:ring-emerald-500
                           focus:border-transparent"
                ></textarea>

                <div class="flex justify-end mt-3">

                    <button
                        type="submit"
                        class="w-full sm:w-auto px-5 py-3 rounded-xl
                               bg-emerald-600 hover:bg-emerald-500
                               text-white text-sm font-semibold transition"
                    >
                        Kirim Pesan
                    </button>

                </div>

            </form>

        </div>

    @else

        <div class="px-4 py-3 rounded-xl
                    bg-gray-800 border border-gray-700
                    text-sm text-gray-400 text-center">
            Tiket ini sudah ditutup.
        </div>

    @endif

</div>

@endsection