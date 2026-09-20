@extends('layouts.app')

@section('title', 'Pusat Bantuan')

@section('content')

<div class="max-w-5xl mx-auto px-4 py-6">

    {{-- Header --}}
    <div class="flex items-center justify-between gap-3 mb-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-white">
                Pusat Bantuan
            </h1>
            <p class="text-sm text-gray-400 mt-1">
                Hubungi tim KasirKU jika Anda membutuhkan bantuan.
            </p>
        </div>

        <a
            href="{{ route('support.create') }}"
            class="shrink-0 inline-flex items-center gap-2 px-4 py-2.5
                   bg-emerald-600 hover:bg-emerald-500
                   text-white text-sm font-semibold rounded-xl
                   transition"
        >
            <span class="text-lg leading-none">+</span>
            <span class="hidden sm:inline">Buat Tiket</span>
            <span class="sm:hidden">Tiket</span>
        </a>
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

    {{-- Daftar Tiket --}}
    @if($tickets->count())

        <div class="space-y-3">

            @foreach($tickets as $ticket)

                <a
                    href="{{ route('support.show', $ticket) }}"
                    class="block bg-gray-800/80 border border-gray-700
                           hover:border-gray-600 hover:bg-gray-800
                           rounded-2xl p-4 sm:p-5 transition"
                >

                    <div class="flex items-start justify-between gap-4">

                        <div class="min-w-0 flex-1">

                            {{-- Ticket Number --}}
                            <div class="flex flex-wrap items-center gap-2 mb-2">
                                <span class="text-xs font-mono text-gray-500">
                                    #{{ $ticket->ticket_number }}
                                </span>

                                {{-- Status --}}
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

                                <span class="inline-flex items-center px-2 py-1 rounded-lg
                                             border text-[11px] font-medium
                                             {{ $statusData['class'] }}">
                                    {{ $statusData['label'] }}
                                </span>
                            </div>

                            {{-- Subject --}}
                            <h2 class="text-sm sm:text-base font-semibold text-white truncate">
                                {{ $ticket->subject }}
                            </h2>

                            {{-- Store --}}
                            @if($ticket->store)
                                <p class="text-xs text-gray-400 mt-1">
                                    🏪 {{ $ticket->store->name }}
                                </p>
                            @endif

                            {{-- Category --}}
                            <p class="text-xs text-gray-500 mt-2">
                                {{ ucfirst($ticket->category) }}
                                ·
                                {{ $ticket->created_at?->format('d M Y, H:i') }}
                            </p>

                        </div>

                        {{-- Arrow --}}
                        <div class="text-gray-500 pt-1">
                            →
                        </div>

                    </div>

                </a>

            @endforeach

        </div>

        {{-- Pagination --}}
        @if(method_exists($tickets, 'links'))
            <div class="mt-6">
                {{ $tickets->links() }}
            </div>
        @endif

    @else

        {{-- Empty State --}}
        <div class="bg-gray-800/80 border border-gray-700
                    rounded-2xl px-6 py-12 text-center">

            <div class="w-14 h-14 mx-auto mb-4 rounded-2xl
                        bg-emerald-500/10
                        flex items-center justify-center">
                <span class="text-2xl">💬</span>
            </div>

            <h2 class="text-base sm:text-lg font-semibold text-white">
                Belum ada tiket bantuan
            </h2>

            <p class="text-sm text-gray-400 mt-2 max-w-md mx-auto">
                Jika Anda mengalami masalah atau membutuhkan bantuan,
                silakan buat tiket baru dan tim KasirKU akan membantu Anda.
            </p>

            <a
                href="{{ route('support.create') }}"
                class="inline-flex items-center gap-2 mt-6
                       px-5 py-2.5 rounded-xl
                       bg-emerald-600 hover:bg-emerald-500
                       text-white text-sm font-semibold transition"
            >
                <span class="text-lg leading-none">+</span>
                Buat Tiket Bantuan
            </a>

        </div>

    @endif

</div>

@endsection