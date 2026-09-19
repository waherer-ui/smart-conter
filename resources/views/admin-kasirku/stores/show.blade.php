@extends('layouts.app')

@section('title', 'Detail Toko')

@section('content')

<div class="max-w-7xl mx-auto space-y-6">

{{-- HEADER DETAIL TOKO --}}
<div class="mb-6">

    <a href="{{ url()->previous() }}"
       class="inline-flex items-center gap-2 text-sm text-gray-400 hover:text-white transition mb-4">
        ← Kembali
    </a>

    <div class="bg-gray-800/80 border border-white/10 rounded-2xl p-5 shadow-xl">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-5">

            {{-- IDENTITAS TOKO --}}
            <div class="flex items-center gap-4">

                <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-2xl">
                    🏪
                </div>

                <div class="min-w-0">

                    <div class="flex items-center gap-2 flex-wrap">

                        <h1 class="text-xl font-bold text-white">
                            {{ $store->name }}
                        </h1>

                        @if($store->is_active)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-red-500/10 text-red-400 border border-red-500/20">
                                <span class="inline-block w-1.5 h-1.5 rounded-full bg-red-400"></span>
                                Nonaktif
                            </span>
                        @endif

                    </div>

                    <p class="text-sm text-gray-500 mt-1">
                        ID Toko #{{ $store->id }}
                    </p>

                </div>

            </div>


            {{-- PEMILIK --}}
            @if($store->owner)

                <div class="sm:text-right">

                    <p class="text-xs text-gray-500 uppercase tracking-wider">
                        Pemilik Toko
                    </p>

                    <div class="flex items-center sm:justify-end gap-3 mt-2">

                        <div class="w-9 h-9 rounded-lg
                                    bg-indigo-500/20
                                    flex items-center justify-center
                                    text-sm font-bold text-indigo-300">

                            {{ strtoupper(substr($store->owner->name, 0, 1)) }}

                        </div>

                        <div class="text-left sm:text-right min-w-0">

                            <p class="text-sm font-semibold text-white">
                                {{ $store->owner->name }}
                            </p>

                            <p class="text-xs text-gray-500 truncate">
                                {{ $store->owner->email }}
                            </p>

                        </div>

                    </div>

                </div>

            @endif

        </div>

    </div>

</div>


{{-- INFORMASI TOKO --}}
<div class="bg-gray-800/80 border border-white/10 rounded-2xl p-5 shadow-xl">

    <h2 class="text-lg font-semibold text-white mb-5">
        🏪 Informasi Toko
    </h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        <div class="bg-gray-900/50 rounded-xl p-4">
            <p class="text-xs text-gray-500 uppercase">
                Nama Toko
            </p>

            <p class="text-white font-semibold mt-1">
                {{ $store->name }}
            </p>
        </div>

        <div class="bg-gray-900/50 rounded-xl p-4">
            <p class="text-xs text-gray-500 uppercase">
                ID Toko
            </p>

            <p class="text-white font-semibold mt-1">
                #{{ $store->id }}
            </p>
        </div>

        <div class="bg-gray-900/50 rounded-xl p-4">
            <p class="text-xs text-gray-500 uppercase">
                Alamat
            </p>

            <p class="text-gray-200 mt-1">
                {{ $store->address ?: '-' }}
            </p>
        </div>

        <div class="bg-gray-900/50 rounded-xl p-4">
            <p class="text-xs text-gray-500 uppercase">
                Telepon
            </p>

            <p class="text-gray-200 mt-1">
                {{ $store->phone ?: '-' }}
            </p>
        </div>

                        @foreach($store->users as $staff)

                    <div
                        class="flex items-center gap-3
                               bg-gray-900/50 rounded-xl p-3"
                    >

                        <div
                            class="w-10 h-10 shrink-0 rounded-xl
                                   bg-indigo-500/20
                                   flex items-center justify-center
                                   text-indigo-300 font-semibold"
                        >
                            {{ strtoupper(substr($staff->name, 0, 1)) }}
                        </div>

                        <div class="flex-1 min-w-0">

                            <p class="text-sm text-white font-medium truncate">
                                {{ $staff->name }}
                            </p>

                            <p class="text-xs text-gray-500 truncate">
                                {{ $staff->email }}
                            </p>

                        </div>

                        <span
                            class="shrink-0 px-2 py-1 rounded-lg text-xs
                                   bg-gray-700 text-gray-300"
                        >
                            {{ strtoupper($staff->pivot->role ?? $staff->role) }}
                        </span>

                    </div>

                @endforeach

    </div>

</div>

    {{-- STATISTIK TOKO --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        <div class="bg-gray-800/80 border border-white/10 rounded-2xl p-4">
            <p class="text-xs text-gray-400 uppercase">
                Produk
            </p>
            <p class="text-2xl font-bold text-white mt-1">
                {{ $store->products->count() }}
            </p>
        </div>

        <div class="bg-gray-800/80 border border-white/10 rounded-2xl p-4">
            <p class="text-xs text-gray-400 uppercase">
                Pelanggan
            </p>
            <p class="text-2xl font-bold text-white mt-1">
                {{ $store->customers->count() }}
            </p>
        </div>

        <div class="bg-gray-800/80 border border-white/10 rounded-2xl p-4">
            <p class="text-xs text-gray-400 uppercase">
                Transaksi
            </p>
            <p class="text-2xl font-bold text-white mt-1">
                {{ $store->transactions->count() }}
            </p>
        </div>

        <div class="bg-gray-800/80 border border-white/10 rounded-2xl p-4">
            <p class="text-xs text-gray-400 uppercase">
                Pengguna
            </p>
            <p class="text-2xl font-bold text-white mt-1">
                {{ $store->users->count() }}
            </p>
        </div>

    </div>

    {{-- PRODUK --}}
<div class="bg-gray-800/80 border border-white/10 rounded-2xl p-5 shadow-xl">

    <div class="flex items-center justify-between gap-3 mb-5">

        <div>
            <h2 class="text-lg font-semibold text-white">
                📦 Produk Toko
            </h2>

            <p class="text-sm text-gray-500 mt-1">
                {{ $store->products->count() }} produk terdaftar
            </p>
        </div>

    </div>

    @if($store->products->count())

        <div class="overflow-x-auto">

            <table class="w-full text-sm">

                <thead>
                    <tr class="border-b border-white/10 text-gray-500">
                        <th class="text-left py-3 pr-4">Produk</th>
                        <th class="text-left py-3 px-4">SKU</th>
                        <th class="text-right py-3 px-4">Stok</th>
                        <th class="text-right py-3 pl-4">Harga</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-white/5">

                   @foreach($store->products->take(5) as $product)

                        <tr>

                            <td class="py-3 pr-4">
                                <p class="text-white font-medium">
                                    {{ $product->name }}
                                </p>

                                @if($product->category)
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ $product->category }}
                                    </p>
                                @endif
                            </td>

                            <td class="py-3 px-4 text-gray-400">
                                {{ $product->sku ?: '-' }}
                            </td>

                            <td class="py-3 px-4 text-right text-gray-200">
                                {{ $product->stock }}
                            </td>

                            <td class="py-3 pl-4 text-right text-gray-200">
                                Rp{{ number_format($product->price, 0, ',', '.') }}
                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

    @else

        <p class="text-sm text-gray-500">
            Belum ada produk di toko ini.
        </p>

    @endif

</div>

{{-- PELANGGAN --}}
<div class="bg-gray-800/80 border border-white/10 rounded-2xl p-5 shadow-xl">

    <div class="mb-5">
        <h2 class="text-lg font-semibold text-white">
            👤 Pelanggan Toko
        </h2>

        <p class="text-sm text-gray-500 mt-1">
            {{ $store->customers->count() }} pelanggan terdaftar
        </p>
    </div>

    @if($store->customers->count())

        <div class="overflow-x-auto">

            <table class="w-full text-sm">

                <thead>
                    <tr class="border-b border-white/10 text-gray-500">
                        <th class="text-left py-3 pr-4">Nama</th>
                        <th class="text-left py-3 px-4">Telepon</th>
                        <th class="text-left py-3 pl-4">Alamat</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-white/5">

                    @foreach($store->customers as $customer)

                        <tr>

                            <td class="py-3 pr-4 text-white font-medium">
                                {{ $customer->name }}
                            </td>

                            <td class="py-3 px-4 text-gray-400">
                                {{ $customer->phone ?: '-' }}
                            </td>

                            <td class="py-3 pl-4 text-gray-400">
                                {{ $customer->address ?: '-' }}
                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

    @else

        <p class="text-sm text-gray-500">
            Belum ada pelanggan di toko ini.
        </p>

    @endif

</div>

{{-- TRANSAKSI --}}
<div class="bg-gray-800/80 border border-white/10 rounded-2xl p-5 shadow-xl">

    <div class="mb-5">
        <h2 class="text-lg font-semibold text-white">
            🧾 Transaksi Toko
        </h2>

        <p class="text-sm text-gray-500 mt-1">
            {{ $store->transactions->count() }} transaksi tercatat
        </p>
    </div>

    @if($store->transactions->count())

        <div class="overflow-x-auto">

            <table class="w-full text-sm">

                <thead>
                    <tr class="border-b border-white/10 text-gray-500">
                        <th class="text-left py-3 pr-4">Invoice</th>
                        <th class="text-left py-3 px-4">Tanggal</th>
                        <th class="text-right py-3 px-4">Total</th>
                        <th class="text-left py-3 pl-4">Pembayaran</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-white/5">

                    @foreach($store->transactions as $transaction)

                        <tr>

                            <td class="py-3 pr-4 text-white font-medium">
                                {{ $transaction->invoice_number }}
                            </td>

                            <td class="py-3 px-4 text-gray-400">
                                {{ $transaction->created_at
                                    ? $transaction->created_at->format('d M Y H:i')
                                    : '-' }}
                            </td>

                            <td class="py-3 px-4 text-right text-gray-200">
                                Rp{{ number_format($transaction->total, 0, ',', '.') }}
                            </td>

                            <td class="py-3 pl-4 text-gray-400">
                                {{ $transaction->payment_method ?: '-' }}
                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

    @else

        <p class="text-sm text-gray-500">
            Belum ada transaksi di toko ini.
        </p>

    @endif

</div>

{{-- AKTIVITAS TOKO --}}
<div class="bg-gray-800/80 border border-white/10 rounded-2xl p-5 shadow-xl">

    <div class="mb-5">
        <h2 class="text-lg font-semibold text-white">
            🕒 Aktivitas Toko
        </h2>

        <p class="text-sm text-gray-500 mt-1">
            Riwayat aktivitas produk dan stok
        </p>
    </div>

    @if($store->productHistories->count())

        <div class="space-y-3">

            @foreach($store->productHistories->sortByDesc('created_at')->take(10) as $history)

                <div class="flex items-start gap-3 p-3 rounded-xl bg-gray-900/50">

                    <div class="text-lg">
                        📦
                    </div>

                    <div class="flex-1 min-w-0">

                        <p class="text-sm text-white">
                            {{ $history->name }}
                        </p>

                        <p class="text-xs text-gray-500 mt-1">
                            {{ $history->status_type ?: 'Aktivitas produk' }}
                        </p>

                        <p class="text-xs text-gray-600 mt-1">
                            {{ $history->created_at
                                ? $history->created_at->format('d M Y H:i')
                                : '-' }}
                        </p>

                    </div>

                </div>

            @endforeach

        </div>

    @else

        <p class="text-sm text-gray-500">
            Belum ada aktivitas produk di toko ini.
        </p>

    @endif

</div>

</div>

@endsection