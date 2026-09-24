@extends('layouts.app')

@section('content')

<div class="max-w-3xl mx-auto px-4 py-6">

    {{-- Header --}}
    <div class="mb-6">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-emerald-500/15 flex items-center justify-center">
                <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1M12 3v12m0 0l-4-4m4 4l4-4"/>
                </svg>
            </div>

            <div>
                <h1 class="text-xl font-bold text-white">
                    Backup Data
                </h1>
                <p class="text-sm text-gray-400">
                    Simpan salinan data bisnis Anda
                </p>
            </div>
        </div>
    </div>


    {{-- Info --}}
    <div class="bg-gray-800/80 border border-gray-700 rounded-2xl p-5 mb-5">

        <div class="flex items-start gap-3">
            <div class="flex-shrink-0 mt-0.5">
                <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M12 20a8 8 0 100-16 8 8 0 000 16z"/>
                </svg>
            </div>

            <div>
                <h2 class="text-sm font-semibold text-white mb-1">
                    Backup seluruh data toko
                </h2>

                <p class="text-sm text-gray-400 leading-relaxed">
                    Backup akan mencakup data bisnis dari seluruh toko yang Anda miliki.
                    Data akan dikemas dalam file ZIP agar mudah disimpan.
                </p>
            </div>
        </div>

    </div>


    {{-- Daftar toko --}}
    <div class="bg-gray-800/80 border border-gray-700 rounded-2xl overflow-hidden mb-5">

        <div class="px-5 py-4 border-b border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-semibold text-white">
                        Toko yang akan dibackup
                    </h2>
                    <p class="text-xs text-gray-400 mt-1">
                        {{ $stores->count() }} toko milik Anda
                    </p>
                </div>

                <span class="px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-400 text-xs font-medium">
                    {{ $stores->count() }} Toko
                </span>
            </div>
        </div>


        <div class="divide-y divide-gray-700">

            @forelse($stores as $store)

                <div class="px-5 py-4 flex items-center justify-between gap-3">

                    <div class="flex items-center gap-3 min-w-0">

                        <div class="w-9 h-9 rounded-lg bg-gray-700 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-6h6v6"/>
                            </svg>
                        </div>

                        <div class="min-w-0">
                            <p class="text-sm font-medium text-white truncate">
                                {{ $store->name }}
                            </p>

                            @if($store->address)
                                <p class="text-xs text-gray-500 truncate">
                                    {{ $store->address }}
                                </p>
                            @else
                                <p class="text-xs text-gray-500">
                                    Toko aktif
                                </p>
                            @endif
                        </div>

                    </div>

                    @if($store->is_active)
                        <span class="text-xs text-emerald-400 flex-shrink-0">
                            Aktif
                        </span>
                    @else
                        <span class="text-xs text-gray-500 flex-shrink-0">
                            Nonaktif
                        </span>
                    @endif

                </div>

            @empty

                <div class="px-5 py-8 text-center">
                    <p class="text-sm text-gray-400">
                        Belum ada toko yang dapat dibackup.
                    </p>
                </div>

            @endforelse

        </div>

    </div>
  

    {{-- Data yang dibackup --}}
    <div class="bg-gray-800/80 border border-gray-700 rounded-2xl p-5 mb-5">
      
      <div class="bg-amber-500/10 border border-amber-500/20 rounded-2xl p-4 mb-5">
    <div class="flex items-start gap-3">
        <svg class="w-5 h-5 text-amber-400 flex-shrink-0 mt-0.5"
             fill="none"
             stroke="currentColor"
             viewBox="0 0 24 24">
            <path stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M12 9v2m0 4h.01M10.29 3.86l-7.12 12A2 2 0 004.9 19h14.2a2 2 0 001.73-3.14l-7.12-12a2 2 0 00-3.42 0z"/>
        </svg>

        <div>
            <p class="text-sm font-medium text-amber-300">
                Simpan file backup dengan aman
            </p>

            <p class="text-xs text-gray-400 mt-1 leading-relaxed">
                File backup berisi data bisnis toko Anda.
                Simpan di tempat yang aman dan jangan membagikannya kepada orang lain.
            </p>
        </div>
    </div>
</div>

        <h2 class="font-semibold text-white mb-4">
            Data yang disertakan
        </h2>

        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">

            @foreach([
                'Produk',
                'Riwayat Produk',
                'Pelanggan',
                'Hutang',
                'Pembayaran Hutang',
                'Supplier',
                'Pembelian',
                'Transaksi',
                'Pengeluaran',
                'Pengaturan Toko',
                'Transfer Stok',
                'Data Toko'
            ] as $item)

                <div class="flex items-center gap-2 text-xs text-gray-300 bg-gray-900/50 rounded-lg px-3 py-2">
                    <svg class="w-4 h-4 text-emerald-400 flex-shrink-0"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 13l4 4L19 7"/>
                    </svg>

                    <span>{{ $item }}</span>
                </div>

            @endforeach

        </div>

    </div>


    {{-- Tombol backup --}}
    <div class="bg-gray-800/80 border border-gray-700 rounded-2xl p-5">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

            <div>
                <h2 class="font-semibold text-white">
                    Siap melakukan backup?
                </h2>

                <p class="text-xs text-gray-400 mt-1">
                    File akan dibuat dalam format ZIP.
                </p>
            </div>

            <a
                href="{{ route('backup.download') }}"
              onclick="return confirm('Buat backup seluruh data dari {{ $stores->count() }} toko sekarang?');"
                class="inline-flex items-center justify-center gap-2
                       px-5 py-3
                       rounded-xl
                       bg-emerald-600 hover:bg-emerald-500
                       text-white text-sm font-semibold
                       transition
                       shadow-lg shadow-emerald-900/20"
            >

                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1M12 3v12m0 0l-4-4m4 4l4-4"/>
                </svg>

                Backup Sekarang

            </a>

        </div>

    </div>

</div>

@endsection