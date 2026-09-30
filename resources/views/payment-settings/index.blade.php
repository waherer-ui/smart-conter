@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-6">

    {{-- Header --}}
    <div class="mb-6">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-emerald-500/10
                        flex items-center justify-center text-emerald-400 text-xl">
                💳
            </div>

            <div>
                <h1 class="text-xl font-bold text-white">
                    Rekening & Pembayaran
                </h1>

                <p class="text-sm text-gray-400">
                    Atur rekening, e-wallet, dan QR pembayaran toko.
                </p>
            </div>
        </div>
    </div>

    {{-- Success --}}
    @if(session('success'))
        <div class="mb-5 rounded-xl border border-emerald-500/30
                    bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300">
            {{ session('success') }}
        </div>
    @endif

    {{-- Validation Error --}}
    @if($errors->any())
        <div class="mb-5 rounded-xl border border-red-500/30
                    bg-red-500/10 px-4 py-3 text-sm text-red-300">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    {{-- ========================================================= --}}
    {{-- REKENING & E-WALLET --}}
    {{-- ========================================================= --}}

    <div class="bg-gray-900 border border-gray-800 rounded-2xl overflow-hidden mb-6">

        <div class="p-5 border-b border-gray-800">
            <div class="flex items-start justify-between gap-4">

                <div>
                    <h2 class="text-lg font-semibold text-white">
                        Rekening & E-Wallet
                    </h2>

                    <p class="text-sm text-gray-400 mt-1">
                        Tambahkan rekening bank atau akun e-wallet
                        yang digunakan toko untuk menerima pembayaran.
                    </p>
                </div>

                <span class="shrink-0 text-xs px-3 py-1 rounded-full
                             bg-emerald-500/10 text-emerald-400">
                    Pro & Premium
                </span>

            </div>
        </div>


        {{-- Form Tambah Rekening --}}
        <div class="p-5 border-b border-gray-800">

            <h3 class="text-sm font-semibold text-white mb-4">
                Tambah Rekening / E-Wallet
            </h3>

            <form
                action="{{ route('payment-accounts.store') }}"
                method="POST"
                class="grid grid-cols-1 md:grid-cols-2 gap-4"
            >
                @csrf

                {{-- Jenis --}}
                <div>
                    <label class="block text-sm text-gray-300 mb-2">
                        Jenis
                    </label>

                    <select
                        name="type"
                        id="payment-type"
                        required
                        class="w-full rounded-xl bg-gray-800 border border-gray-700
                               text-white px-4 py-3 focus:border-emerald-500
                               focus:ring-1 focus:ring-emerald-500"
                    >
                        <option value="bank">Bank</option>
                        <option value="ewallet">E-Wallet</option>
                    </select>
                </div>


                {{-- Provider --}}
                <div>
                    <label class="block text-sm text-gray-300 mb-2">
                        Provider
                    </label>

                    <select
                        name="provider"
                        id="payment-provider"
                        required
                        class="w-full rounded-xl bg-gray-800 border border-gray-700
                               text-white px-4 py-3 focus:border-emerald-500
                               focus:ring-1 focus:ring-emerald-500"
                    ></select>
                </div>


                {{-- Nama --}}
                <div>
                    <label class="block text-sm text-gray-300 mb-2">
                        Nama Pemilik / Akun
                    </label>

                    <input
                        type="text"
                        name="account_name"
                        required
                        maxlength="100"
                        placeholder="Contoh: Septian / Toko ABC"
                        class="w-full rounded-xl bg-gray-800 border border-gray-700
                               text-white placeholder-gray-500 px-4 py-3
                               focus:border-emerald-500 focus:ring-1
                               focus:ring-emerald-500"
                    >
                </div>


                {{-- Nomor --}}
                <div>
                    <label class="block text-sm text-gray-300 mb-2">
                        Nomor Rekening / Nomor Akun
                    </label>

                    <input
                        type="text"
                        name="account_number"
                        required
                        maxlength="50"
                        placeholder="Masukkan nomor rekening / akun"
                        class="w-full rounded-xl bg-gray-800 border border-gray-700
                               text-white placeholder-gray-500 px-4 py-3
                               focus:border-emerald-500 focus:ring-1
                               focus:ring-emerald-500"
                    >
                </div>


                {{-- Button --}}
                <div class="md:col-span-2 flex justify-end">
                    <button
                        type="submit"
                        class="px-5 py-3 rounded-xl bg-emerald-600
                               hover:bg-emerald-500 text-white font-semibold
                               transition"
                    >
                        + Tambah Pembayaran
                    </button>
                </div>

            </form>
        </div>


        {{-- Daftar Account --}}
        <div class="p-5">

            <h3 class="text-sm font-semibold text-white mb-4">
                Akun Pembayaran Toko
            </h3>

            @forelse($accounts as $account)

                <div class="border border-gray-800 rounded-xl p-4 mb-3
                            bg-gray-950">

                    <div class="flex flex-col md:flex-row
                                md:items-center md:justify-between gap-4">

                        <div class="flex items-start gap-3">

                            <div class="w-10 h-10 rounded-xl
                                        bg-gray-800 flex items-center
                                        justify-center text-lg">
                                {{ $account->type === 'bank' ? '🏦' : '📱' }}
                            </div>

                            <div>
                                <div class="flex items-center gap-2 flex-wrap">

                                    <h4 class="font-semibold text-white">
                                        {{ $account->provider }}
                                    </h4>

                                    @if($account->is_active)
                                        <span class="text-xs px-2 py-1 rounded-full
                                                     bg-emerald-500/10
                                                     text-emerald-400">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="text-xs px-2 py-1 rounded-full
                                                     bg-gray-700 text-gray-400">
                                            Nonaktif
                                        </span>
                                    @endif

                                </div>

                                <p class="text-sm text-gray-300 mt-1">
                                    {{ $account->account_name }}
                                </p>

                                <p class="text-sm text-gray-500">
                                    {{ $account->account_number }}
                                </p>
                            </div>

                        </div>


                        {{-- Actions --}}
                        <div class="flex items-center gap-2">

                            <form
                                action="{{ route('payment-accounts.toggle', $account) }}"
                                method="POST"
                            >
                                @csrf
                                @method('PATCH')

                                <button
                                    type="submit"
                                    class="px-3 py-2 rounded-lg bg-gray-800
                                           hover:bg-gray-700 text-sm text-gray-300"
                                >
                                    {{ $account->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </form>


                            <form
                                action="{{ route('payment-accounts.destroy', $account) }}"
                                method="POST"
                                onsubmit="return confirm('Hapus akun pembayaran ini?')"
                            >
                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="px-3 py-2 rounded-lg bg-red-500/10
                                           hover:bg-red-500/20 text-red-400
                                           text-sm"
                                >
                                    Hapus
                                </button>
                            </form>

                        </div>

                    </div>

                </div>

            @empty

                <div class="text-center py-8 text-gray-500">
                    Belum ada rekening atau e-wallet yang ditambahkan.
                </div>

            @endforelse

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- QR PEMBAYARAN --}}
    {{-- ========================================================= --}}

    <div class="bg-gray-900 border border-gray-800 rounded-2xl overflow-hidden">

        <div class="p-5 border-b border-gray-800">

            <div class="flex items-start justify-between gap-4">

                <div>
                    <h2 class="text-lg font-semibold text-white">
                        QR Pembayaran
                    </h2>

                    <p class="text-sm text-gray-400 mt-1">
                        Upload QR pembayaran statis milik toko.
                    </p>
                </div>

                <span class="shrink-0 text-xs px-3 py-1 rounded-full
                             bg-emerald-500/10 text-emerald-400">
                    Pro & Premium
                </span>

            </div>

        </div>


        {{-- Upload QR --}}
        <div class="p-5 border-b border-gray-800">

            <form
                action="{{ route('payment-qrs.store') }}"
                method="POST"
                enctype="multipart/form-data"
                class="space-y-4"
            >
                @csrf

                <div>
                    <label class="block text-sm text-gray-300 mb-2">
                        Nama QR
                    </label>

                    <input
                        type="text"
                        name="name"
                        required
                        maxlength="100"
                        placeholder="Contoh: QRIS Toko"
                        class="w-full rounded-xl bg-gray-800 border border-gray-700
                               text-white placeholder-gray-500 px-4 py-3
                               focus:border-emerald-500 focus:ring-1
                               focus:ring-emerald-500"
                    >
                </div>


                <div>
                    <label class="block text-sm text-gray-300 mb-2">
                        Gambar QR
                    </label>

                    <input
                        type="file"
                        name="image"
                        accept="image/jpeg,image/png,image/webp"
                        required
                        class="block w-full text-sm text-gray-400
                               file:mr-4 file:py-3 file:px-4
                               file:rounded-xl file:border-0
                               file:bg-emerald-600 file:text-white
                               file:font-semibold hover:file:bg-emerald-500
                               bg-gray-800 rounded-xl border border-gray-700"
                    >

                    <p class="text-xs text-gray-500 mt-2">
                        JPG, PNG, atau WEBP. Maksimal 2 MB.
                    </p>
                </div>


                <div class="flex justify-end">
                    <button
                        type="submit"
                        class="px-5 py-3 rounded-xl bg-emerald-600
                               hover:bg-emerald-500 text-white font-semibold
                               transition"
                    >
                        + Upload QR
                    </button>
                </div>

            </form>

        </div>


        {{-- Daftar QR --}}
        <div class="p-5">

            <h3 class="text-sm font-semibold text-white mb-4">
                QR Pembayaran Toko
            </h3>

            @forelse($qrs as $qr)

                <div class="border border-gray-800 rounded-xl p-4 mb-3
                            bg-gray-950">

                    <div class="flex flex-col md:flex-row gap-5">

                        {{-- QR Image --}}
                        <div class="shrink-0">

                            <div class="w-40 h-40 rounded-xl bg-white
                                        p-2 flex items-center justify-center">

                        <img
                            src="{{ Storage::disk('s3')->url($qr->image_path) }}"
                            alt="{{ $qr->name }}"
                            class="max-w-full max-h-full object-contain"
                        >

                            </div>

                        </div>


                        {{-- Information --}}
                        <div class="flex-1">

                            <div class="flex items-center gap-2 flex-wrap">

                                <h4 class="font-semibold text-white">
                                    {{ $qr->name }}
                                </h4>

                                @if($qr->is_active)
                                    <span class="text-xs px-2 py-1 rounded-full
                                                 bg-emerald-500/10
                                                 text-emerald-400">
                                        Aktif
                                    </span>
                                @else
                                    <span class="text-xs px-2 py-1 rounded-full
                                                 bg-gray-700 text-gray-400">
                                        Nonaktif
                                    </span>
                                @endif

                            </div>

                            <p class="text-sm text-gray-500 mt-2">
                                QR ini digunakan sebagai metode pembayaran
                                Via QR pada halaman Kasir.
                            </p>


                            <div class="flex flex-wrap gap-2 mt-5">

                                <form
                                    action="{{ route('payment-qrs.toggle', $qr) }}"
                                    method="POST"
                                >
                                    @csrf
                                    @method('PATCH')

                                    <button
                                        type="submit"
                                        class="px-3 py-2 rounded-lg bg-gray-800
                                               hover:bg-gray-700 text-sm
                                               text-gray-300"
                                    >
                                        {{ $qr->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>


                                <form
                                    action="{{ route('payment-qrs.destroy', $qr) }}"
                                    method="POST"
                                    onsubmit="return confirm('Hapus QR pembayaran ini?')"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="px-3 py-2 rounded-lg bg-red-500/10
                                               hover:bg-red-500/20 text-red-400
                                               text-sm"
                                    >
                                        Hapus
                                    </button>
                                </form>

                            </div>

                        </div>

                    </div>

                </div>

            @empty

                <div class="text-center py-8 text-gray-500">
                    Belum ada QR pembayaran yang diupload.
                </div>

            @endforelse

        </div>

    </div>

</div>


<script>
    const paymentProviders = {
        bank: [
            'BCA',
            'BRI',
            'BNI',
            'Mandiri',
            'BSI',
            'CIMB Niaga',
            'BTN',
            'Danamon',
            'Permata',
            'OCBC',
            'Bank Jago',
            'SeaBank',
            'Bank Neo Commerce'
        ],

        ewallet: [
            'DANA',
            'GoPay',
            'OVO',
            'ShopeePay',
            'LinkAja',
            'i.Saku',
            'Sakuku',
            'AstraPay'
        ]
    };

    const paymentType = document.getElementById('payment-type');
    const paymentProvider = document.getElementById('payment-provider');

    function updatePaymentProviders() {

        const type = paymentType.value;

        paymentProvider.innerHTML = '';

        paymentProviders[type].forEach(provider => {

            const option = document.createElement('option');

            option.value = provider;
            option.textContent = provider;

            paymentProvider.appendChild(option);

        });
    }

    paymentType.addEventListener('change', updatePaymentProviders);

    updatePaymentProviders();
</script>

@endsection