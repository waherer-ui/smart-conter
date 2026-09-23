@extends('layouts.app')

@section('title', 'Transfer Antar Toko')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white">
                🔄 Transfer Antar Toko
            </h1>
            <p class="text-sm text-gray-400 mt-1">
                Kelola perpindahan stok antar toko Anda.
            </p>
        </div>

        <a href="{{ route('transfer.create') }}"
           class="inline-flex items-center justify-center gap-2
                  px-4 py-2.5 rounded-xl
                  bg-emerald-600 hover:bg-emerald-500
                  text-white font-semibold text-sm
                  transition">
            <span>＋</span>
            Transfer Baru
        </a>
    </div>


    {{-- Flash Success --}}
    @if(session('success'))
        <div class="mb-5 rounded-xl border border-emerald-500/30
                    bg-emerald-500/10 px-4 py-3
                    text-sm text-emerald-300">
            {{ session('success') }}
        </div>
    @endif


    {{-- Validation Error --}}
    @if($errors->any())
        <div class="mb-5 rounded-xl border border-red-500/30
                    bg-red-500/10 px-4 py-3">
            <p class="text-sm font-semibold text-red-300 mb-2">
                Terjadi kesalahan:
            </p>

            <ul class="list-disc list-inside text-sm text-red-300 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    {{-- Daftar Transfer --}}
    <div class="bg-gray-800/80 border border-gray-700 rounded-2xl overflow-hidden">

        @forelse($transfers as $transfer)

            <div class="p-4 sm:p-5 border-b border-gray-700 last:border-b-0">

                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

                    {{-- Informasi Transfer --}}
                    <div class="min-w-0">

                        <div class="flex flex-wrap items-center gap-2 mb-2">

                            <span class="text-xs font-medium text-gray-400">
                                #TRF-{{ str_pad($transfer->id, 5, '0', STR_PAD_LEFT) }}
                            </span>

                            @if($transfer->status === 'pending')

                                <span class="px-2.5 py-1 rounded-full
                                             bg-yellow-500/10
                                             border border-yellow-500/20
                                             text-yellow-300 text-xs font-semibold">
                                    Menunggu Penerimaan
                                </span>

                            @elseif($transfer->status === 'completed')

                                <span class="px-2.5 py-1 rounded-full
                                             bg-emerald-500/10
                                             border border-emerald-500/20
                                             text-emerald-300 text-xs font-semibold">
                                    Selesai
                                </span>

                            @elseif($transfer->status === 'cancelled')

                                <span class="px-2.5 py-1 rounded-full
                                             bg-red-500/10
                                             border border-red-500/20
                                             text-red-300 text-xs font-semibold">
                                    Dibatalkan
                                </span>

                            @else

                                <span class="px-2.5 py-1 rounded-full
                                             bg-gray-700
                                             text-gray-300 text-xs font-semibold">
                                    {{ ucfirst($transfer->status) }}
                                </span>

                            @endif

                        </div>


                        {{-- Toko --}}
                        <div class="flex flex-col sm:flex-row sm:items-center gap-2 text-sm">

                            <div class="font-semibold text-white">
                                {{ $transfer->sourceStore->name ?? '-' }}
                            </div>

                            <span class="hidden sm:inline text-gray-500">
                                →
                            </span>

                            <span class="sm:hidden text-gray-500">
                                ↓
                            </span>

                            <div class="font-semibold text-emerald-400">
                                {{ $transfer->destinationStore->name ?? '-' }}
                            </div>

                        </div>


                        {{-- Metadata --}}
                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-2 text-xs text-gray-400">

                            <span>
                                📅
                                {{ optional($transfer->transfer_date)->format('d M Y') }}
                            </span>

                            <span>
                                📦
                                {{ $transfer->items->sum('quantity') }} item
                            </span>

                            @if($transfer->user)
                                <span>
                                    👤 {{ $transfer->user->name }}
                                </span>
                            @endif

                        </div>

                    </div>


                    {{-- Detail --}}
                    <div class="flex-shrink-0">

                        <a href="{{ route('transfer.show', $transfer->id) }}"
                           class="inline-flex items-center justify-center
                                  w-full lg:w-auto
                                  px-4 py-2 rounded-xl
                                  bg-gray-700 hover:bg-gray-600
                                  text-gray-200 text-sm font-medium
                                  transition">
                            Lihat Detail
                        </a>

                    </div>

                </div>

            </div>

        @empty

            <div class="px-6 py-16 text-center">

                <div class="text-4xl mb-4">
                    🔄
                </div>

                <h3 class="text-lg font-semibold text-white">
                    Belum ada transfer
                </h3>

                <p class="text-sm text-gray-400 mt-1 mb-5">
                    Transfer stok antar toko akan muncul di sini.
                </p>

                <a href="{{ route('transfer.create') }}"
                   class="inline-flex items-center gap-2
                          px-4 py-2.5 rounded-xl
                          bg-emerald-600 hover:bg-emerald-500
                          text-white font-semibold text-sm
                          transition">
                    ＋ Buat Transfer
                </a>

            </div>

        @endforelse

    </div>


    {{-- Pagination --}}
    @if($transfers->hasPages())
        <div class="mt-5">
            {{ $transfers->links() }}
        </div>
    @endif

</div>
@endsection