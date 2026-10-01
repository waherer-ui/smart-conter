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
     LOKASI TOKO & ABSENSI
========================================================== --}}

<div class="bg-gray-800 p-6 rounded-xl border border-white/10">

    <div class="mb-6">
        <h2 class="text-lg font-semibold text-white">
            📍 Lokasi Toko & Absensi
        </h2>

        <p class="text-sm text-gray-400 mt-1">
            Tentukan titik lokasi toko yang digunakan sebagai acuan absensi staf.
        </p>
    </div>

    <div class="space-y-5">

        {{-- Tombol GPS --}}
        <div>

            <button
                type="button"
                id="get-store-location"
                class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-500 text-white font-medium px-4 py-2.5 rounded-lg transition"
            >
                📍 Gunakan Lokasi Saya
            </button>

            <p
                id="location-status"
                class="text-xs text-gray-500 mt-2"
            >
                Berdiri di lokasi toko lalu tekan tombol di atas.
            </p>

        </div>

        {{-- Koordinat --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

            <div>

                <label
                    for="latitude"
                    class="block text-sm text-gray-300 mb-2"
                >
                    Latitude
                </label>

                <input
                    type="text"
                    id="latitude"
                    name="latitude"
                    value="{{ old('latitude', $store->latitude ?? '') }}"
                    readonly
                    class="w-full bg-gray-900 border border-white/10 rounded-lg px-4 py-2.5 text-gray-300"
                >

            </div>

            <div>

                <label
                    for="longitude"
                    class="block text-sm text-gray-300 mb-2"
                >
                    Longitude
                </label>

                <input
                    type="text"
                    id="longitude"
                    name="longitude"
                    value="{{ old('longitude', $store->longitude ?? '') }}"
                    readonly
                    class="w-full bg-gray-900 border border-white/10 rounded-lg px-4 py-2.5 text-gray-300"
                >

            </div>

        </div>

        {{-- Radius --}}
        <div class="max-w-md">

            <label
                for="attendance_radius"
                class="block text-sm text-gray-300 mb-2"
            >
                Radius Absensi (meter)
            </label>

            <input
                type="number"
                id="attendance_radius"
                name="attendance_radius"
                value="{{ old('attendance_radius', $store->attendance_radius ?? 100) }}"
                min="10"
                max="1000"
                step="1"
                class="w-full bg-gray-900 border border-white/10 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-emerald-500"
            >

            <p class="text-xs text-gray-500 mt-2">
                Staf dapat melakukan absensi selama berada dalam radius yang ditentukan dari titik toko.
            </p>

        </div>

    </div>

</div>

{{-- =========================================================
     JAM KERJA & KETERLAMBATAN
========================================================== --}}

<div class="bg-gray-800 p-6 rounded-xl border border-white/10">

    <div class="mb-6">
        <h2 class="text-lg font-semibold text-white">
            ⏰ Jam Kerja & Keterlambatan
        </h2>

        <p class="text-sm text-gray-400 mt-1">
            Atur jam kerja staf dan batas toleransi keterlambatan absensi.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

        {{-- Jam Masuk --}}
        <div>

            <label
                for="work_start_time"
                class="block text-sm text-gray-300 mb-2"
            >
                Jam Masuk
            </label>

            <input
                type="time"
                id="work_start_time"
                name="work_start_time"
                value="{{ old('work_start_time', $store->work_start_time ?? '08:00') }}"
                required
                class="w-full bg-gray-900 border border-white/10 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-emerald-500"
            >

        </div>

        {{-- Jam Pulang --}}
        <div>

            <label
                for="work_end_time"
                class="block text-sm text-gray-300 mb-2"
            >
                Jam Pulang
            </label>

            <input
                type="time"
                id="work_end_time"
                name="work_end_time"
                value="{{ old('work_end_time', $store->work_end_time ?? '17:00') }}"
                required
                class="w-full bg-gray-900 border border-white/10 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-emerald-500"
            >

        </div>

        {{-- Toleransi --}}
        <div>

            <label
                for="late_tolerance"
                class="block text-sm text-gray-300 mb-2"
            >
                Toleransi Terlambat
            </label>

            <div class="relative">

                <input
                    type="number"
                    id="late_tolerance"
                    name="late_tolerance"
                    value="{{ old('late_tolerance', $store->late_tolerance ?? 15) }}"
                    min="0"
                    max="180"
                    step="1"
                    required
                    class="w-full bg-gray-900 border border-white/10 rounded-lg px-4 py-2.5 pr-16 text-white focus:outline-none focus:border-emerald-500"
                >

                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-sm text-gray-500">
                    menit
                </span>

            </div>

        </div>

    </div>

    <p class="text-xs text-gray-500 mt-4">
        Contoh: jam masuk 08:00 dengan toleransi 15 menit.
        Absensi setelah 08:15 akan tercatat sebagai terlambat.
    </p>

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
    
    <a href="{{ route('payment-settings.index') }}"
   class="block rounded-xl border border-gray-700 bg-gray-800/50 p-4 transition hover:bg-gray-700/60">
    <div class="flex items-center gap-3">
        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-500/10 text-emerald-400">
            💳
        </div>

        <div>
            <h3 class="font-semibold text-white">
                Rekening & Pembayaran
            </h3>

            <p class="text-sm text-gray-400">
                Atur rekening bank, e-wallet, dan QR pembayaran toko.
            </p>
        </div>
    </div>
</a>


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

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

        <div>

            <h2 class="text-lg font-semibold text-white">
                Pengaturan Struk
            </h2>

            <p class="text-sm text-gray-400 mt-1">
                Atur tampilan dan informasi yang ditampilkan pada struk transaksi.
            </p>

        </div>

        <a
            href="{{ route('receipt-settings.index') }}"
            class="inline-flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-500 text-white font-medium px-5 py-2.5 rounded-lg transition"
        >

            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="w-5 h-5"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M9 14.25l6-6m4.5-3.493V21H4.5V4.5h15V4.5zM9 8.25h.008v.008H9V8.25zm6 7.5h.008v.008H15v-.008z"
                />
            </svg>

            Kelola Tampilan Struk

        </a>

    </div>

</div>

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

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const button = document.getElementById('get-store-location');
    const status = document.getElementById('location-status');
    const latitude = document.getElementById('latitude');
    const longitude = document.getElementById('longitude');

    if (!button) return;

    button.addEventListener('click', function () {

        if (!navigator.geolocation) {
            status.textContent =
                'GPS tidak didukung oleh browser/perangkat ini.';
            return;
        }

        button.disabled = true;
        button.textContent = '📍 Mengambil lokasi...';

        status.textContent =
            'Meminta lokasi GPS perangkat...';

        navigator.geolocation.getCurrentPosition(

            function (position) {

                latitude.value =
                    position.coords.latitude.toFixed(7);

                longitude.value =
                    position.coords.longitude.toFixed(7);

                status.textContent =
                    '✓ Lokasi berhasil diambil. Jangan lupa simpan pengaturan.';

                button.disabled = false;
                button.textContent =
                    '📍 Perbarui Lokasi';
            },

            function (error) {

                let message =
                    'Lokasi tidak dapat diambil.';

                if (error.code === 1) {
                    message =
                        'Izin lokasi ditolak. Silakan izinkan akses lokasi.';
                }

                if (error.code === 2) {
                    message =
                        'Lokasi tidak tersedia. Pastikan GPS aktif.';
                }

                if (error.code === 3) {
                    message =
                        'Waktu pengambilan lokasi habis. Coba lagi.';
                }

                status.textContent = message;

                button.disabled = false;
                button.textContent =
                    '📍 Gunakan Lokasi Saya';
            },

            {
                enableHighAccuracy: true,
                timeout: 15000,
                maximumAge: 0
            }
        );

    });

});
</script>

@endsection