@extends('layouts.app')

@section('title', 'Detail Tiket Bantuan')

@section('content')

<div class="max-w-5xl mx-auto px-4 py-6">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">

        <a
            href="{{ route('admin-kasirku.support.index') }}"
            class="w-9 h-9 shrink-0 flex items-center justify-center
                   rounded-xl bg-gray-800 border border-gray-700
                   text-gray-300 hover:text-white transition"
        >
            ←
        </a>

        <div class="min-w-0">
            <h1 class="text-xl sm:text-2xl font-bold text-white">
                Detail Tiket Bantuan
            </h1>

            <p class="text-sm text-gray-400 mt-1">
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


    {{-- Informasi Tiket --}}
    <div class="bg-gray-800/80 border border-gray-700 rounded-2xl p-4 sm:p-6 mb-5">

        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">

            <div class="min-w-0">

                <div class="flex flex-wrap items-center gap-2 mb-2">

                    <span class="text-xs font-mono text-gray-500">
                        #{{ $ticket->ticket_number }}
                    </span>

                    @php
                        $statuses = [
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

                        $statusData = $statuses[$ticket->status] ?? [
                            'label' => ucfirst($ticket->status),
                            'class' => 'bg-gray-500/10 text-gray-400 border-gray-500/20'
                        ];
                    @endphp

                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg
                                 border text-[11px] font-medium
                                 {{ $statusData['class'] }}">
                        {{ $statusData['label'] }}
                    </span>

                </div>

                <h2 class="text-lg sm:text-xl font-semibold text-white">
                    {{ $ticket->subject }}
                </h2>

            </div>

        </div>


        {{-- Metadata --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-5 pt-5 border-t border-gray-700">

            <div>
                <p class="text-xs text-gray-500 mb-1">
                    Owner
                </p>

                <p class="text-sm text-white">
                    {{ $ticket->owner->name ?? '-' }}
                </p>

                @if($ticket->owner)
                    <p class="text-xs text-gray-400 mt-1">
                        {{ $ticket->owner->email }}
                    </p>
                @endif
            </div>


            <div>
                <p class="text-xs text-gray-500 mb-1">
                    Toko
                </p>

                <p class="text-sm text-white">
                    🏪 {{ $ticket->store->name ?? '-' }}
                </p>
            </div>


            <div>
                <p class="text-xs text-gray-500 mb-1">
                    Kategori
                </p>

                <p class="text-sm text-gray-300">
                    {{ ucfirst($ticket->category) }}
                </p>
            </div>


            <div>
                <p class="text-xs text-gray-500 mb-1">
                    Prioritas
                </p>

                <p class="text-sm text-gray-300">
                    {{ ucfirst($ticket->priority) }}
                </p>
            </div>

        </div>

    </div>


    {{-- Ubah Status --}}
    <div class="bg-gray-800/80 border border-gray-700 rounded-2xl p-4 sm:p-5 mb-5">

        <h2 class="text-sm font-semibold text-white mb-3">
            Status Tiket
        </h2>

        <form
            action="{{ route('admin-kasirku.support.status', $ticket) }}"
            method="POST"
            class="flex flex-col sm:flex-row gap-3"
        >
            @csrf
            @method('PUT')

            <select
                name="status"
                class="flex-1 rounded-xl bg-gray-900 border border-gray-700
                       text-white text-sm px-4 py-3
                       focus:outline-none focus:ring-2 focus:ring-emerald-500"
            >
                <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>
                    Baru
                </option>

                <option value="processing" {{ $ticket->status === 'processing' ? 'selected' : '' }}>
                    Diproses
                </option>

                <option value="waiting" {{ $ticket->status === 'waiting' ? 'selected' : '' }}>
                    Menunggu
                </option>

                <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>
                    Selesai
                </option>

                <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>
                    Ditutup
                </option>
            </select>

            <button
                type="submit"
                class="px-5 py-3 rounded-xl
                       bg-emerald-600 hover:bg-emerald-500
                       text-white text-sm font-semibold transition"
            >
                Simpan Status
            </button>

        </form>

    </div>


    {{-- Percakapan --}}
    <div class="mb-5">

        <h2 class="text-sm font-semibold text-white mb-3">
            Percakapan
        </h2>

        <div class="space-y-3">

            @forelse($ticket->messages as $message)

                @php
                    $isOwner = $message->user_id == $ticket->owner_id;
                @endphp

                <div class="flex {{ $isOwner ? 'justify-start' : 'justify-end' }}">

                    <div class="max-w-[90%] sm:max-w-[75%]">

                        <div class="rounded-2xl px-4 py-3
                            {{ $isOwner
                                ? 'bg-gray-800 border border-gray-700 text-gray-200 rounded-bl-md'
                                : 'bg-emerald-600 text-white rounded-br-md'
                            }}">

                            <p class="text-sm whitespace-pre-line break-words">
                                {{ $message->message }}
                            </p>

                        </div>

                        <div class="mt-1 px-1 text-[11px] text-gray-500
                                    {{ $isOwner ? 'text-left' : 'text-right' }}">

                            {{ $isOwner
                                ? ($ticket->owner->name ?? 'Owner')
                                : ($message->user->name ?? 'Admin KasirKU')
                            }}

                            ·

                            {{ $message->created_at?->format('d M Y, H:i') }}

                        </div>

                    </div>

                </div>

            @empty

                <div class="bg-gray-800/80 border border-gray-700 rounded-2xl p-6 text-center">
                    <p class="text-sm text-gray-400">
                        Belum ada pesan.
                    </p>
                </div>

            @endforelse

        </div>

    </div>


    {{-- Balas --}}
    @if($ticket->status !== 'closed')

        <div class="bg-gray-800/80 border border-gray-700 rounded-2xl p-4 sm:p-5">

            <h2 class="text-sm font-semibold text-white mb-3">
                Balas Owner
            </h2>

            <form
                action="{{ route('admin-kasirku.support.reply', $ticket) }}"
                method="POST"
            >
                @csrf

                <textarea
                    name="message"
                    rows="5"
                    required
                    placeholder="Tulis balasan untuk owner..."
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
                        Kirim Balasan
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