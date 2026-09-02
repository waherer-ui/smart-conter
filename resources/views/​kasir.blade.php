@extends('layouts.app')

@section('header', 'Kasir & Transaksi Penjualan')

@section('content')

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 pb-12">

    {{-- ============================================================
         BAGIAN KIRI : KATALOG PRODUK
    ============================================================ --}}
    <div class="lg:col-span-2 space-y-4">

        {{-- PENCARIAN --}}
        <div class="bg-gray-800/80 border border-white/10 rounded-2xl p-4 shadow-xl backdrop-blur-md">

            <form action="{{ route('kasir.index') }}" method="GET" class="flex gap-2">

                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari nama barang, kategori, atau kode SKU..."
                    class="w-full bg-gray-900 border border-white/10 rounded-xl px-4 py-2.5 text-white text-sm focus:ring-2 focus:ring-indigo-500 outline-none"
                >
                                <button
                    type="button"
                    onclick="openQrScanner()"
                    class="px-4 py-2 bg-black text-white rounded-lg hover:bg-gray-800"
                >
                    📷
                </button>

                <button
                    type="submit"
                    class="bg-indigo-600 hover:bg-indigo-500 text-white px-5 py-2.5 rounded-xl text-xs font-medium transition shadow"
                >
                    Cari
                </button>

                @if(request('search') || request('category'))

                    <a
                        href="{{ route('kasir.index') }}"
                        class="bg-gray-700 hover:bg-gray-600 text-gray-300 px-3 py-2.5 rounded-xl text-xs font-medium flex items-center transition"
                    >
                        Reset
                    </a>

                @endif

            </form>

        </div>


        {{-- KATEGORI --}}
        <div class="bg-gray-800/80 border border-white/10 rounded-2xl p-4 shadow-xl backdrop-blur-md">

            <form
                action="{{ route('kasir.index') }}"
                method="GET"
                id="kasirCategoryForm"
            >

                @if(request('search'))

                    <input
                        type="hidden"
                        name="search"
                        value="{{ request('search') }}"
                    >

                @endif

                <div class="flex items-center gap-2">

                    <span class="text-xs text-gray-400 font-medium">
                        Kategori:
                    </span>

                    <select
                        name="category"
                        onchange="document.getElementById('kasirCategoryForm').submit()"
                        class="w-full bg-gray-900 border border-white/10 rounded-xl px-4 py-2 text-white text-xs outline-none focus:ring-2 focus:ring-indigo-500 cursor-pointer"
                    >

                        <option
                            value="all"
                            {{ (!request('category') || request('category') == 'all') ? 'selected' : '' }}
                        >
                            Semua Kategori
                        </option>

                        @isset($categories)

                            @foreach($categories as $cat)

                                <option
                                    value="{{ $cat }}"
                                    {{ request('category') == $cat ? 'selected' : '' }}
                                >
                                    {{ $cat }}
                                </option>

                            @endforeach

                        @endisset

                    </select>

                </div>

            </form>

        </div>


        {{-- DAFTAR PRODUK --}}
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
                <img src="{{ asset('products/' . $p->image) }}" alt="{{ $p->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
            @else
                <span class="text-[10px] text-gray-500">No Img</span>
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
<div
    id="payment-modal"
    class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
>

    <div class="bg-gray-800 border border-white/15 rounded-2xl w-full max-w-md p-6 shadow-2xl relative space-y-4">

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


        <div class="bg-indigo-950/40 border border-indigo-500/30 p-4 rounded-xl text-center">

            <span class="text-xs text-gray-400">
                Total Tagihan
            </span>

            <h2
                id="modal-total-pay"
                class="text-emerald-400 text-2xl font-bold mt-1"
            >
                Rp 0
            </h2>

        </div>


        <div>

            <label class="block text-xs font-medium text-gray-300 mb-1">
                Metode Pembayaran
            </label>

            <select
                id="pay-method"
                onchange="paymentMethodChanged()"
                class="w-full bg-gray-900 border border-white/10 rounded-xl px-3 py-2 text-white text-xs outline-none"
            >

                <option value="Tunai">
                    Tunai (Cash)
                </option>

                <option value="QRIS / Transfer">
                    QRIS / Transfer
                </option>

                <option value="Debit Card">
                    Debit Card
                </option>

            </select>

        </div>


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

            <span class="text-gray-300">
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
                class="flex-1 bg-gray-700 hover:bg-gray-600 text-gray-300 py-2.5 rounded-xl text-xs font-medium transition"
            >
                Batal
            </button>

            <button
                type="button"
                id="submit-transaction-btn"
                onclick="submitTransaction()"
                class="flex-1 bg-indigo-600 hover:bg-indigo-500 text-white py-2.5 rounded-xl text-xs font-semibold transition shadow"
            >
                Selesaikan & Cetak Struk
            </button>

        </div>

    </div>

</div>


{{-- ============================================================
     STRUK
============================================================ --}}
<div
    id="print-receipt"
    class="hidden print:block font-mono text-black bg-white p-4 max-w-[300px] mx-auto text-xs"
>

    <div class="text-center pb-2 border-b border-dashed border-black">

        <h2 class="font-bold text-sm">
            SMART POS KONTER
        </h2>

        <p class="text-[10px]">
            Pusat Aksesoris & Servis HP
        </p>

        <p class="text-[10px]" id="receipt-invoice">
            -
        </p>

        <p class="text-[10px]" id="receipt-date">
            -
        </p>

    </div>


    <div
        class="py-2 border-b border-dashed border-black space-y-1"
        id="receipt-items"
    >
    </div>


    <div class="py-2 border-b border-dashed border-black space-y-1 text-[11px]">

        <div class="flex justify-between">
            <span>Subtotal:</span>
            <span id="receipt-subtotal">Rp 0</span>
        </div>

        <div class="flex justify-between">
            <span>Diskon:</span>
            <span id="receipt-discount">Rp 0</span>
        </div>

        <div class="flex justify-between font-bold text-xs pt-1">
            <span>TOTAL:</span>
            <span id="receipt-total">Rp 0</span>
        </div>

        <div class="flex justify-between">
            <span>
                Bayar (<span id="receipt-method">Tunai</span>):
            </span>

            <span id="receipt-paid">
                Rp 0
            </span>
        </div>

        <div class="flex justify-between">
            <span>Kembali:</span>
            <span id="receipt-change">Rp 0</span>
        </div>

    </div>


    <div class="text-center pt-3 text-[10px]">

        <p>
            Terima Kasih Atas Kunjungan Anda!
        </p>

        <p>
            Barang yang sudah dibeli tidak dapat ditukar.
        </p>

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
<script src="https://unpkg.com/html5-qrcode"></script>
<script>

let cart = [];

let transactionProcessing = false;


/*
|--------------------------------------------------------------------------
| FORMAT RUPIAH
|--------------------------------------------------------------------------
*/

function formatRupiah(angka) {

    return 'Rp ' +
        Number(angka || 0).toLocaleString('id-ID');

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

            price: Number(price),

            qty: 1,

            stock: stock

        });

    }

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

                    <p class="text-emerald-400 text-[11px]">
                        ${formatRupiah(item.price)}
                    </p>

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

    let method =
        document.getElementById('pay-method').value;

    let input =
        document.getElementById('pay-amount');

    let calc =
        calculateCartTotal();


    if (method !== 'Tunai') {

        input.value =
            calc.total;

        input.readOnly = true;

        calculateChange();

    } else {

        input.value = '';

        input.readOnly = false;

        input.placeholder =
            'Ketik nominal uang...';

        calculateChange();

    }

}


/*
|--------------------------------------------------------------------------
| HITUNG KEMBALIAN
|--------------------------------------------------------------------------
*/

function calculateChange() {

    let calc =
        calculateCartTotal();


    let paid =
        parseFloat(
            document.getElementById('pay-amount').value
        ) || 0;


    let change =
        paid - calc.total;


    document.getElementById(
        'pay-change'
    ).innerText =
        formatRupiah(
            change >= 0 ? change : 0
        );

}


/*
|--------------------------------------------------------------------------
| SUBMIT TRANSAKSI
|--------------------------------------------------------------------------
*/

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

        const paid =
            parseFloat(
                document.getElementById('pay-amount').value
            ) || 0;


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
                    payment_method: method
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
            formatRupiah(paid);


        const change = Math.max(
            0,
            paid - calc.total
        );

        document.getElementById(
            'receipt-change'
        ).innerText =
            formatRupiah(change);


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

    /*
    |--------------------------------------------------------------------------
    | Print hanya dipanggil ketika user menekan tombol.
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


    document.getElementById(
        'pay-amount'
    ).value = '';


    document.getElementById(
        'pay-amount'
    ).readOnly = false;


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
| INIT
|--------------------------------------------------------------------------
*/

document.addEventListener(
    'DOMContentLoaded',
    function () {

        renderCart();

    }
);

// ======================================================
// QR SCANNER
// ======================================================

let html5QrCode = null;
let qrScanning = false;
let qrProcessing = false;

const scanUrlTemplate = @json(
    route('produk.scan', ['sku' => '__SKU__'])
);


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