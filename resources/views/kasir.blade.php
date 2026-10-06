@extends('layouts.app')

@php
    $activeStoreId = session('active_store_id');

    $activePaymentQr = null;
    $paymentAccounts = collect();

    $activeStore = null;

    $paymentQrEnabled = false;
    $paymentBankEnabled = false;
    $debtEnabled = false;
    
    $activePriceRules = collect();

if ($activeStoreId) {
    $activePriceRules = \App\Models\PriceRule::where('store_id', $activeStoreId)
        ->where('is_active', true)
        ->get([
            'id',
            'product_id',
            'type',
            'min_quantity',
            'discount_type',
            'discount_value',
            'special_price',
            'start_at',
            'end_at',
        ]);
}

    if ($activeStoreId) {

        $activePaymentQr = \App\Models\PaymentQr::where('store_id', $activeStoreId)
            ->where('is_active', true)
            ->latest()
            ->first();

        $paymentAccounts = \App\Models\PaymentAccount::where('store_id', $activeStoreId)
            ->where('is_active', true)
            ->latest()
            ->get();

        $activeStore = \App\Models\Store::find($activeStoreId);

        if ($activeStore) {
            $paymentQrEnabled = $activeStore->hasFeature('payment_qr');
            $paymentBankEnabled = $activeStore->hasFeature('payment_bank');
            $debtEnabled = $activeStore->hasFeature('debt');
        }
    }
@endphp

@section('header', '🛒')
@section('mobile_action', 'scan')

@section('header_tools')
<div class="mt-1">
    {{-- Search Produk --}}
    <div class="bg-gray-800/80 border border-white/10 rounded-xl p-1 shadow-xl backdrop-blur-md">
        <form action="{{ route('kasir.index') }}" method="GET" class="flex gap-2">
            
            @if(request('category') && request('category') != 'all')
                <input type="hidden" name="category" value="{{ request('category') }}">
            @endif

            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Cari produk atau SKU..."
                class="flex-1 min-w-0 bg-gray-900 border border-white/10 rounded-xl px-3 py-2 text-white text-sm focus:ring-1 focus:ring-indigo-500 outline-none"
                autocomplete="off"
            >

            <button
                type="button"
                onclick="openQrScanner()"
                class="shrink-0 px-3 rounded-xl bg-gray-700 hover:bg-gray-600 text-white"
            >
                📷
            </button>
        </form>
    </div>
</div>
@endsection

@section('content')

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 pb-12">

    {{-- ============================================================
         BAGIAN KIRI : KATALOG PRODUK
    ============================================================ --}}
    <div class="lg:col-span-2 space-y-4">

        {{-- KATEGORI --}}
<div class="category-scroll">

    {{-- SEMUA --}}
    <a
        href="{{ route('kasir.index', array_filter([
            'search' => request('search'),
        ])) }}"
        class="category-chip {{ (!$category || $category === 'all') ? 'active' : '' }}"
    >
        Semua
    </a>

    {{-- CATEGORY DARI DATABASE --}}
    @isset($categories)

        @foreach($categories as $cat)

            <a
                href="{{ route('kasir.index', array_filter([
                    'search' => request('search'),
                    'category' => $cat,
                ])) }}"
                class="category-chip {{ $category === $cat ? 'active' : '' }}"
            >
                {{ $cat }}
            </a>

        @endforeach

    @endisset

</div>


        {{-- DAFTAR PRODUK --}}
      @if(request('search') || (request('category') && request('category') != 'all'))
        <div class="bg-gray-800/80 border border-white/10 rounded-2xl p-4 shadow-xl backdrop-blur-md">

            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">
                Klik Produk untuk Masuk Keranjang
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 max-h-[450px] overflow-y-auto pr-1">

                @forelse($products as $p)

                    <div
                        onclick="addToCart(
                            '{{ $p->id }}',
                            @js($p->name),
                            {{ $p->price }},
                            {{ $p->stock }}
                        )"
                        class="bg-gray-900/90 border border-white/10 rounded-xl p-3 flex flex-col justify-between hover:border-indigo-500 cursor-pointer transition group"
                    >

                        <div>
                                  {{-- THUMBNAIL GAMBAR PRODUK --}}
        <div class="w-full h-24 mb-2 bg-gray-800 rounded-lg overflow-hidden flex items-center justify-center border border-white/5">
@if($p->image)
    <img
        src="{{ Storage::disk('s3')->url($p->image) }}"
        alt="{{ $p->name }}"
        class="w-10 h-10 rounded-lg object-cover border border-white/10 shrink-0"
    >
@else
    <div class="w-10 h-10 rounded-lg bg-gray-700/50 flex items-center justify-center text-gray-400 text-[10px] border border-white/10 shrink-0">
        No Img
    </div>
@endif
        </div>


                            <div class="flex justify-between items-center mb-1">

                                <span class="text-[10px] font-mono text-indigo-400">
                                    {{ $p->sku }}
                                </span>

                                <span class="text-[10px] bg-gray-800 text-gray-300 px-2 py-0.5 rounded-full border border-white/5">
                                    {{ $p->category }}
                                </span>

                            </div>

                            <h4 class="text-white font-medium text-xs line-clamp-2 group-hover:text-indigo-300 transition">
                                {{ $p->name }}
                            </h4>

                            @if($p->brand)

                                <p class="text-[10px] text-gray-400 mt-0.5">
                                    Brand: {{ $p->brand }}
                                </p>

                            @endif

                            <p class="text-emerald-400 font-semibold text-xs mt-2">
                                Rp {{ number_format($p->price, 0, ',', '.') }}
                            </p>

                        </div>

                        <div class="mt-3 pt-2 border-t border-white/5 flex items-center justify-between">
    @if($p->stock <= 0)
        <span class="text-[10px] text-rose-400 font-semibold flex items-center gap-1">
            <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span> Habis
        </span>
    @elseif($p->stock <= 5)
        <span class="text-[10px] text-orange-400 font-semibold flex items-center gap-1">
            <span class="w-1.5 h-1.5 rounded-full bg-orange-400"></span> Menipis ({{ $p->stock }})
        </span>
    @elseif($p->stock <= 10)
        <span class="text-[10px] text-yellow-400 font-semibold flex items-center gap-1">
            <span class="w-1.5 h-1.5 rounded-full bg-yellow-400"></span> Rendah ({{ $p->stock }})
        </span>
    @else
        <span class="text-[10px] text-gray-400 flex items-center gap-1">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Stok: {{ $p->stock }}
        </span>
    @endif


                            <span class="bg-indigo-600/20 text-indigo-300 text-[10px] px-2 py-0.5 rounded group-hover:bg-indigo-600 group-hover:text-white transition">
                                Pilih +
                            </span>

                        </div>

                    </div>

                @empty

                    <div class="col-span-full text-center py-8 text-gray-400 text-xs">
                        Produk tidak ditemukan.
                    </div>

                @endforelse

            </div>

        </div>
        @endif
        

    </div>


    {{-- ============================================================
         BAGIAN KANAN : KERANJANG
    ============================================================ --}}
    <div class="space-y-4">

        <div class="bg-gray-800/80 border border-white/10 rounded-2xl p-5 shadow-xl backdrop-blur-md flex flex-col justify-between h-fit lg:sticky lg:top-6">

            <div>

                <h3 class="text-sm font-semibold text-white border-b border-white/10 pb-3 mb-3">
                    Keranjang Belanja
                </h3>

                <div
                    id="cart-items"
                    class="max-h-[250px] overflow-y-auto space-y-2 mb-4 pr-1"
                >

                    <p class="text-xs text-gray-400 text-center py-4">
                        Belum ada barang di keranjang.
                    </p>

                </div>

            </div>


            <div class="border-t border-white/10 pt-4 space-y-3">

                <div class="flex justify-between text-xs text-gray-300">

                    <span>Subtotal:</span>

                    <strong id="cart-subtotal" class="text-white">
                        Rp 0
                    </strong>

                </div>


                {{-- DISKON --}}
                <div class="bg-gray-900/60 p-2.5 rounded-xl border border-white/5">

                    <label class="text-[11px] font-medium text-gray-300 block mb-1">
                        Diskon / Promo
                    </label>

                    <div class="flex gap-2">

                        <select
                            id="discountType"
                            onchange="calculateCartTotal()"
                            class="bg-gray-900 text-white border border-white/10 rounded-lg px-2 py-1 text-xs outline-none"
                        >
                            <option value="nominal">Rp</option>
                            <option value="percent">%</option>
                        </select>

                        <input
                            type="number"
                            id="discountValue"
                            placeholder="0"
                            value="0"
                            min="0"
                            oninput="calculateCartTotal()"
                            class="w-full bg-gray-900 text-white border border-white/10 rounded-lg px-3 py-1 text-xs outline-none"
                        >

                    </div>

                </div>


                <div class="flex justify-between items-center text-sm font-bold text-white">

                    <span>Total Tagihan:</span>

                    <strong id="cart-total" class="text-emerald-400 text-base">
                        Rp 0
                    </strong>

                </div>


                <button
                    type="button"
                    onclick="openPaymentModal()"
                    class="w-full bg-indigo-600 hover:bg-indigo-500 text-white py-2.5 rounded-xl text-xs font-semibold transition shadow"
                >
                    Proses Pembayaran
                </button>

            </div>

        </div>

    </div>

</div>


{{-- ============================================================
     MODAL PEMBAYARAN
============================================================ --}}
<div id="payment-modal"
     class="hidden fixed inset-0 z-50 flex items-center justify-center p-1 bg-black/60 backdrop-blur-sm overflow-y-auto">

    <div class="bg-gray-800 border border-white/15 rounded-2xl w-full max-w-md p-2 shadow-2xl relative space-y-1 max-h-[84vh] overflow-y-auto">

        <div class="flex justify-between items-center pb-2 border-b border-white/10">

            <h3 class="text-base font-semibold text-white">
                Proses Pembayaran
            </h3>

            <button
                type="button"
                onclick="closePaymentModal()"
                class="text-gray-400 hover:text-white text-sm font-bold"
            >
                ✕
            </button>

        </div>


        <div class="bg-indigo-950/40 border border-indigo-500/30 p-2 rounded-xl text-center">

            <span class="text-xs text-gray-400">
                Total Tagihan
            </span>

            <h2
                id="modal-total-pay"
                class="text-emerald-400 text-xl font-bold mt-0.5"
            >
                Rp 0
            </h2>

        </div>


        <div>

            <label class="block text-xs font-medium text-gray-300 mb-1">
                Metode Pembayaran
            </label>

<select id="pay-method"
        onchange="paymentMethodChanged()"
        class="w-full rounded-xl border border-white/10 bg-gray-900
               px-3 py-2.5 text-sm text-white focus:border-emerald-500
               focus:outline-none">

    <option value="Tunai">Tunai</option>

    @if($paymentQrEnabled)
        <option value="QRIS">QRIS</option>
    @endif

    @if($paymentBankEnabled)
        @foreach($paymentAccounts as $account)
            <option
                value="account_{{ $account->id }}"
                data-name="{{ $account->provider }}"
                data-account-name="{{ $account->account_name }}"
                data-account-number="{{ $account->account_number }}"
            >
                {{ $account->provider }}
            </option>
        @endforeach
    @endif

    @if($debtEnabled)
        <option value="Cashbon / Utang">
            Cashbon / Utang
        </option>
    @endif

</select>
            
{{-- QR PEMBAYARAN --}}
@if($activePaymentQr)
    <div
        id="store-qr-payment-box"
        class="hidden mt-1 rounded-xl border border-emerald-500/30
               bg-emerald-500/10 p-2 text-center"
    >

        <div class="mb-1">
            <p class="font-semibold text-white text-xs">
                {{ $activePaymentQr->name }}
            </p>

            <p class="mt-0.5 text-[9px] text-gray-400">
                Scan QR untuk melakukan pembayaran
            </p>
        </div>

        {{-- QR --}}
        <div class="mx-auto w-fit rounded-lg bg-white p-0.5 shadow">
            <img
                src="{{ Storage::disk('s3')->url($activePaymentQr->image_path) }}"
                alt="{{ $activePaymentQr->name }}"
                style="width: 110px; height: 110px; object-fit: contain;"
            >
        </div>

        <p class="mt-1 text-[9px] text-emerald-300">
            QR pembayaran toko
        </p>

    </div>
@else
    <div
        id="store-qr-payment-box"
        class="hidden mt-2 rounded-xl border border-yellow-500/30
               bg-yellow-500/10 p-2"
    >
        <p class="text-xs font-semibold text-yellow-300">
            QR pembayaran belum tersedia
        </p>

        <p class="mt-1 text-[10px] text-gray-400">
            Silakan tambahkan QR pembayaran toko melalui Pengaturan Pembayaran.
        </p>
    </div>
@endif


{{-- INFO REKENING / E-WALLET --}}
<div
    id="payment-account-box"
    class="hidden mt-2 rounded-xl border border-blue-500/20
           bg-blue-500/10 p-3"
>
    <p class="text-[9px] text-gray-400">
        Bayar ke
    </p>

    <p
        id="payment-account-name"
        class="mt-0.5 text-xs font-semibold text-white"
    ></p>

    <p
        id="payment-account-number"
        class="mt-0.5 text-[10px] text-blue-300 font-mono"
    ></p>
</div>

        </div>


{{-- PELANGGAN --}}
<div id="cashbon-customer-box">

    <label class="block text-xs font-medium text-gray-300 mb-1">
        Pelanggan
    </label>

    <select
    id="cashbon-customer"
    class="w-full bg-gray-900 border border-white/10 rounded-xl px-3 py-2 text-white text-xs outline-none focus:ring-1 focus:ring-indigo-500"
>
    <option value="">
        Pilih pelanggan...
    </option>

    @isset($customers)

        @foreach($customers as $customer)

<option
    value="{{ $customer->id }}"
    data-type="{{ $customer->customer_type }}"
>
    {{ $customer->name }}
    @if($customer->phone)
        — {{ $customer->phone }}
    @endif
</option>

        @endforeach

    @endisset
</select>

<button
    type="button"
    onclick="openAddCustomerModal()"
    class="w-full mt-2 bg-gray-700 hover:bg-gray-600 border border-white/10 text-indigo-300 hover:text-white py-2 rounded-xl text-xs font-semibold transition"
>
    + Tambah Pelanggan
</button>

<p class="text-[10px] text-gray-500 mt-1">
    Pelanggan opsional. Wajib dipilih untuk Cashbon / Utang.
</p>

</div>


{{-- NOMINAL PEMBAYARAN --}}
<div>

    <label class="block text-xs font-medium text-gray-300 mb-1">
        Nominal Uang Bayar (Rp)
    </label>

    <input
        type="number"
        id="pay-amount"
        placeholder="Ketik nominal uang..."
        min="0"
        oninput="calculateChange()"
        class="w-full bg-gray-900 border border-white/10 rounded-xl px-3 py-2 text-white text-xs outline-none"
    >

</div>


        <div class="flex justify-between items-center py-2 border-t border-white/10 text-xs">

    <span
        id="pay-change-label"
        class="text-gray-300"
    >
        Kembalian:
    </span>

    <strong
        id="pay-change"
        class="text-emerald-400 text-sm"
    >
        Rp 0
    </strong>

</div>


        <div class="flex gap-2 pt-2">

            <button
                type="button"
                onclick="closePaymentModal()"
                class="flex-1 bg-gray-700 hover:bg-gray-600 text-gray-300 py-2 rounded-lg text-[11px] font-medium transition"
            >
                Batal
            </button>

            <button
                type="button"
                id="submit-transaction-btn"
                onclick="submitTransaction()"
                class="flex-1 bg-indigo-600 hover:bg-indigo-500 text-white py-2.5 rounded-xl text-xs font-semibold transition shadow"
            >
                Bayar
            </button>

        </div>

    </div>

</div>

{{-- ============================================================
     MODAL TAMBAH PELANGGAN DARI CASHBON
============================================================ --}}
<div
    id="add-customer-modal"
    class="hidden fixed inset-0 z-[60] items-center justify-center p-4 bg-black/70 backdrop-blur-sm"
>
    <div class="bg-gray-800 border border-white/15 rounded-2xl w-full max-w-md p-5 shadow-2xl">

        <div class="flex justify-between items-center pb-3 border-b border-white/10">

            <h3 class="text-sm font-semibold text-white">
                Tambah Pelanggan
            </h3>

            <button
                type="button"
                onclick="closeAddCustomerModal()"
                class="text-gray-400 hover:text-white text-sm font-bold"
            >
                ✕
            </button>

        </div>


        <div class="space-y-3 mt-4">

            <div>

                <label class="block text-xs font-medium text-gray-300 mb-1">
                    Nama Pelanggan *
                </label>

                <input
                    type="text"
                    id="new-customer-name"
                    placeholder="Nama pelanggan..."
                    class="w-full bg-gray-900 border border-white/10 rounded-xl px-3 py-2 text-white text-xs outline-none focus:ring-1 focus:ring-indigo-500"
                >

            </div>
            
            <div>

    <label class="block text-xs font-medium text-gray-300 mb-1">
        Tipe Pelanggan
    </label>

    <select
        id="new-customer-type"
        class="w-full bg-gray-900 border border-white/10 rounded-xl px-3 py-2 text-white text-xs outline-none focus:ring-1 focus:ring-indigo-500"
    >
        <option value="umum">👤 Umum</option>
        <option value="member">⭐ Member</option>
        <option value="reseller">🏪 Reseller</option>
    </select>

</div>


            <div>

                <label class="block text-xs font-medium text-gray-300 mb-1">
                    No. HP
                </label>

                <input
                    type="text"
                    id="new-customer-phone"
                    placeholder="Nomor HP..."
                    class="w-full bg-gray-900 border border-white/10 rounded-xl px-3 py-2 text-white text-xs outline-none focus:ring-1 focus:ring-indigo-500"
                >

            </div>


            <div>

                <label class="block text-xs font-medium text-gray-300 mb-1">
                    Alamat
                </label>

                <textarea
                    id="new-customer-address"
                    rows="2"
                    placeholder="Alamat pelanggan..."
                    class="w-full bg-gray-900 border border-white/10 rounded-xl px-3 py-2 text-white text-xs outline-none focus:ring-1 focus:ring-indigo-500 resize-none"
                ></textarea>

            </div>

        </div>


        <div class="flex gap-2 mt-5">

            <button
                type="button"
                onclick="closeAddCustomerModal()"
                class="flex-1 bg-gray-700 hover:bg-gray-600 text-gray-300 py-2.5 rounded-xl text-xs font-medium"
            >
                Batal
            </button>

            <button
                type="button"
                id="save-new-customer-btn"
                onclick="saveNewCustomer()"
                class="flex-1 bg-indigo-600 hover:bg-indigo-500 text-white py-2.5 rounded-xl text-xs font-semibold"
            >
                Simpan Pelanggan
            </button>

        </div>

    </div>
</div>

@php
    $receiptSetting = null;
    $customReceiptEnabled = false;

    if ($activeStoreId) {
        $receiptSetting = \App\Models\ReceiptSetting::where(
            'store_id',
            $activeStoreId
        )->first();
    }

    if ($activeStore) {
        $customReceiptEnabled = $activeStore->hasFeature('custom_receipt');
    }
@endphp


{{-- ============================================================
STRUK
============================================================ --}}

<div
    id="print-receipt"
    class="hidden print:block font-mono text-black bg-white p-4 max-w-[300px] mx-auto text-xs"
>{{-- ========================================================
     HEADER
========================================================= --}}

<div class="text-center pb-2 border-b border-dashed border-black">

    @if($customReceiptEnabled && $receiptSetting)

        {{-- LOGO --}}
        @if($receiptSetting->logo)

            <div class="flex justify-center mb-2">

                <img
                    src="{{ asset($receiptSetting->logo) }}"
                    alt="Logo"
                    class="max-w-[170px] max-h-[90px] object-contain"
                >

            </div>

        @endif


        {{-- NAMA USAHA --}}
        @if($receiptSetting->business_name)

            <h2
                class="font-bold text-sm"
                id="receipt-business-name"
            >
                {{ $receiptSetting->business_name }}
            </h2>

        @else

            <h2
                class="font-bold text-sm"
                id="receipt-business-name"
            >
                Kasir½M
            </h2>

        @endif


        {{-- ALAMAT --}}
        @if($receiptSetting->address)

            <p
                class="text-[10px] whitespace-pre-line"
                id="receipt-address"
            >
                {{ $receiptSetting->address }}
            </p>

        @endif


        {{-- TELEPON --}}
        @if($receiptSetting->phone)

            <p
                class="text-[10px]"
                id="receipt-phone"
            >
                Telp: {{ $receiptSetting->phone }}
            </p>

        @endif


        {{-- EMAIL --}}
        @if($receiptSetting->email)

            <p
                class="text-[10px]"
                id="receipt-email"
            >
                {{ $receiptSetting->email }}
            </p>

        @endif


        {{-- HEADER CUSTOM --}}
        @if($receiptSetting->header_text)

            <p
                class="text-[10px] whitespace-pre-line mt-1"
                id="receipt-header-text"
            >
                {{ $receiptSetting->header_text }}
            </p>

        @endif

    @else

        {{-- DEFAULT FREE --}}

        <h2 class="font-bold text-sm">
            Kasir½M
        </h2>

    @endif


    {{-- INVOICE --}}

    <p
        class="text-[10px] mt-1"
        id="receipt-invoice"
    >
        -
    </p>


    {{-- TANGGAL --}}

    <p
        class="text-[10px]"
        id="receipt-date"
    >
        -
    </p>


    {{-- CUSTOMER CASHBON --}}

    <p
        class="text-[10px] mt-1"
        id="receipt-customer"
    >
    </p>

</div>


{{-- ========================================================
     ITEM
========================================================= --}}

<div
    class="py-2 border-b border-dashed border-black space-y-1"
    id="receipt-items"
>
</div>


{{-- ========================================================
     TOTAL
========================================================= --}}

<div
    class="py-2 border-b border-dashed border-black space-y-1 text-[11px]"
>

    <div class="flex justify-between">

        <span>Subtotal:</span>

        <span id="receipt-subtotal">
            Rp 0
        </span>

    </div>


    <div
        id="receipt-discount-row"
        class="flex justify-between"
    >

        <span>Diskon:</span>

        <span id="receipt-discount">
            Rp 0
        </span>

    </div>


    <div class="flex justify-between font-bold text-xs pt-1">

        <span>TOTAL:</span>

        <span id="receipt-total">
            Rp 0
        </span>

    </div>


    <div class="flex justify-between">

        <span>
            Bayar
            (<span id="receipt-method">Tunai</span>):
        </span>

        <span id="receipt-paid">
            Rp 0
        </span>

    </div>


    <div class="flex justify-between">

        <span id="receipt-balance-label">
            Kembali:
        </span>

        <span id="receipt-change">
            Rp 0
        </span>

    </div>

</div>


{{-- ========================================================
     FOOTER
========================================================= --}}

<div class="text-center pt-3 text-[10px]">

    @if($customReceiptEnabled && $receiptSetting)

        @if($receiptSetting->footer_text)

            <p
                id="receipt-footer-text"
                class="whitespace-pre-line"
            >
                {{ $receiptSetting->footer_text }}
            </p>

        @else

            <p id="receipt-footer-text">
                Terima kasih 🙏
            </p>

        @endif


        @if($receiptSetting->business_name)

            <p
                class="font-bold mt-1"
                id="receipt-footer-business"
            >
                {{ $receiptSetting->business_name }}
            </p>

        @endif

    @else

        <p id="receipt-footer-text">
            Terima Kasih Atas Kunjungan Anda!
        </p>

        <p>
            Barang yang sudah dibeli tidak dapat ditukar.
        </p>

        <p class="font-bold mt-1">
            Kasir½M
        </p>

    @endif

</div>

</div>


{{-- ============================================================
     TOMBOL SETELAH TRANSAKSI
============================================================ --}}
<div
    id="receipt-actions"
    class="hidden no-print mt-4"
>

    <div class="bg-gray-800/80 border border-white/10 rounded-2xl p-4">

        <div class="text-center mb-3">

            <p class="text-emerald-400 font-semibold text-sm">
                ✓ Transaksi Berhasil
            </p>

            <p
                id="success-invoice"
                class="text-white text-xs mt-1"
            >
                -
            </p>

            <p class="text-gray-400 text-xs mt-1">
                Transaksi sudah tersimpan.
            </p>

        </div>


        <div class="flex gap-2">

            <button
                type="button"
                onclick="printReceipt()"
                class="flex-1 bg-indigo-600 hover:bg-indigo-500 text-white py-3 rounded-xl text-xs font-semibold"
            >
                🖨️ Cetak Struk
            </button>

            <button
                type="button"
                onclick="newTransaction()"
                class="flex-1 bg-gray-700 hover:bg-gray-600 text-white py-3 rounded-xl text-xs font-semibold"
            >
                + Transaksi Baru
            </button>

        </div>

    </div>

</div>


{{-- ============================================================
     PRINT CSS
============================================================ --}}
<style>

    .no-print {
        display: block;
    }

    @media print {

        body * {
            visibility: hidden !important;
        }

        #print-receipt,
        #print-receipt * {
            visibility: visible !important;
        }

        #print-receipt {

            display: block !important;

            position: absolute;

            left: 0;

            top: 0;

            width: 80mm;

            max-width: 80mm;

            margin: 0;

            padding: 4mm;

            background: white;

            color: black;

            font-family: monospace;
        }

        .no-print {
            display: none !important;
        }

    }
    
    /* =========================================================
   CATEGORY FILTER CONTAINER
========================================================= */

.dashboard-category {
    width: 100%;

    margin-top: 8px;
    margin-bottom: 10px;

    padding: 5px;

    background: rgba(17, 24, 39, .72);

    border: 1px solid rgba(255, 255, 255, .07);

    border-radius: 15px;

    overflow: hidden;

    box-shadow:
        0 4px 14px rgba(0, 0, 0, .12);
}


/* =========================================================
   CATEGORY HORIZONTAL SCROLL
========================================================= */

.category-scroll {
    display: flex;
    align-items: center;

    gap: 6px;

    width: 100%;

    overflow-x: auto;
    overflow-y: hidden;

    white-space: nowrap;

    padding: 1px;

    scrollbar-width: none;

    -webkit-overflow-scrolling: touch;

    overscroll-behavior-x: contain;
}

.category-scroll::-webkit-scrollbar {
    display: none;
}


/* =========================================================
   CATEGORY CHIP
========================================================= */

.category-chip {
    flex: 0 0 auto;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    min-height: 31px;

    padding: 0 12px;

    border-radius: 999px;

    background: transparent;

    border: 1px solid transparent;

    color: #9ca3af;

    font-size: 10px;
    font-weight: 700;

    line-height: 1;

    text-decoration: none;

    white-space: nowrap;

    transition:
        background .18s ease,
        color .18s ease,
        border-color .18s ease,
        box-shadow .18s ease,
        transform .12s ease;
}


/* =========================================================
   CATEGORY HOVER
========================================================= */

.category-chip:hover {
    background: rgba(255, 255, 255, .05);

    color: #e5e7eb;
}


/* =========================================================
   CATEGORY ACTIVE
========================================================= */

.category-chip.active {
    background: #4f46e5;

    border-color: #6366f1;

    color: #ffffff;

    box-shadow:
        0 4px 10px rgba(79, 70, 229, .25);
}


/* =========================================================
   CATEGORY TAP
========================================================= */

.category-chip:active {
    transform: scale(.95);
}


/* =========================================================
   TABLET / DESKTOP
========================================================= */

@media (min-width: 640px) {

    .dashboard-category {
        margin-top: 10px;
        margin-bottom: 12px;

        padding: 6px;
    }

    .category-scroll {
        gap: 7px;
    }

    .category-chip {
        min-height: 33px;

        padding: 0 14px;

        font-size: 11px;
    }

}

</style>

<!-- MODAL SCAN QR -->
<div
    id="qrScannerModal"
    class="fixed inset-0 bg-black/70 z-50 hidden items-center justify-center p-4"
>
    <div class="bg-white rounded-2xl w-full max-w-md p-4">

        <div class="flex items-center justify-between mb-3">
            <h3 class="text-lg font-bold">
                📷 Scan QR Produk
            </h3>

            <button
                type="button"
                onclick="closeQrScanner()"
                class="text-2xl text-gray-500"
            >
                &times;
            </button>
        </div>

        <div
            id="qr-reader"
            class="w-full overflow-hidden rounded-xl"
        ></div>

        <div
            id="qr-scan-status"
            class="text-sm text-gray-600 text-center mt-3"
        >
            Arahkan kamera ke QR produk
        </div>

        <button
            type="button"
            onclick="closeQrScanner()"
            class="w-full mt-4 px-4 py-3 bg-gray-200 rounded-xl font-semibold"
        >
            Tutup
        </button>

    </div>
</div>


{{-- ============================================================
     JAVASCRIPT KASIR
============================================================ --}}
@php
    $priceRulesJson = $activePriceRules->map(function ($rule) {
        return [
            'id' => $rule->id,
            'product_id' => $rule->product_id,
            'type' => $rule->type,
            'min_quantity' => $rule->min_quantity,
            'discount_type' => $rule->discount_type,
            'discount_value' => $rule->discount_value,
            'special_price' => $rule->special_price,
            'start_at' => $rule->start_at?->toIso8601String(),
            'end_at' => $rule->end_at?->toIso8601String(),
        ];
    })->values();
@endphp

<script src="https://unpkg.com/html5-qrcode"></script>
<script>

let cart = [];

let transactionProcessing = false;

const CART_STORAGE_KEY = 'smart_pos_cart';

const priceRules = @json($priceRulesJson);

const scanUrlTemplate = @json(
    route('produk.scan', ['sku' => '__SKU__'])
);


/*
|--------------------------------------------------------------------------
| LOAD CART DARI STORAGE
|--------------------------------------------------------------------------
*/

function loadCart() {

    try {

        const savedCart =
            localStorage.getItem(CART_STORAGE_KEY);

        if (savedCart) {

            const parsedCart =
                JSON.parse(savedCart);

            if (Array.isArray(parsedCart)) {

                cart = parsedCart.map(item => ({

                    id: item.id,

                    name: item.name,

                    basePrice:
                        Number(item.basePrice ?? item.price) || 0,

                    price:
                        Number(item.basePrice ?? item.price) || 0,

                    qty:
                        Number(item.qty) || 0,

                    stock:
                        Number(item.stock) || 0

                })).filter(item =>
                    item.qty > 0 &&
                    item.stock > 0
                );

            }

        }

    } catch (error) {

        console.error(
            'Gagal memuat keranjang:',
            error
        );

        cart = [];

    }

    refreshCartPrices();
}


/*
|--------------------------------------------------------------------------
| SIMPAN CART
|--------------------------------------------------------------------------
*/

function saveCart() {

    try {

        localStorage.setItem(
            CART_STORAGE_KEY,
            JSON.stringify(cart)
        );

    } catch (error) {

        console.error(
            'Gagal menyimpan keranjang:',
            error
        );

    }

}
/*
|--------------------------------------------------------------------------
| FORMAT RUPIAH
|--------------------------------------------------------------------------
*/

function formatRupiah(angka) {

    return 'Rp ' +
        Number(angka || 0).toLocaleString('id-ID');

}

function getRulePrice(item) {

    const basePrice = Number(item.basePrice) || 0;
    const quantity = Number(item.qty) || 0;

    const now = new Date();

    const applicableRules = priceRules.filter(rule => {

        const sameProduct =
            Number(rule.product_id) === Number(item.id);

        const globalRule =
            rule.product_id === null;

        if (!sameProduct && !globalRule) {
            return false;
        }

        if (
            rule.start_at &&
            now < new Date(rule.start_at)
        ) {
            return false;
        }

        if (
            rule.end_at &&
            now > new Date(rule.end_at)
        ) {
            return false;
        }

        return true;
    });

    if (!applicableRules.length) {
        return basePrice;
    }

    // Produk spesifik lebih diutamakan daripada semua produk
    applicableRules.sort((a, b) => {

        const aSpecific = a.product_id !== null ? 1 : 0;
        const bSpecific = b.product_id !== null ? 1 : 0;

        if (aSpecific !== bSpecific) {
            return bSpecific - aSpecific;
        }

        return Number(b.id) - Number(a.id);
    });

    const rule = applicableRules[0];

    // RESELLER / GROSIR
    if (rule.type === 'reseller') {

        if (
            !rule.min_quantity ||
            quantity < Number(rule.min_quantity)
        ) {
            return basePrice;
        }

        // Produk tertentu → harga khusus
        if (
            rule.product_id !== null &&
            rule.special_price !== null
        ) {
            return Math.max(
                0,
                Number(rule.special_price)
            );
        }

        // Semua produk → potongan per unit
        if (
            rule.product_id === null &&
            rule.discount_value !== null
        ) {

            let price = basePrice;

            if (rule.discount_type === 'nominal') {

                price -= Number(
                    rule.discount_value
                );

            } else if (
                rule.discount_type === 'percent'
            ) {

                price -=
                    price *
                    (
                        Number(rule.discount_value) / 100
                    );
            }

            return Math.max(0, price);
        }

        return basePrice;
    }

    // PROMOSI
    if (rule.type === 'promotion') {

        let price = basePrice;

        if (rule.discount_type === 'nominal') {

            price -= Number(
                rule.discount_value
            );

        } else if (
            rule.discount_type === 'percent'
        ) {

            price -=
                price *
                (
                    Number(rule.discount_value) / 100
                );
        }

        return Math.max(0, price);
    }

    return basePrice;
}


function refreshCartPrices() {

    cart.forEach(item => {

        item.price =
            getRulePrice(item);

    });

    saveCart();
}


/*
|--------------------------------------------------------------------------
| TAMBAH PRODUK
|--------------------------------------------------------------------------
*/

function addToCart(id, name, price, stock) {

    stock = parseInt(stock);

    if (stock <= 0) {

        alert('Stok produk habis.');

        return;
    }

    let existingItem =
        cart.find(item => item.id == id);

    if (existingItem) {

        if (existingItem.qty < existingItem.stock) {

            existingItem.qty++;

        } else {

            alert('Stok produk tidak mencukupi!');

            return;
        }

    } else {

        cart.push({

            id: id,

            name: name,

            basePrice: Number(price),

            price: Number(price),

            qty: 1,

            stock: stock

        });

    }

    refreshCartPrices();

    saveCart();

    renderCart();
}


/*
|--------------------------------------------------------------------------
| UPDATE JUMLAH
|--------------------------------------------------------------------------
*/

function updateQty(id, change) {

    let item =
        cart.find(item => item.id == id);

    if (!item) {
        return;
    }

    item.qty += change;

    if (item.qty <= 0) {

        cart =
            cart.filter(i => i.id != id);

    } else if (item.qty > item.stock) {

        alert('Stok maksimum tercapai!');

        item.qty = item.stock;
    }

    refreshCartPrices();

    saveCart();

    renderCart();
}


/*
|--------------------------------------------------------------------------
| HAPUS PRODUK
|--------------------------------------------------------------------------
*/

function removeFromCart(id) {

    cart =
        cart.filter(item => item.id != id);
    
    saveCart();
    renderCart();

}


/*
|--------------------------------------------------------------------------
| HITUNG TOTAL
|--------------------------------------------------------------------------
*/

function calculateCartTotal() {

    let subtotal =
        cart.reduce(
            (sum, item) =>
                sum + (item.price * item.qty),
            0
        );

    let discountType =
        document.getElementById('discountType').value;

    let discountValue =
        parseFloat(
            document.getElementById('discountValue').value
        ) || 0;


    if (discountValue < 0) {
        discountValue = 0;
    }


    let discountAmount = 0;


    if (discountType === 'percent') {

        discountValue =
            Math.min(100, discountValue);

        discountAmount =
            (subtotal * discountValue) / 100;

    } else {

        discountAmount =
            Math.min(subtotal, discountValue);

    }


    let total =
        Math.max(
            0,
            subtotal - discountAmount
        );


    document.getElementById(
        'cart-subtotal'
    ).innerText =
        formatRupiah(subtotal);


    document.getElementById(
        'cart-total'
    ).innerText =
        formatRupiah(total);


    return {

        subtotal: subtotal,

        discountAmount: discountAmount,

        total: total

    };

}


/*
|--------------------------------------------------------------------------
| RENDER KERANJANG
|--------------------------------------------------------------------------
*/

function renderCart() {

    let container =
        document.getElementById('cart-items');


    if (cart.length === 0) {

        container.innerHTML =
            `<p class="text-xs text-gray-400 text-center py-4">
                Belum ada barang di keranjang.
            </p>`;

        calculateCartTotal();

        return;
    }


    container.innerHTML = '';


    cart.forEach(item => {

        container.innerHTML += `

            <div
                class="bg-gray-900/90 border border-white/5 p-2.5 rounded-xl flex justify-between items-center text-xs"
            >

                <div class="flex-1 pr-2">

                    <h5 class="text-white font-medium line-clamp-1">
                        ${escapeHtml(item.name)}
                    </h5>

<div class="text-[11px]">
    ${
        Number(item.price) < Number(item.basePrice)
            ? `
                <span class="text-gray-500 line-through mr-1">
                    ${formatRupiah(item.basePrice)}
                </span>
                <span class="text-emerald-400 font-semibold">
                    ${formatRupiah(item.price)}
                </span>
              `
            : `
                <span class="text-emerald-400">
                    ${formatRupiah(item.price)}
                </span>
              `
    }
</div>

                </div>


                <div class="flex items-center gap-1.5">

                    <button
                        onclick="updateQty('${item.id}', -1)"
                        class="bg-gray-800 text-white w-6 h-6 rounded flex items-center justify-center font-bold hover:bg-gray-700"
                    >
                        -
                    </button>

                    <span class="text-white w-5 text-center font-semibold">
                        ${item.qty}
                    </span>

                    <button
                        onclick="updateQty('${item.id}', 1)"
                        class="bg-gray-800 text-white w-6 h-6 rounded flex items-center justify-center font-bold hover:bg-gray-700"
                    >
                        +
                    </button>

                    <button
                        onclick="removeFromCart('${item.id}')"
                        class="text-rose-400 hover:text-rose-300 ml-1 font-bold px-1"
                    >
                        ✕
                    </button>

                </div>

            </div>

        `;

    });


    calculateCartTotal();

}


/*
|--------------------------------------------------------------------------
| ESCAPE HTML
|--------------------------------------------------------------------------
*/

function escapeHtml(text) {

    const div =
        document.createElement('div');

    div.textContent = text;

    return div.innerHTML;

}


/*
|--------------------------------------------------------------------------
| BUKA MODAL PEMBAYARAN
|--------------------------------------------------------------------------
*/

function openPaymentModal() {

    if (cart.length === 0) {

        alert('Keranjang belanja masih kosong!');

        return;
    }


    let calc =
        calculateCartTotal();


    document.getElementById(
        'modal-total-pay'
    ).innerText =
        formatRupiah(calc.total);


    document.getElementById(
        'pay-amount'
    ).value = '';
    
    document.getElementById(
    'cashbon-customer'
).value = '';

    document.getElementById(
        'pay-change'
    ).innerText =
        formatRupiah(0);


    document.getElementById(
        'payment-modal'
    ).classList.remove('hidden');


    paymentMethodChanged();

}


/*
|--------------------------------------------------------------------------
| TUTUP MODAL
|--------------------------------------------------------------------------
*/

function closePaymentModal() {

    document.getElementById(
        'payment-modal'
    ).classList.add('hidden');

}


/*
|--------------------------------------------------------------------------
| PERUBAHAN METODE PEMBAYARAN
|--------------------------------------------------------------------------
*/

function paymentMethodChanged() {

    const method =
        document.getElementById('pay-method').value;

    const input =
        document.getElementById('pay-amount');

    const customerBox =
        document.getElementById('cashbon-customer-box');

    const qrBox =
        document.getElementById('store-qr-payment-box');

    const accountBox =
        document.getElementById('payment-account-box');

    const accountName =
        document.getElementById('payment-account-name');

    const accountNumber =
        document.getElementById('payment-account-number');

    const calc =
        calculateCartTotal();


    // Reset tampilan
    if (qrBox) {
        qrBox.classList.add('hidden');
    }

    if (accountBox) {
        accountBox.classList.add('hidden');
    }


    /*
    |--------------------------------------------------------------------------
    | TUNAI
    |--------------------------------------------------------------------------
    */

    if (method === 'Tunai') {

        input.value = '';
        input.readOnly = false;
        input.placeholder = 'Ketik nominal uang...';

        calculateChange();
        return;
    }


    /*
    |--------------------------------------------------------------------------
    | QRIS
    |--------------------------------------------------------------------------
    */

    if (method === 'QRIS') {

        if (qrBox) {
            qrBox.classList.remove('hidden');
        }

        input.value = calc.total;
        input.readOnly = true;
        input.placeholder = 'Nominal pembayaran';

        calculateChange();
        return;
    }


    /*
    |--------------------------------------------------------------------------
    | REKENING / E-WALLET
    |--------------------------------------------------------------------------
    */

    if (method.startsWith('account_')) {

        const option =
            document.querySelector(
                `#pay-method option[value="${method}"]`
            );

        if (option) {

            accountName.innerText =
                option.dataset.name || '';

            accountNumber.innerText =
                (
                    option.dataset.accountName || ''
                ) +
                ' • ' +
                (
                    option.dataset.accountNumber || ''
                );

            accountBox.classList.remove('hidden');
        }

        input.value = calc.total;
        input.readOnly = true;
        input.placeholder = 'Nominal pembayaran';

        calculateChange();
        return;
    }


    /*
    |--------------------------------------------------------------------------
    | CASHBON / UTANG
    |--------------------------------------------------------------------------
    */

    if (method === 'Cashbon / Utang') {

        input.value = '';
        input.readOnly = false;
        input.placeholder =
            'Masukkan pembayaran sebagian...';

        calculateChange();
        return;
    }

}


/*
|--------------------------------------------------------------------------
| HITUNG KEMBALIAN
|--------------------------------------------------------------------------
*/

function calculateChange() {

    const method =
        document.getElementById('pay-method').value;

    const label =
        document.getElementById('pay-change-label');

    const result =
        document.getElementById('pay-change');

    let calc =
        calculateCartTotal();

    let paid =
        parseFloat(
            document.getElementById('pay-amount').value
        ) || 0;


    /*
    |--------------------------------------------------------------------------
    | CASHBON / UTANG
    |--------------------------------------------------------------------------
    */

    if (method === 'Cashbon / Utang') {

        const remainingDebt =
            Math.max(
                0,
                calc.total - paid
            );

        label.innerText =
            'Sisa Utang:';

        result.innerText =
            formatRupiah(remainingDebt);

        return;
    }


    /*
    |--------------------------------------------------------------------------
    | PEMBAYARAN BIASA
    |--------------------------------------------------------------------------
    */

    label.innerText =
        'Kembalian:';

    const change =
        paid - calc.total;

    result.innerText =
        formatRupiah(
            change >= 0 ? change : 0
        );
}



/*
|--------------------------------------------------------------------------
| SUBMIT TRANSAKSI
|--------------------------------------------------------------------------
*/

async function submitTransaction() {

    // Cegah double click
    if (transactionProcessing) {
        return;
    }

    transactionProcessing = true;

    const button = document.getElementById(
        'submit-transaction-btn'
    );

    button.disabled = true;
    button.innerText = 'Menyimpan transaksi...';

    try {

        /*
        |--------------------------------------------------------------------------
        | HITUNG TOTAL
        |--------------------------------------------------------------------------
        */

        const calc = calculateCartTotal();

        const method =
            document.getElementById('pay-method').value;

        let paid =
            parseFloat(
                document.getElementById('pay-amount').value
            ) || 0;
            
            const customerId =
    document.getElementById('cashbon-customer').value;
    


        /*
        |--------------------------------------------------------------------------
        | VALIDASI
        |--------------------------------------------------------------------------
        */

        if (cart.length === 0) {
            throw new Error(
                'Keranjang belanja masih kosong!'
            );
        }

        if (
            method === 'Tunai' &&
            paid < calc.total
        ) {
            throw new Error(
                'Nominal uang bayar kurang dari total tagihan!'
            );
        }
        
        /*
|--------------------------------------------------------------------------
| VALIDASI CASHBON
|--------------------------------------------------------------------------
*/

if (method === 'Cashbon / Utang') {
    if (!customerId) {
        throw new Error(
            'Silakan pilih pelanggan untuk transaksi Cashbon / Utang.'
        );
    }

    if (calc.total <= 0) {
        throw new Error(
            'Transaksi Cashbon harus memiliki total tagihan.'
        );
    }

    if (paid < 0) {
        throw new Error(
            'Nominal pembayaran tidak valid.'
        );
    }

    if (paid > calc.total) {
        throw new Error(
            'Nominal pembayaran tidak boleh melebihi total tagihan.'
        );
    }
}


        /*
        |--------------------------------------------------------------------------
        | DATA ITEM
        |--------------------------------------------------------------------------
        */

        const items = cart.map(item => ({
            id: parseInt(item.id),
            quantity: parseInt(item.qty)
        }));


        /*
        |--------------------------------------------------------------------------
        | KIRIM KE SERVER
        |--------------------------------------------------------------------------
        */

        const response = await fetch(
            "{{ route('transaksi.store') }}",
            {
                method: 'POST',

                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN':
                        '{{ csrf_token() }}'
                },

                body: JSON.stringify({
                    items: items,
                    subtotal: calc.subtotal,
                    discount: calc.discountAmount,
                    total: calc.total,
                    paid: paid,
                    payment_method: method,
                    customer_id: customerId || null
                })
            }
        );


        /*
        |--------------------------------------------------------------------------
        | BACA RESPONSE
        |--------------------------------------------------------------------------
        */

        const result = await response.json();


        /*
        |--------------------------------------------------------------------------
        | TRANSAKSI GAGAL
        |--------------------------------------------------------------------------
        */

        if (
            !response.ok ||
            !result.success
        ) {
            throw new Error(
                result.message ||
                'Transaksi gagal disimpan.'
            );
        }
      window.lastTransactionId = result.transaction_id;

        /*
        |--------------------------------------------------------------------------
        | SIAPKAN DATA STRUK
        |--------------------------------------------------------------------------
        */

        const now = new Date();

        document.getElementById(
            'receipt-invoice'
        ).innerText =
            result.invoice_number;

        document.getElementById(
            'receipt-date'
        ).innerText =
            now.toLocaleString('id-ID');
            
/*
|--------------------------------------------------------------------------
| PELANGGAN STRUK
|--------------------------------------------------------------------------
*/

const customerSelect =
    document.getElementById('cashbon-customer');

const selectedCustomer =
    customerSelect.options[
        customerSelect.selectedIndex
    ];

let customerName = '-';

if (
    method === 'Cashbon / Utang' &&
    selectedCustomer
) {
    customerName =
        selectedCustomer.textContent
            .split('—')[0]
            .trim();
}

document.getElementById(
    'receipt-customer'
).innerText =
    method === 'Cashbon / Utang'
        ? 'Pelanggan: ' + customerName
        : '';

        document.getElementById(
            'receipt-subtotal'
        ).innerText =
            formatRupiah(calc.subtotal);

        document.getElementById(
            'receipt-discount'
        ).innerText =
            formatRupiah(calc.discountAmount);

        document.getElementById(
            'receipt-total'
        ).innerText =
            formatRupiah(calc.total);

        document.getElementById(
            'receipt-method'
        ).innerText =
            method;

        document.getElementById(
    'receipt-paid'
).innerText =
    paid === 0
        ? 'BELUM DIBAYAR'
        : formatRupiah(paid);


        const change = Math.max(
    0,
    paid - calc.total
);

const remainingDebt = Math.max(
    0,
    calc.total - paid
);

document.getElementById(
    'receipt-balance-label'
).innerText =
    method === 'Cashbon / Utang'
        ? 'Sisa Utang:'
        : 'Kembali:';

document.getElementById(
    'receipt-change'
).innerText =
    method === 'Cashbon / Utang'
        ? formatRupiah(remainingDebt)
        : formatRupiah(change);


        /*
        |--------------------------------------------------------------------------
        | DETAIL PRODUK STRUK
        |--------------------------------------------------------------------------
        */

        const receiptItems =
            document.getElementById(
                'receipt-items'
            );

        receiptItems.innerHTML = '';

        cart.forEach(item => {

            const row =
                document.createElement('div');

            row.className =
                'flex justify-between gap-2';

            const name =
                document.createElement('span');

            name.textContent =
                `${item.name} x${item.qty}`;

            const price =
                document.createElement('span');

            price.textContent =
                formatRupiah(
                    item.price * item.qty
                );

            row.appendChild(name);
            row.appendChild(price);

            receiptItems.appendChild(row);

        });


        /*
        |--------------------------------------------------------------------------
        | TUTUP MODAL
        |--------------------------------------------------------------------------
        */

        closePaymentModal();


        /*
        |--------------------------------------------------------------------------
        | KOSONGKAN KERANJANG
        |--------------------------------------------------------------------------
        */

        cart = [];
        saveCart();

        renderCart();


        /*
        |--------------------------------------------------------------------------
        | TAMPILKAN TRANSAKSI BERHASIL
        |--------------------------------------------------------------------------
        */

        document.getElementById(
            'success-invoice'
        ).innerText =
            'Invoice: ' +
            result.invoice_number;

        document.getElementById(
            'receipt-actions'
        ).classList.remove('hidden');


        /*
        |--------------------------------------------------------------------------
        | SCROLL KE HASIL
        |--------------------------------------------------------------------------
        */

        document.getElementById(
            'receipt-actions'
        ).scrollIntoView({
            behavior: 'smooth',
            block: 'center'
        });


        /*
        |--------------------------------------------------------------------------
        | PENTING
        |--------------------------------------------------------------------------
        |
        | JANGAN window.print() DI SINI.
        |
        | HP tidak langsung membuka dialog print.
        |
        */


    } catch (error) {

        console.error(
            'Transaction Error:',
            error
        );

        alert(
            error.message ||
            'Terjadi kesalahan saat menyimpan transaksi.'
        );

    } finally {

        /*
        |--------------------------------------------------------------------------
        | AKTIFKAN KEMBALI TOMBOL
        |--------------------------------------------------------------------------
        */

        transactionProcessing = false;

        button.disabled = false;

        button.innerText =
            'Selesaikan & Cetak Struk';

    }

}


/*
|--------------------------------------------------------------------------
| CETAK STRUK
|--------------------------------------------------------------------------
*/

function printReceipt() {

    const transactionId =
        window.lastTransactionId;

    // Pastikan ID transaksi tersedia
    if (!transactionId) {

        alert(
            'Data transaksi tidak ditemukan.'
        );

        return;
    }


    /*
    |--------------------------------------------------------------------------
    | DETEKSI MEDIAN APP
    |--------------------------------------------------------------------------
    */

    const isMedianApp =
        navigator.userAgent
            .toLowerCase()
            .includes('median');


    /*
    |--------------------------------------------------------------------------
    | MEDIAN → PDF
    |--------------------------------------------------------------------------
    */

    if (isMedianApp) {

        const pdfUrl =
            @json(route('transaksi.struk.pdf', ['id' => '__ID__']))
            .replace(
                '__ID__',
                transactionId
            );

        window.open(
            pdfUrl,
            '_blank'
        );

        return;
    }


    /*
    |--------------------------------------------------------------------------
    | BROWSER → PRINT BIASA
    |--------------------------------------------------------------------------
    */

    window.print();

}

/*
|--------------------------------------------------------------------------
| TRANSAKSI BARU
|--------------------------------------------------------------------------
*/

function newTransaction() {


    /*
    |--------------------------------------------------------------------------
    | Sembunyikan aksi struk
    |--------------------------------------------------------------------------
    */

    document.getElementById(
        'receipt-actions'
    ).classList.add('hidden');


    /*
    |--------------------------------------------------------------------------
    | Bersihkan struk lama
    |--------------------------------------------------------------------------
    */

    document.getElementById(
        'receipt-invoice'
    ).innerText = '-';


    document.getElementById(
        'receipt-date'
    ).innerText = '-';
    
    document.getElementById(
    'receipt-customer'
).innerText = '';


    document.getElementById(
        'receipt-items'
    ).innerHTML = '';


    document.getElementById(
        'receipt-subtotal'
    ).innerText = 'Rp 0';


    document.getElementById(
        'receipt-discount'
    ).innerText = 'Rp 0';


    document.getElementById(
        'receipt-total'
    ).innerText = 'Rp 0';


    document.getElementById(
        'receipt-paid'
    ).innerText = 'Rp 0';


    document.getElementById(
    'receipt-change'
      ).innerText = 'Rp 0';

    document.getElementById(
    'receipt-method'
      ).innerText = 'Tunai';


    /*
    |--------------------------------------------------------------------------
    | Reset diskon
    |--------------------------------------------------------------------------
    */

    document.getElementById(
        'discountType'
    ).value = 'nominal';


    document.getElementById(
        'discountValue'
    ).value = 0;


    /*
    |--------------------------------------------------------------------------
    | Reset pembayaran
    |--------------------------------------------------------------------------
    */

    document.getElementById(
        'pay-method'
    ).value = 'Tunai';
    
    const qrBox = document.getElementById(
    'store-qr-payment-box'
);

if (qrBox) {
    qrBox.classList.add('hidden');
}

const accountBox =
    document.getElementById('payment-account-box');

if (accountBox) {
    accountBox.classList.add('hidden');
}

    document.getElementById(
        'pay-amount'
    ).value = '';


    document.getElementById(
        'pay-amount'
    ).readOnly = false;
    
    document.getElementById(
    'cashbon-customer'
).value = '';

document.getElementById(
    'pay-change'
).innerText =
    formatRupiah(0);


    /*
    |--------------------------------------------------------------------------
    | Scroll ke atas
    |--------------------------------------------------------------------------
    */

    window.scrollTo({

        top: 0,

        behavior: 'smooth'

    });

}

/*
|--------------------------------------------------------------------------
| TAMBAH PELANGGAN DARI CASHBON
|--------------------------------------------------------------------------
*/

function openAddCustomerModal() {

    document.getElementById(
        'add-customer-modal'
    ).classList.remove('hidden');

    document.getElementById(
        'add-customer-modal'
    ).classList.add('flex');

    setTimeout(() => {

        document.getElementById(
            'new-customer-name'
        ).focus();

    }, 100);
}


function closeAddCustomerModal() {

    document.getElementById(
        'add-customer-modal'
    ).classList.add('hidden');

    document.getElementById(
        'add-customer-modal'
    ).classList.remove('flex');

}


/*
|--------------------------------------------------------------------------
| SIMPAN PELANGGAN BARU
|--------------------------------------------------------------------------
*/

async function saveNewCustomer() {

    const nameInput =
        document.getElementById(
            'new-customer-name'
        );

    const phoneInput =
        document.getElementById(
            'new-customer-phone'
        );

    const addressInput =
        document.getElementById(
            'new-customer-address'
        );
        
        const typeInput =
    document.getElementById(
        'new-customer-type'
    );

    const button =
        document.getElementById(
            'save-new-customer-btn'
        );


    const name =
        nameInput.value.trim();

    const phone =
        phoneInput.value.trim();

    const address =
        addressInput.value.trim();
        
        const customerType =
    typeInput.value;


    if (!name) {

        alert(
            'Nama pelanggan wajib diisi.'
        );

        nameInput.focus();

        return;
    }


    button.disabled = true;

    button.innerText =
        'Menyimpan...';


    try {

        const response =
            await fetch(
                "{{ route('pelanggan.ajax.store') }}",
                {
                    method: 'POST',

                    headers: {
                        'Content-Type':
                            'application/json',

                        'Accept':
                            'application/json',

                        'X-CSRF-TOKEN':
                            '{{ csrf_token() }}'
                    },

                    body: JSON.stringify({
                    name: name,
                    phone: phone,
                    address: address,
                    customer_type: customerType
                })
                }
            );


        const result =
            await response.json();


        if (
            !response.ok ||
            !result.success
        ) {

            throw new Error(
                result.message ||
                'Gagal menyimpan pelanggan.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | MASUKKAN PELANGGAN BARU KE DROPDOWN
        |--------------------------------------------------------------------------
        */

        const customerSelect =
            document.getElementById(
                'cashbon-customer'
            );


        const option =
            document.createElement('option');


        option.value =
            result.customer.id;
            
            option.dataset.type = result.customer?.customer_type ?? 'umum';


        option.textContent =
            result.customer.phone
                ? result.customer.name +
                  ' — ' +
                  result.customer.phone
                : result.customer.name;


        customerSelect.appendChild(
            option
        );


        /*
        |--------------------------------------------------------------------------
        | LANGSUNG PILIH
        |--------------------------------------------------------------------------
        */

        customerSelect.value =
            result.customer.id;


        /*
        |--------------------------------------------------------------------------
        | BERSIHKAN FORM
        |--------------------------------------------------------------------------
        */

        nameInput.value = '';

        phoneInput.value = '';
        
        addressInput.value = '';
        
        typeInput.value = 'umum';


        closeAddCustomerModal();


        /*
        |--------------------------------------------------------------------------
        | INFO
        |--------------------------------------------------------------------------
        */

        alert(
            'Pelanggan "' +
            result.customer.name +
            '" berhasil ditambahkan.'
        );


    } catch (error) {

        console.error(
            'Add Customer Error:',
            error
        );

        alert(
            error.message ||
            'Gagal menyimpan pelanggan.'
        );

    } finally {

        button.disabled = false;

        button.innerText =
            'Simpan Pelanggan';

    }

}


/*
|--------------------------------------------------------------------------
| INIT
|--------------------------------------------------------------------------
*/

document.addEventListener(
    'DOMContentLoaded',
    function () {
        
        loadCart();
        renderCart();
        handlePendingScan();

    }
);

// ======================================================
// HANDLE GLOBAL SCAN DARI HALAMAN LAIN
// ======================================================

async function handlePendingScan() {

    const params = new URLSearchParams(
        window.location.search
    );

    const scanSku = params.get('scan_sku');

    if (!scanSku) {
        return;
    }

    // Bersihkan URL agar scan tidak diproses ulang
    window.history.replaceState(
        {},
        document.title,
        window.location.pathname
    );

    const sku = String(scanSku).trim();

    if (!sku) {
        return;
    }

    try {

        const url = scanUrlTemplate.replace(
            '__SKU__',
            encodeURIComponent(sku)
        );

        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        if (!response.ok || !data.success) {

            alert(
                data.message ||
                'Produk hasil scan tidak ditemukan.'
            );

            return;
        }

        const product = data.product;

        // Masukkan produk ke keranjang
        addToCart(
            product.id,
            product.name,
            product.price,
            product.stock
        );

        console.log(
            'Produk dari Global Scanner:',
            product
        );

    } catch (error) {

        console.error(
            'Pending Scan Error:',
            error
        );

        alert(
            'Gagal mengambil produk hasil scan.'
        );
    }
}

// ======================================================
// QR SCANNER
// ======================================================

let html5QrCode = null;
let qrScanning = false;
let qrProcessing = false;



function openQrScanner() {

    const modal = document.getElementById('qrScannerModal');

    modal.classList.remove('hidden');
    modal.classList.add('flex');

    document.getElementById('qr-scan-status').innerText =
        'Mengaktifkan kamera...';

    startQrScanner();
}


async function startQrScanner() {

    if (typeof Html5Qrcode === 'undefined') {

        alert(
            'Scanner QR gagal dimuat. Periksa koneksi internet.'
        );

        closeQrScanner();

        return;
    }

    if (qrScanning) {
        return;
    }

    qrProcessing = false;

    html5QrCode = new Html5Qrcode('qr-reader');

    try {

        await html5QrCode.start(

            {
                facingMode: 'environment'
            },

            {
                fps: 10,

                qrbox: {
                    width: 250,
                    height: 250
                }
            },

            onQrCodeSuccess,

            function () {
                // Abaikan error scan sementara.
            }

        );

        qrScanning = true;

        document.getElementById('qr-scan-status').innerText =
            'Arahkan kamera ke QR produk';

    } catch (error) {

        console.error(error);

        document.getElementById('qr-scan-status').innerText =
            'Kamera tidak dapat digunakan.';

        alert(
            'Tidak dapat mengakses kamera. Pastikan izin kamera diberikan.'
        );
    }
}


async function onQrCodeSuccess(decodedText) {

    if (qrProcessing) {
        return;
    }

    qrProcessing = true;

    const sku = decodedText.trim();

    document.getElementById('qr-scan-status').innerText =
        'Mencari produk ' + sku + '...';


    try {

        // Stop kamera sementara
        await stopQrScanner();


        const url = scanUrlTemplate.replace(
            '__SKU__',
            encodeURIComponent(sku)
        );


        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        });


        const data = await response.json();


        if (!response.ok || !data.success) {

            alert(
                data.message ||
                'Produk tidak ditemukan.'
            );

            qrProcessing = false;

            return;
        }


        const product = data.product;


        // Masukkan ke keranjang
        addToCart(
            product.id,
            product.name,
            product.price,
            product.stock
        );


        // Tutup scanner
        closeQrScanner();


        // Beri feedback
        console.log(
            'Produk berhasil discan:',
            product
        );

    } catch (error) {

        console.error(
            'QR Scanner Error:',
            error
        );

        alert(
            'Terjadi kesalahan saat membaca produk.'
        );

        qrProcessing = false;
    }
}


async function stopQrScanner() {

    if (
        html5QrCode &&
        qrScanning
    ) {

        try {

            await html5QrCode.stop();

            await html5QrCode.clear();

        } catch (error) {

            console.error(
                'Gagal menghentikan scanner:',
                error
            );
        }
    }

    qrScanning = false;
}


async function closeQrScanner() {

    await stopQrScanner();

    qrProcessing = false;

    const modal =
        document.getElementById('qrScannerModal');

    modal.classList.add('hidden');
    modal.classList.remove('flex');

    document.getElementById('qr-reader').innerHTML = '';

    document.getElementById('qr-scan-status').innerText =
        'Arahkan kamera ke QR produk';
}

</script>

@endsection