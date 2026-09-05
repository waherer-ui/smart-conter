@extends('layouts.app')

@section('title', 'Riwayat Transaksi')

@section('header', 'Riwayat Transaksi')

@section('content')

<div class="space-y-6">

    {{-- HEADER & FILTER --}}
    <div class="bg-gray-800/80 border border-white/10 rounded-2xl p-5 shadow-xl">

        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

            <div>
                <h2 class="text-lg font-bold text-white">
                    Riwayat Transaksi
                </h2>

                <p class="text-xs text-gray-400 mt-1">
                    Daftar seluruh transaksi penjualan yang tersimpan.
                </p>
            </div>

<form
    action="{{ route('riwayat') }}"
    method="GET"
    class="flex flex-col sm:flex-row gap-2"
>
    {{-- PENCARIAN --}}
    <input
        type="text"
        name="search"
        value="{{ request('search') }}"
        placeholder="Cari invoice / kasir..."
        class="bg-gray-900 border border-white/10 rounded-xl px-3 py-2 text-xs text-white outline-none focus:ring-2 focus:ring-indigo-500"
    >

    {{-- PILIH PERIODE --}}
    <select
        name="period"
        id="periodFilter"
        onchange="applyRiwayatPeriod(this.value)"
        class="bg-gray-900 border border-white/10 rounded-xl px-3 py-2 text-xs text-white outline-none focus:ring-2 focus:ring-indigo-500"
    >
        <option value="">Pilih periode</option>
        <option value="today" {{ request('period') == 'today' ? 'selected' : '' }}>
            Hari ini
        </option>
        <option value="7days" {{ request('period') == '7days' ? 'selected' : '' }}>
            7 hari terakhir
        </option>
        <option value="1month" {{ request('period') == '1month' ? 'selected' : '' }}>
            1 bulan terakhir
        </option>
        <option value="1year" {{ request('period') == '1year' ? 'selected' : '' }}>
            1 tahun terakhir
        </option>
        <option value="3years" {{ request('period') == '3years' ? 'selected' : '' }}>
            3 tahun terakhir
        </option>
        <option value="custom" {{ request('period') == 'custom' ? 'selected' : '' }}>
            Rentang tanggal
        </option>
    </select>

    {{-- RENTANG TANGGAL --}}
    <div
        id="customDateRange"
        class="{{ request('period') == 'custom' ? 'flex' : 'hidden' }} flex-col sm:flex-row gap-2"
    >
        <input
            type="date"
            name="start_date"
            id="riwayatStartDate"
            value="{{ request('start_date') }}"
            class="bg-gray-900 border border-white/10 rounded-xl px-3 py-2 text-xs text-white outline-none focus:ring-2 focus:ring-indigo-500"
        >

        <input
            type="date"
            name="end_date"
            id="riwayatEndDate"
            value="{{ request('end_date') }}"
            class="bg-gray-900 border border-white/10 rounded-xl px-3 py-2 text-xs text-white outline-none focus:ring-2 focus:ring-indigo-500"
        >
    </div>

    {{-- CARI --}}
    <button
        type="submit"
        class="bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-2 rounded-xl text-xs font-semibold transition"
    >
        Cari
    </button>

    {{-- RESET --}}
    @if(
        request('search') ||
        request('period') ||
        request('start_date') ||
        request('end_date')
    )
        <a
            href="{{ route('riwayat') }}"
            class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-xl text-xs font-semibold text-center transition"
        >
            Reset
        </a>
    @endif
</form>
</div>
 </div>


    {{-- DAFTAR TRANSAKSI --}}
    <div class="bg-gray-800/80 border border-white/10 rounded-2xl shadow-xl overflow-hidden">

        <div class="p-5 border-b border-white/10">

            <div class="flex items-center justify-between">

                <div>
                    <h3 class="text-sm font-semibold text-white">
                        Daftar Transaksi
                    </h3>

                    <p class="text-[11px] text-gray-400 mt-1">
                        Menampilkan transaksi terbaru.
                    </p>
                </div>

                @if(isset($transactions))

                    <span class="text-[11px] text-gray-400">
                        Total:
                        <span class="text-indigo-400 font-semibold">
                            {{ $transactions->total() }}
                        </span>
                    </span>

                @endif

            </div>

        </div>


        @if(isset($transactions) && $transactions->count() > 0)

            {{-- DESKTOP TABLE --}}
            <div class="hidden md:block overflow-x-auto">

                <table class="w-full text-left">

                    <thead class="bg-gray-900/60">

                        <tr class="text-[10px] uppercase tracking-wider text-gray-400">

                            <th class="px-5 py-3">
                                Invoice
                            </th>

                            <th class="px-5 py-3">
                                Tanggal
                            </th>

                            <th class="px-5 py-3">
                                Kasir
                            </th>

                            <th class="px-5 py-3">
                                Barang
                            </th>

                            <th class="px-5 py-3">
                                Pembayaran
                            </th>

                            <th class="px-5 py-3 text-right">
                                Total
                            </th>

                        </tr>

                    </thead>

                    <tbody class="divide-y divide-white/5">

                        @foreach($transactions as $transaction)

                            <tr class="hover:bg-white/[0.02] transition">

                                {{-- INVOICE --}}
                                <td class="px-5 py-4">

                                    <span class="text-indigo-400 font-mono text-xs">
                                        {{ $transaction->invoice_number }}
                                    </span>

                                </td>


                                {{-- TANGGAL --}}
                                <td class="px-5 py-4">

                                    <div class="text-xs text-white">
                                        {{ $transaction->created_at->format('d M Y') }}
                                    </div>

                                    <div class="text-[10px] text-gray-500">
                                        {{ $transaction->created_at->format('H:i:s') }}
                                    </div>

                                </td>


                                {{-- KASIR --}}
                                <td class="px-5 py-4">

                                    <div class="text-xs text-white">
                                        {{ $transaction->user->name ?? '-' }}
                                    </div>

                                    <div class="text-[10px] text-gray-500">
                                        {{ $transaction->user->role ?? '-' }}
                                    </div>

                                </td>


                                {{-- BARANG --}}
                                <td class="px-5 py-4">

                                    <div class="space-y-1">

                                        @foreach($transaction->items as $item)

                                            <div class="flex items-center gap-2">

                                                <span class="text-xs text-gray-300">
                                                    {{ $item->product_name }}
                                                </span>

                                                <span class="text-[10px] text-gray-500">
                                                    x{{ $item->quantity }}
                                                </span>

                                            </div>

                                        @endforeach

                                    </div>

                                </td>


                                {{-- PEMBAYARAN --}}
                                <td class="px-5 py-4">

                                    <span class="inline-flex bg-gray-700/70 border border-white/5 text-gray-300 px-2 py-1 rounded-lg text-[10px]">
                                        {{ $transaction->payment_method }}
                                    </span>

                                </td>


                                {{-- TOTAL --}}
                                <td class="px-5 py-4 text-right">

                                    <div class="text-emerald-400 font-semibold text-xs">
                                        Rp {{ number_format($transaction->total, 0, ',', '.') }}
                                    </div>

                                    <div class="text-[10px] text-gray-500 mt-1">
                                        Bayar:
                                        Rp {{ number_format($transaction->paid, 0, ',', '.') }}
                                    </div>

                                    <div class="text-[10px] text-gray-500">
                                        Kembali:
                                        Rp {{ number_format($transaction->change, 0, ',', '.') }}
                                    </div>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>


            {{-- MOBILE CARD --}}
            <div class="md:hidden p-4 space-y-3">

                @foreach($transactions as $transaction)

                    <div class="bg-gray-900/70 border border-white/10 rounded-xl p-4">

                        {{-- HEADER --}}
                        <div class="flex justify-between items-start gap-3">

                            <div>

                                <div class="text-indigo-400 font-mono text-xs">
                                    {{ $transaction->invoice_number }}
                                </div>

                                <div class="text-[10px] text-gray-500 mt-1">
                                    {{ $transaction->created_at->format('d M Y H:i:s') }}
                                </div>

                            </div>

                            <span class="bg-gray-700 text-gray-300 px-2 py-1 rounded-lg text-[10px]">
                                {{ $transaction->payment_method }}
                            </span>

                        </div>


                        {{-- KASIR --}}
                        <div class="mt-3 text-[11px]">

                            <span class="text-gray-500">
                                Kasir:
                            </span>

                            <span class="text-gray-300">
                                {{ $transaction->user->name ?? '-' }}
                            </span>

                        </div>


                        {{-- BARANG --}}
                        <div class="mt-3 pt-3 border-t border-white/5 space-y-1">

                            @foreach($transaction->items as $item)

                                <div class="flex justify-between text-xs">

                                    <span class="text-gray-300">
                                        {{ $item->product_name }}
                                        <span class="text-gray-500">
                                            x{{ $item->quantity }}
                                        </span>
                                    </span>

                                    <span class="text-gray-400">
                                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                    </span>

                                </div>

                            @endforeach

                        </div>


                        {{-- TOTAL --}}
                        <div class="mt-3 pt-3 border-t border-white/10">

                            <div class="flex justify-between">

                                <span class="text-xs text-gray-400">
                                    Subtotal
                                </span>

                                <span class="text-xs text-gray-300">
                                    Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}
                                </span>

                            </div>


                            <div class="flex justify-between mt-1">

                                <span class="text-xs text-gray-400">
                                    Diskon
                                </span>

                                <span class="text-xs text-gray-300">
                                    Rp {{ number_format($transaction->discount, 0, ',', '.') }}
                                </span>

                            </div>


                            <div class="flex justify-between mt-2">

                                <span class="text-sm font-bold text-white">
                                    TOTAL
                                </span>

                                <span class="text-sm font-bold text-emerald-400">
                                    Rp {{ number_format($transaction->total, 0, ',', '.') }}
                                </span>

                            </div>


                            <div class="flex justify-between mt-1">

                                <span class="text-[10px] text-gray-500">
                                    Bayar
                                </span>

                                <span class="text-[10px] text-gray-400">
                                    Rp {{ number_format($transaction->paid, 0, ',', '.') }}
                                </span>

                            </div>


                            <div class="flex justify-between">

                                <span class="text-[10px] text-gray-500">
                                    Kembalian
                                </span>

                                <span class="text-[10px] text-emerald-400">
                                    Rp {{ number_format($transaction->change, 0, ',', '.') }}
                                </span>

                            </div>

                        </div>

                    </div>

                @endforeach

            </div>


            {{-- PAGINATION --}}
            <div class="p-5 border-t border-white/10">

                {{ $transactions->links() }}

            </div>

        @else

            {{-- BELUM ADA TRANSAKSI --}}
            <div class="p-10 text-center">

                <div class="text-4xl mb-3">
                    🧾
                </div>

                <h3 class="text-sm font-semibold text-white">
                    Belum Ada Transaksi
                </h3>

                <p class="text-xs text-gray-400 mt-1">
                    Transaksi yang berhasil dilakukan akan muncul di sini.
                </p>

                <a
                    href="{{ route('kasir.index') }}"
                    class="inline-block mt-4 bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-2 rounded-xl text-xs font-semibold transition"
                >
                    Mulai Transaksi
                </a>

            </div>

        @endif

    </div>

</div>

@endsection

<script>
    function applyRiwayatPeriod(period) {
        const form = document.getElementById('periodFilter').form;
        const dateRange = document.getElementById('customDateRange');
        const start = document.getElementById('riwayatStartDate');
        const end = document.getElementById('riwayatEndDate');

        // Kalau pilih rentang tanggal
        if (period === 'custom') {
            dateRange.classList.remove('hidden');
            dateRange.classList.add('flex');

            return;
        }

        // Kalau pilih periode otomatis
        dateRange.classList.remove('flex');
        dateRange.classList.add('hidden');

        if (!period) {
            return;
        }

        const today = new Date();
        let startDate = new Date(today);

        if (period === 'today') {
            startDate = new Date(today);

        } else if (period === '7days') {
            startDate.setDate(today.getDate() - 6);

        } else if (period === '1month') {
            startDate.setMonth(today.getMonth() - 1);

        } else if (period === '1year') {
            startDate.setFullYear(today.getFullYear() - 1);

        } else if (period === '3years') {
            startDate.setFullYear(today.getFullYear() - 3);
        }

        start.value = formatDateForInput(startDate);
        end.value = formatDateForInput(today);

        // Langsung jalankan filter
        form.submit();
    }

    function formatDateForInput(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');

        return `${year}-${month}-${day}`;
    }
</script>