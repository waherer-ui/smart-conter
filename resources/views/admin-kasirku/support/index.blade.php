@extends('layouts.app')

@section('title', 'Pusat Bantuan')

@section('content')

<div class="space-y-6">

    {{-- =========================================================
         HEADER
    ========================================================== --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <div>
            <h1 class="text-2xl font-bold text-white">
                Pusat Bantuan
            </h1>

            <p class="mt-1 text-sm text-gray-400">
                Kelola tiket bantuan dan keluhan dari pemilik toko KasirKU.
            </p>
        </div>

    </div>


    {{-- =========================================================
         STATISTIK
    ========================================================== --}}
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-5">

        {{-- TOTAL --}}
        <div class="rounded-2xl border border-gray-700 bg-gray-800/80 p-4">
            <p class="text-xs text-gray-400">
                Total Tiket
            </p>

            <p class="mt-2 text-2xl font-bold text-white">
                {{ $stats['total'] }}
            </p>
        </div>


        {{-- OPEN --}}
        <div class="rounded-2xl border border-gray-700 bg-gray-800/80 p-4">
            <p class="text-xs text-gray-400">
                Baru
            </p>

            <p class="mt-2 text-2xl font-bold text-yellow-400">
                {{ $stats['open'] }}
            </p>
        </div>


        {{-- PROCESSING --}}
        <div class="rounded-2xl border border-gray-700 bg-gray-800/80 p-4">
            <p class="text-xs text-gray-400">
                Diproses
            </p>

            <p class="mt-2 text-2xl font-bold text-blue-400">
                {{ $stats['processing'] }}
            </p>
        </div>


        {{-- WAITING --}}
        <div class="rounded-2xl border border-gray-700 bg-gray-800/80 p-4">
            <p class="text-xs text-gray-400">
                Menunggu
            </p>

            <p class="mt-2 text-2xl font-bold text-orange-400">
                {{ $stats['waiting'] }}
            </p>
        </div>


        {{-- RESOLVED --}}
        <div class="rounded-2xl border border-gray-700 bg-gray-800/80 p-4">
            <p class="text-xs text-gray-400">
                Selesai
            </p>

            <p class="mt-2 text-2xl font-bold text-emerald-400">
                {{ $stats['resolved'] }}
            </p>
        </div>

    </div>


    {{-- =========================================================
         FILTER
    ========================================================== --}}
    <div class="rounded-2xl border border-gray-700 bg-gray-800/80 p-4">

        <form
            method="GET"
            action="{{ route('admin-kasirku.support.index') }}"
            class="grid grid-cols-1 gap-3 sm:grid-cols-4"
        >

            {{-- SEARCH --}}
            <div class="sm:col-span-2">

                <label class="mb-1 block text-xs text-gray-400">
                    Cari tiket
                </label>

                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Nomor tiket, judul, owner, email..."
                    class="w-full rounded-xl border border-gray-600 bg-gray-900 px-4 py-2.5 text-sm text-white placeholder-gray-500 focus:border-indigo-500 focus:outline-none"
                >

            </div>


            {{-- STATUS --}}
            <div>

                <label class="mb-1 block text-xs text-gray-400">
                    Status
                </label>

                <select
                    name="status"
                    class="w-full rounded-xl border border-gray-600 bg-gray-900 px-4 py-2.5 text-sm text-white focus:border-indigo-500 focus:outline-none"
                >

                    <option value="">
                        Semua Status
                    </option>

                    <option value="open" @selected(request('status') === 'open')>
                        Baru
                    </option>

                    <option value="processing" @selected(request('status') === 'processing')>
                        Diproses
                    </option>

                    <option value="waiting" @selected(request('status') === 'waiting')>
                        Menunggu Pengguna
                    </option>

                    <option value="resolved" @selected(request('status') === 'resolved')>
                        Selesai
                    </option>

                    <option value="closed" @selected(request('status') === 'closed')>
                        Ditutup
                    </option>

                </select>

            </div>


            {{-- CATEGORY --}}
            <div>

                <label class="mb-1 block text-xs text-gray-400">
                    Kategori
                </label>

                <select
                    name="category"
                    class="w-full rounded-xl border border-gray-600 bg-gray-900 px-4 py-2.5 text-sm text-white focus:border-indigo-500 focus:outline-none"
                >

                    <option value="">
                        Semua Kategori
                    </option>

                    <option value="akun" @selected(request('category') === 'akun')>
                        Akun
                    </option>

                    <option value="toko" @selected(request('category') === 'toko')>
                        Toko
                    </option>

                    <option value="pembayaran" @selected(request('category') === 'pembayaran')>
                        Pembayaran
                    </option>

                    <option value="langganan" @selected(request('category') === 'langganan')>
                        Langganan
                    </option>

                    <option value="produk" @selected(request('category') === 'produk')>
                        Produk
                    </option>

                    <option value="transaksi" @selected(request('category') === 'transaksi')>
                        Transaksi
                    </option>

                    <option value="laporan" @selected(request('category') === 'laporan')>
                        Laporan
                    </option>

                    <option value="bug" @selected(request('category') === 'bug')>
                        Bug / Error
                    </option>

                    <option value="lainnya" @selected(request('category') === 'lainnya')>
                        Lainnya
                    </option>

                </select>

            </div>


            {{-- BUTTON --}}
            <div class="flex items-end gap-2 sm:col-span-4">

                <button
                    type="submit"
                    class="rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-500"
                >
                    🔍 Cari
                </button>

                <a
                    href="{{ route('admin-kasirku.support.index') }}"
                    class="rounded-xl border border-gray-600 px-5 py-2.5 text-sm font-semibold text-gray-300 transition hover:bg-gray-700"
                >
                    Reset
                </a>

            </div>

        </form>

    </div>


    {{-- =========================================================
         DAFTAR TIKET
    ========================================================== --}}
    <div class="overflow-hidden rounded-2xl border border-gray-700 bg-gray-800/80">

        <div class="border-b border-gray-700 px-5 py-4">

            <h2 class="font-semibold text-white">
                Tiket Bantuan
            </h2>

            <p class="mt-1 text-xs text-gray-400">
                Daftar laporan dan permintaan bantuan dari owner.
            </p>

        </div>


        @if($tickets->count())

            <div class="divide-y divide-gray-700">

                @foreach($tickets as $ticket)

                    <a
                        href="{{ route('admin-kasirku.support.show', $ticket) }}"
                        class="block px-5 py-4 transition hover:bg-gray-700/40"
                    >

                        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

                            {{-- INFO TIKET --}}
                            <div class="min-w-0 flex-1">

                                <div class="flex flex-wrap items-center gap-2">

                                    <span class="text-xs font-semibold text-indigo-400">
                                        #{{ $ticket->ticket_number }}
                                    </span>

                                    {{-- PRIORITAS --}}
                                    @php
                                        $priorityClasses = match($ticket->priority) {
                                            'urgent' => 'bg-red-500/15 text-red-400 border-red-500/30',
                                            'high' => 'bg-orange-500/15 text-orange-400 border-orange-500/30',
                                            'normal' => 'bg-blue-500/15 text-blue-400 border-blue-500/30',
                                            'low' => 'bg-gray-500/15 text-gray-400 border-gray-500/30',
                                            default => 'bg-gray-500/15 text-gray-400 border-gray-500/30',
                                        };

                                        $priorityLabels = [
                                            'urgent' => 'Mendesak',
                                            'high' => 'Tinggi',
                                            'normal' => 'Normal',
                                            'low' => 'Rendah',
                                        ];
                                    @endphp

                                    <span class="rounded-full border px-2 py-0.5 text-[11px] {{ $priorityClasses }}">
                                        {{ $priorityLabels[$ticket->priority] ?? ucfirst($ticket->priority) }}
                                    </span>

                                </div>


                                <h3 class="mt-2 truncate font-semibold text-white">
                                    {{ $ticket->subject }}
                                </h3>


                                <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-xs text-gray-400">

                                    <span>
                                        👤 {{ $ticket->owner->name ?? '—' }}
                                    </span>

                                    <span>
                                        🏪 {{ $ticket->store->name ?? 'Tanpa toko' }}
                                    </span>

                                    <span>
                                        {{ ucfirst($ticket->category) }}
                                    </span>

                                    <span>
                                        {{ $ticket->created_at?->format('d M Y H:i') }}
                                    </span>

                                </div>

                            </div>


                            {{-- STATUS + ADMIN --}}
                            <div class="flex items-center gap-3">

                                @php
                                    $statusClasses = match($ticket->status) {
                                        'open' => 'bg-yellow-500/15 text-yellow-400 border-yellow-500/30',
                                        'processing' => 'bg-blue-500/15 text-blue-400 border-blue-500/30',
                                        'waiting' => 'bg-orange-500/15 text-orange-400 border-orange-500/30',
                                        'resolved' => 'bg-emerald-500/15 text-emerald-400 border-emerald-500/30',
                                        'closed' => 'bg-gray-500/15 text-gray-400 border-gray-500/30',
                                        default => 'bg-gray-500/15 text-gray-400 border-gray-500/30',
                                    };

                                    $statusLabels = [
                                        'open' => 'Baru',
                                        'processing' => 'Diproses',
                                        'waiting' => 'Menunggu',
                                        'resolved' => 'Selesai',
                                        'closed' => 'Ditutup',
                                    ];
                                @endphp

                                <span class="whitespace-nowrap rounded-full border px-3 py-1 text-xs font-medium {{ $statusClasses }}">
                                    {{ $statusLabels[$ticket->status] ?? ucfirst($ticket->status) }}
                                </span>

                                <span class="hidden text-xs text-gray-500 sm:inline">
                                    {{ $ticket->assignedTo->name ?? 'Belum ditugaskan' }}
                                </span>

                                <span class="text-gray-500">
                                    →
                                </span>

                            </div>

                        </div>

                    </a>

                @endforeach

            </div>


            {{-- PAGINATION --}}
            <div class="border-t border-gray-700 px-5 py-4">

                {{ $tickets->links() }}

            </div>

        @else

            <div class="px-5 py-16 text-center">

                <div class="text-4xl">
                    🎫
                </div>

                <h3 class="mt-3 font-semibold text-white">
                    Belum ada tiket bantuan
                </h3>

                <p class="mt-1 text-sm text-gray-400">
                    Tiket dari pemilik toko akan muncul di halaman ini.
                </p>

            </div>

        @endif

    </div>

</div>

@endsection