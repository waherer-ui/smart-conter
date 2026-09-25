@extends('layouts.app')

@section('title', 'Pengaturan Struk')
@section('header', 'Pengaturan Struk')

@section('content')

<div class="space-y-6">{{-- SUCCESS --}}
@if(session('success'))
    <div class="rounded-xl border border-emerald-500/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300">
        {{ session('success') }}
    </div>
@endif

{{-- ERROR VALIDATION --}}
@if($errors->any())
    <div class="rounded-xl border border-red-500/20 bg-red-500/10 px-4 py-3 text-sm text-red-300">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif


{{-- HEADER PAGE --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

    <div>
        <h2 class="text-xl font-semibold text-white">
            Custom Struk
        </h2>

        <p class="text-sm text-gray-400 mt-1">
            Atur tampilan informasi dan logo yang akan muncul
            pada struk transaksi toko.
        </p>
    </div>

    <a
        href="{{ route('setting') }}"
        class="inline-flex items-center justify-center gap-2 rounded-lg border border-white/10 bg-gray-800 px-4 py-2.5 text-sm text-gray-300 hover:bg-gray-700 hover:text-white transition"
    >
        ← Kembali ke Pengaturan
    </a>

</div>


{{-- TOKO AKTIF --}}
<div class="bg-gray-800 p-5 rounded-xl border border-white/10">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

        <div>
            <p class="text-xs text-gray-500 mb-1">
                TOKO AKTIF
            </p>

            <h3 class="text-lg font-semibold text-white">
                {{ $store->name }}
            </h3>
        </div>

        <span class="inline-flex w-fit items-center rounded-full border border-emerald-500/20 bg-emerald-500/10 px-3 py-1 text-xs text-emerald-400">
            Custom Struk Aktif
        </span>

    </div>

</div>


{{-- FORM --}}
<form
    action="{{ route('receipt-settings.update') }}"
    method="POST"
    enctype="multipart/form-data"
    class="space-y-6"
>

    @csrf
    @method('PUT')


    {{-- INFORMASI USAHA --}}
    <div class="bg-gray-800 p-6 rounded-xl border border-white/10">

        <div class="mb-6">

            <h2 class="text-lg font-semibold text-white">
                Informasi Usaha
            </h2>

            <p class="text-sm text-gray-400 mt-1">
                Informasi dasar yang akan ditampilkan
                pada bagian atas struk.
            </p>

        </div>


        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

            {{-- NAMA USAHA --}}
            <div>

                <label
                    for="business_name"
                    class="block text-sm text-gray-300 mb-2"
                >
                    Nama Usaha
                </label>

                <input
                    type="text"
                    id="business_name"
                    name="business_name"
                    value="{{ old('business_name', $receiptSetting->business_name) }}"
                    maxlength="255"
                    placeholder="Contoh: Warung½T"
                    class="w-full bg-gray-900 border border-white/10 rounded-lg px-4 py-2.5 text-white placeholder-gray-500 focus:outline-none focus:border-indigo-500"
                >

            </div>


            {{-- TELEPON --}}
            <div>

                <label
                    for="phone"
                    class="block text-sm text-gray-300 mb-2"
                >
                    Nomor Telepon
                </label>

                <input
                    type="text"
                    id="phone"
                    name="phone"
                    value="{{ old('phone', $receiptSetting->phone) }}"
                    maxlength="50"
                    placeholder="Contoh: 08123456789"
                    class="w-full bg-gray-900 border border-white/10 rounded-lg px-4 py-2.5 text-white placeholder-gray-500 focus:outline-none focus:border-indigo-500"
                >

            </div>


            {{-- ALAMAT --}}
            <div class="md:col-span-2">

                <label
                    for="address"
                    class="block text-sm text-gray-300 mb-2"
                >
                    Alamat
                </label>

                <textarea
                    id="address"
                    name="address"
                    rows="3"
                    maxlength="1000"
                    placeholder="Masukkan alamat toko"
                    class="w-full bg-gray-900 border border-white/10 rounded-lg px-4 py-2.5 text-white placeholder-gray-500 focus:outline-none focus:border-indigo-500"
                >{{ old('address', $receiptSetting->address) }}</textarea>

            </div>


            {{-- EMAIL --}}
            <div class="md:col-span-2">

                <label
                    for="email"
                    class="block text-sm text-gray-300 mb-2"
                >
                    Email
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email', $receiptSetting->email) }}"
                    maxlength="255"
                    placeholder="contoh@email.com"
                    class="w-full bg-gray-900 border border-white/10 rounded-lg px-4 py-2.5 text-white placeholder-gray-500 focus:outline-none focus:border-indigo-500"
                >

            </div>

        </div>

    </div>


    {{-- LOGO --}}
    <div class="bg-gray-800 p-6 rounded-xl border border-white/10">

        <div class="mb-6">

            <h2 class="text-lg font-semibold text-white">
                Logo Struk
            </h2>

            <p class="text-sm text-gray-400 mt-1">
                Logo akan ditampilkan pada bagian atas struk.
                Format JPG, PNG, atau WEBP. Maksimal 2 MB.
            </p>

        </div>


        {{-- LOGO SAAT INI --}}
        @if($receiptSetting->logo)

            <div class="mb-5">

                <p class="text-sm text-gray-300 mb-3">
                    Logo Saat Ini
                </p>

                <div class="flex flex-col sm:flex-row sm:items-center gap-4">

                    <div class="w-28 h-28 rounded-xl border border-white/10 bg-gray-900 flex items-center justify-center overflow-hidden">

                        <img
                            src="{{ asset($receiptSetting->logo) }}"
                            alt="Logo Struk"
                            class="max-w-full max-h-full object-contain"
                        >

                    </div>


                    <div>

                        <p class="text-xs text-gray-500 mb-3">
                            Logo yang tersimpan saat ini.
                        </p>

                        <button
                            type="button"
                            onclick="document.getElementById('delete-logo-form').submit()"
                            class="inline-flex items-center justify-center gap-2 rounded-lg border border-red-500/20 bg-red-500/10 px-4 py-2.5 text-sm text-red-400 hover:bg-red-500/20 transition"
                        >
                            🗑 Hapus Logo
                        </button>

                    </div>

                </div>

            </div>

        @endif


        {{-- PREVIEW LOGO BARU --}}
        <div
            id="logo-preview-wrapper"
            class="{{ $receiptSetting->logo ? 'hidden' : 'hidden' }} mb-5"
        >

            <p class="text-sm text-gray-300 mb-3">
                Preview Logo Baru
            </p>

            <div class="w-28 h-28 rounded-xl border border-indigo-500/20 bg-gray-900 flex items-center justify-center overflow-hidden">

                <img
                    id="logo-preview"
                    src=""
                    alt="Preview Logo"
                    class="max-w-full max-h-full object-contain"
                >

            </div>

        </div>


        {{-- UPLOAD --}}
        <div>

            <label
                for="logo"
                class="block text-sm text-gray-300 mb-2"
            >
                {{ $receiptSetting->logo ? 'Ganti Logo' : 'Upload Logo' }}
            </label>

            <input
                type="file"
                id="logo"
                name="logo"
                accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                class="block w-full text-sm text-gray-300
                       file:mr-4 file:py-2.5 file:px-4
                       file:rounded-lg file:border-0
                       file:bg-indigo-600 file:text-white
                       hover:file:bg-indigo-500
                       file:cursor-pointer
                       bg-gray-900 border border-white/10 rounded-lg"
            >

            <p class="text-xs text-gray-500 mt-2">
                Maksimal 2 MB. Gunakan logo dengan latar transparan
                jika ingin hasil struk lebih rapi.
            </p>

        </div>

    </div>


    {{-- HEADER STRUK --}}
    <div class="bg-gray-800 p-6 rounded-xl border border-white/10">

        <div class="mb-5">

            <h2 class="text-lg font-semibold text-white">
                Header Struk
            </h2>

            <p class="text-sm text-gray-400 mt-1">
                Teks tambahan yang ditampilkan di bawah informasi usaha.
            </p>

        </div>

        <textarea
            id="header_text"
            name="header_text"
            rows="4"
            maxlength="1000"
            placeholder="Contoh: Pusat Aksesoris & Servis HP"
            class="w-full bg-gray-900 border border-white/10 rounded-lg px-4 py-2.5 text-white placeholder-gray-500 focus:outline-none focus:border-indigo-500"
        >{{ old('header_text', $receiptSetting->header_text) }}</textarea>

    </div>


    {{-- FOOTER STRUK --}}
    <div class="bg-gray-800 p-6 rounded-xl border border-white/10">

        <div class="mb-5">

            <h2 class="text-lg font-semibold text-white">
                Footer Struk
            </h2>

            <p class="text-sm text-gray-400 mt-1">
                Pesan yang ditampilkan pada bagian paling bawah struk.
            </p>

        </div>

        <textarea
            id="footer_text"
            name="footer_text"
            rows="4"
            maxlength="1000"
            placeholder="Contoh: Terima kasih sudah berbelanja 🙏"
            class="w-full bg-gray-900 border border-white/10 rounded-lg px-4 py-2.5 text-white placeholder-gray-500 focus:outline-none focus:border-indigo-500"
        >{{ old('footer_text', $receiptSetting->footer_text) }}</textarea>

    </div>


    {{-- BUTTON --}}
    <div class="flex flex-col sm:flex-row justify-end gap-3 pb-4">

        <a
            href="{{ route('setting') }}"
            class="inline-flex items-center justify-center rounded-lg border border-white/10 bg-gray-800 px-5 py-2.5 text-sm font-medium text-gray-300 hover:bg-gray-700 hover:text-white transition"
        >
            Batal
        </a>

        <button
            type="submit"
            class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-indigo-500 transition"
        >
            💾 Simpan Pengaturan
        </button>

    </div>

</form>


{{-- FORM HAPUS LOGO --}}
@if($receiptSetting->logo)

    <form
        id="delete-logo-form"
        action="{{ route('receipt-settings.logo.delete') }}"
        method="POST"
        class="hidden"
    >
        @csrf
        @method('DELETE')
    </form>

@endif

</div>{{-- PREVIEW LOGO JAVASCRIPT --}}

<script>
document.addEventListener('DOMContentLoaded', function () {

    const logoInput = document.getElementById('logo');
    const previewWrapper = document.getElementById('logo-preview-wrapper');
    const previewImage = document.getElementById('logo-preview');

    if (!logoInput) {
        return;
    }

    logoInput.addEventListener('change', function (event) {

        const file = event.target.files[0];

        if (!file) {
            previewWrapper.classList.add('hidden');
            previewImage.src = '';
            return;
        }

        /*
         * Validasi ukuran di browser.
         * Validasi server tetap dilakukan oleh Laravel.
         */
        if (file.size > 2 * 1024 * 1024) {
            alert('Ukuran logo maksimal 2 MB.');

            logoInput.value = '';
            previewWrapper.classList.add('hidden');
            previewImage.src = '';

            return;
        }

        /*
         * Validasi tipe file.
         */
        const allowedTypes = [
            'image/jpeg',
            'image/png',
            'image/webp'
        ];

        if (!allowedTypes.includes(file.type)) {
            alert('Format logo harus JPG, PNG, atau WEBP.');

            logoInput.value = '';
            previewWrapper.classList.add('hidden');
            previewImage.src = '';

            return;
        }

        /*
         * Tampilkan preview sebelum disimpan.
         */
        const reader = new FileReader();

        reader.onload = function (e) {
            previewImage.src = e.target.result;
            previewWrapper.classList.remove('hidden');
        };

        reader.readAsDataURL(file);
    });

});
</script>@endsection