@extends('layouts.app')

@section('title', 'Pengaturan')
@section('header', 'Pengaturan Aplikasi & Toko')

@section('content')

<div class="space-y-6">{{-- =========================================================
     PESAN
========================================================== --}}

@if(session('success'))

    <div class="rounded-xl border border-green-500/20 bg-green-500/10 px-4 py-3 text-sm text-green-300">
        {{ session('success') }}
    </div>

@endif


@if($errors->any())

    <div class="rounded-xl border border-red-500/20 bg-red-500/10 px-4 py-3 text-sm text-red-300">

        <ul class="list-disc list-inside space-y-1">

            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach

        </ul>

    </div>

@endif


{{-- =========================================================
     INFORMASI TOKO
========================================================== --}}

<form
    method="POST"
    action="{{ route('setting.update') }}"
    class="space-y-6"
>

    @csrf
    @method('PUT')


    <div class="bg-gray-800 p-6 rounded-xl border border-white/10">

        <div class="mb-6">

            <h2 class="text-lg font-semibold text-white">
                Informasi Toko
            </h2>

            <p class="text-sm text-gray-400 mt-1">
                Informasi dasar toko yang digunakan oleh Smart POS.
            </p>

        </div>


        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

            {{-- Nama Toko --}}
            <div>

                <label
                    for="store_name"
                    class="block text-sm text-gray-300 mb-2"
                >
                    Nama Toko
                </label>

                <input
                    type="text"
                    id="store_name"
                    name="store_name"
                    value="{{ old('store_name', $settings->store_name) }}"
                    required
                    maxlength="150"
                    placeholder="Contoh: Smart POS Hernanto"
                    class="w-full bg-gray-900 border border-white/10 rounded-lg px-4 py-2.5 text-white placeholder-gray-500 focus:outline-none focus:border-indigo-500"
                >

            </div>


            {{-- Nomor Telepon --}}
            <div>

                <label
                    for="store_phone"
                    class="block text-sm text-gray-300 mb-2"
                >
                    Nomor Telepon
                </label>

                <input
                    type="text"
                    id="store_phone"
                    name="store_phone"
                    value="{{ old('store_phone', $settings->store_phone) }}"
                    maxlength="30"
                    placeholder="Contoh: 08123456789"
                    class="w-full bg-gray-900 border border-white/10 rounded-lg px-4 py-2.5 text-white placeholder-gray-500 focus:outline-none focus:border-indigo-500"
                >

            </div>


            {{-- Alamat --}}
            <div class="md:col-span-2">

                <label
                    for="store_address"
                    class="block text-sm text-gray-300 mb-2"
                >
                    Alamat Toko
                </label>

                <textarea
                    id="store_address"
                    name="store_address"
                    rows="3"
                    maxlength="1000"
                    placeholder="Masukkan alamat toko"
                    class="w-full bg-gray-900 border border-white/10 rounded-lg px-4 py-2.5 text-white placeholder-gray-500 focus:outline-none focus:border-indigo-500"
                >{{ old('store_address', $settings->store_address) }}</textarea>

            </div>


            {{-- Email --}}
            <div>

                <label
                    for="store_email"
                    class="block text-sm text-gray-300 mb-2"
                >
                    Email Toko
                </label>

                <input
                    type="email"
                    id="store_email"
                    name="store_email"
                    value="{{ old('store_email', $settings->store_email) }}"
                    maxlength="150"
                    placeholder="Contoh: toko@email.com"
                    class="w-full bg-gray-900 border border-white/10 rounded-lg px-4 py-2.5 text-white placeholder-gray-500 focus:outline-none focus:border-indigo-500"
                >

            </div>

        </div>

    </div>


    {{-- =========================================================
         PENGATURAN TRANSAKSI
    ========================================================== --}}

    <div class="bg-gray-800 p-6 rounded-xl border border-white/10">

        <div class="mb-6">

            <h2 class="text-lg font-semibold text-white">
                Pengaturan Transaksi
            </h2>

            <p class="text-sm text-gray-400 mt-1">
                Atur mata uang dan penggunaan diskon pada transaksi.
            </p>

        </div>


        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

            {{-- Currency --}}
            <div>

                <label
                    for="currency"
                    class="block text-sm text-gray-300 mb-2"
                >
                    Mata Uang
                </label>

                <select
                    id="currency"
                    name="currency"
                    class="w-full bg-gray-900 border border-white/10 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-indigo-500"
                >

                    <option
                        value="IDR"
                        {{ old('currency', $settings->currency) === 'IDR' ? 'selected' : '' }}
                    >
                        IDR - Rupiah
                    </option>

                    <option
                        value="USD"
                        {{ old('currency', $settings->currency) === 'USD' ? 'selected' : '' }}
                    >
                        USD - Dollar
                    </option>

                </select>

            </div>


            {{-- Maksimal Diskon --}}
            <div>

                <label
                    for="max_discount"
                    class="block text-sm text-gray-300 mb-2"
                >
                    Maksimal Diskon (%)
                </label>

                <input
                    type="number"
                    id="max_discount"
                    name="max_discount"
                    value="{{ old('max_discount', $settings->max_discount) }}"
                    min="0"
                    max="100"
                    step="0.01"
                    class="w-full bg-gray-900 border border-white/10 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-indigo-500"
                >

            </div>


            {{-- Izinkan Diskon --}}
            <div class="md:col-span-2">

                <label class="flex items-center gap-3 cursor-pointer">

                    <input
                        type="hidden"
                        name="allow_discount"
                        value="0"
                    >

                    <input
                        type="checkbox"
                        name="allow_discount"
                        value="1"
                        {{ old('allow_discount', $settings->allow_discount) ? 'checked' : '' }}
                        class="w-4 h-4 rounded border-gray-600 bg-gray-900 text-indigo-600 focus:ring-indigo-500"
                    >

                    <span class="text-sm text-gray-300">
                        Izinkan penggunaan diskon pada transaksi
                    </span>

                </label>

            </div>

        </div>

    </div>


    {{-- =========================================================
         PENGATURAN STOK
    ========================================================== --}}

    <div class="bg-gray-800 p-6 rounded-xl border border-white/10">

        <div class="mb-6">

            <h2 class="text-lg font-semibold text-white">
                Pengaturan Stok
            </h2>

            <p class="text-sm text-gray-400 mt-1">
                Tentukan batas minimum stok produk.
            </p>

        </div>


        <div class="max-w-md">

            <label
                for="minimum_stock"
                class="block text-sm text-gray-300 mb-2"
            >
                Minimum Stok
            </label>

            <input
                type="number"
                id="minimum_stock"
                name="minimum_stock"
                value="{{ old('minimum_stock', $settings->minimum_stock) }}"
                min="0"
                required
                class="w-full bg-gray-900 border border-white/10 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-indigo-500"
            >

            <p class="text-xs text-gray-500 mt-2">
                Produk akan dianggap memiliki stok rendah ketika stok berada di bawah batas ini.
            </p>

        </div>

    </div>


    {{-- =========================================================
         PENGATURAN STRUK
    ========================================================== --}}

    <div class="bg-gray-800 p-6 rounded-xl border border-white/10">

        <div class="mb-6">

            <h2 class="text-lg font-semibold text-white">
                Pengaturan Struk
            </h2>

            <p class="text-sm text-gray-400 mt-1">
                Tentukan informasi yang ditampilkan pada struk transaksi.
            </p>

        </div>


        <div class="space-y-4">

            {{-- Footer --}}
            <div>

                <label
                    for="receipt_footer"
                    class="block text-sm text-gray-300 mb-2"
                >
                    Pesan Footer Struk
                </label>

                <textarea
                    id="receipt_footer"
                    name="receipt_footer"
                    rows="3"
                    maxlength="1000"
                    placeholder="Contoh: Terima kasih telah berbelanja."
                    class="w-full bg-gray-900 border border-white/10 rounded-lg px-4 py-2.5 text-white placeholder-gray-500 focus:outline-none focus:border-indigo-500"
                >{{ old('receipt_footer', $settings->receipt_footer) }}</textarea>

            </div>


            {{-- Tampilkan Kasir --}}
            <label class="flex items-center gap-3 cursor-pointer">

                <input
                    type="hidden"
                    name="show_cashier"
                    value="0"
                >

                <input
                    type="checkbox"
                    name="show_cashier"
                    value="1"
                    {{ old('show_cashier', $settings->show_cashier) ? 'checked' : '' }}
                    class="w-4 h-4 rounded border-gray-600 bg-gray-900 text-indigo-600 focus:ring-indigo-500"
                >

                <span class="text-sm text-gray-300">
                    Tampilkan nama kasir pada struk
                </span>

            </label>


            {{-- Metode Pembayaran --}}
            <label class="flex items-center gap-3 cursor-pointer">

                <input
                    type="hidden"
                    name="show_payment_method"
                    value="0"
                >

                <input
                    type="checkbox"
                    name="show_payment_method"
                    value="1"
                    {{ old('show_payment_method', $settings->show_payment_method) ? 'checked' : '' }}
                    class="w-4 h-4 rounded border-gray-600 bg-gray-900 text-indigo-600 focus:ring-indigo-500"
                >

                <span class="text-sm text-gray-300">
                    Tampilkan metode pembayaran pada struk
                </span>

            </label>


            {{-- Diskon --}}
            <label class="flex items-center gap-3 cursor-pointer">

                <input
                    type="hidden"
                    name="show_discount"
                    value="0"
                >

                <input
                    type="checkbox"
                    name="show_discount"
                    value="1"
                    {{ old('show_discount', $settings->show_discount) ? 'checked' : '' }}
                    class="w-4 h-4 rounded border-gray-600 bg-gray-900 text-indigo-600 focus:ring-indigo-500"
                >

                <span class="text-sm text-gray-300">
                    Tampilkan diskon pada struk
                </span>

            </label>

        </div>

    </div>


    {{-- =========================================================
         SIMPAN
    ========================================================== --}}

    <div class="flex justify-end pb-4">

        <button
            type="submit"
            class="bg-indigo-600 hover:bg-indigo-500 text-white font-medium px-6 py-2.5 rounded-lg transition"
        >
            Simpan Pengaturan
        </button>

    </div>

</form>

</div>@endsection