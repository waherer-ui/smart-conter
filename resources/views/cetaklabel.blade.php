@extends('layouts.app')

@section('header', 'Cetak Label Harga')

@section('content')

<div class="max-w-7xl mx-auto px-4 py-6">

    {{-- =========================================================
       HEADER
       ========================================================= --}}

    <div class="bg-slate-800 border border-slate-700 rounded-xl p-5 mb-5 no-print">

        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

            <div>
                <h2 class="text-xl font-bold text-white">
                    🏷️ Cetak Label Harga
                </h2>

                <p class="text-sm text-slate-400 mt-1">
                    Pilih satu produk, tentukan jumlah label, lalu cetak.
                </p>
            </div>

            <button
                type="button"
                onclick="cetakLabel()"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg font-semibold text-sm shadow"
            >
                🖨️ Cetak Label
            </button>

        </div>

    </div>


    {{-- =========================================================
       PENGATURAN
       ========================================================= --}}

    <div class="bg-slate-800 border border-slate-700 rounded-xl p-5 mb-5 no-print">

        <h3 class="text-sm font-bold text-white mb-4">
            Pengaturan Label
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

            {{-- UKURAN LABEL --}}

            <div>

                <label class="block text-xs text-slate-400 mb-2">
                    Ukuran Label
                </label>

                <select
                    id="labelSize"
                    onchange="ubahUkuranLabel()"
                    class="w-full bg-slate-900 border border-slate-700 text-white rounded-lg px-3 py-2 text-sm"
                >

                    <option value="small">
                        Kecil
                    </option>

                    <option value="medium" selected>
                        Sedang
                    </option>

                    <option value="large">
                        Besar
                    </option>

                </select>

            </div>


            {{-- JUMLAH LABEL --}}

            <div>

                <label class="block text-xs text-slate-400 mb-2">
                    Jumlah Label
                </label>

                <input
                    type="number"
                    id="jumlahLabel"
                    value="1"
                    min="1"
                    max="100"
                    onchange="validasiJumlah()"
                    class="w-full bg-slate-900 border border-slate-700 text-white rounded-lg px-3 py-2 text-sm"
                >

            </div>


            {{-- INFORMASI PRODUK --}}

            <div>

                <label class="block text-xs text-slate-400 mb-2">
                    Produk Terpilih
                </label>

                <div
                    id="selectedInfo"
                    class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-500"
                >
                    Belum ada produk dipilih
                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
       PETUNJUK
       ========================================================= --}}

    <div class="bg-indigo-500/10 border border-indigo-500/20 rounded-xl p-4 mb-5 no-print">

        <div class="flex gap-3">

            <div class="text-xl">
                👆
            </div>

            <div>

                <p class="text-sm font-semibold text-indigo-300">
                    Pilih Produk
                </p>

                <p class="text-xs text-slate-400 mt-1">
                    Tekan kartu produk yang ingin dicetak.
                    Hanya satu produk yang dapat dipilih dalam satu kali cetak.
                </p>

            </div>

        </div>

    </div>


    {{-- =========================================================
       DAFTAR PRODUK
       ========================================================= --}}

    <div
        id="productSelection"
        class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 no-print"
    >

        @forelse($products ?? [] as $product)

            <div
                class="product-card"
                data-product-id="{{ $product->id }}"
                data-product-name="{{ $product->name }}"
                data-product-sku="{{ $product->sku }}"
                data-product-price="{{ $product->price }}"
                data-product-brand="{{ $product->brand }}"
                onclick="pilihProduk(this)"
            >

                {{-- CHECK --}}
                <div class="product-check">
                    ✓
                </div>


                {{-- FOTO PRODUK --}}

                <div class="product-image">

                    @if($product->image)

                        <img
                            src="{{ asset('products/' . $product->image) }}"
                            alt="{{ $product->name }}"
                        >

                    @else

                        <div class="no-image">
                            📦
                        </div>

                    @endif

                </div>


                {{-- INFO PRODUK --}}

                <div class="p-3">

                    <div class="product-name">
                        {{ $product->name }}
                    </div>


                    @if($product->brand)

                        <div class="product-brand">
                            {{ $product->brand }}
                        </div>

                    @endif


                    <div class="product-price">
                        Rp {{ number_format($product->price, 0, ',', '.') }}
                    </div>


                    <div class="product-sku">
                        {{ $product->sku }}
                    </div>

                </div>

            </div>

        @empty

            <div class="col-span-full text-center py-12">

                <div class="text-4xl mb-3">
                    📦
                </div>

                <p class="text-slate-400 text-sm">
                    Belum ada produk.
                </p>

            </div>

        @endforelse

    </div>


    {{-- =========================================================
       PREVIEW CETAK
       ========================================================= --}}

    <div
        id="printArea"
        class="hidden"
    >

        <div
            id="labelContainer"
            class="label-container"
        >

        </div>

    </div>


    {{-- PRODUK TERPILIH --}}
    <input
        type="hidden"
        id="selectedProductId"
        value=""
    >

</div>


<style>

/* =========================================================
   KARTU PRODUK
   ========================================================= */

.product-card {

    position: relative;

    background: #1e293b;

    border: 2px solid #334155;

    border-radius: 12px;

    overflow: hidden;

    cursor: pointer;

    transition:
        transform 0.15s ease,
        border-color 0.15s ease,
        box-shadow 0.15s ease;

}

.product-card:hover {

    transform: translateY(-2px);

    border-color: #6366f1;

}


.product-card.selected {

    border-color: #6366f1;

    box-shadow:
        0 0 0 3px rgba(99,102,241,0.25);

}


/* =========================================================
   CHECK PRODUK
   ========================================================= */

.product-check {

    position: absolute;

    top: 8px;
    right: 8px;

    width: 28px;
    height: 28px;

    border-radius: 999px;

    background: #4f46e5;

    color: white;

    display: none;

    align-items: center;
    justify-content: center;

    font-size: 15px;

    font-weight: 800;

    z-index: 10;

}

.product-card.selected .product-check {

    display: flex;

}


/* =========================================================
   FOTO PRODUK
   ========================================================= */

.product-image {

    width: 100%;
    height: 150px;

    background: #0f172a;

    display: flex;

    align-items: center;

    justify-content: center;

    overflow: hidden;

}


.product-image img {

    width: 100%;
    height: 100%;

    object-fit: cover;

}


.no-image {

    font-size: 42px;

    opacity: 0.6;

}


/* =========================================================
   INFO PRODUK
   ========================================================= */

.product-name {

    color: white;

    font-size: 13px;

    font-weight: 700;

    line-height: 1.3;

    min-height: 34px;

}


.product-brand {

    color: #94a3b8;

    font-size: 10px;

    margin-top: 3px;

}


.product-price {

    color: #34d399;

    font-size: 14px;

    font-weight: 800;

    margin-top: 8px;

}


.product-sku {

    color: #818cf8;

    font-family: monospace;

    font-size: 10px;

    margin-top: 3px;

}


/* =========================================================
   LABEL
   ========================================================= */

.label-container {

    display: flex;

    flex-wrap: wrap;

    gap: 5mm;

    align-items: flex-start;

}


.price-label {

    background: white;

    color: #111827;

    border: 1px solid #d1d5db;

    border-radius: 6px;

    width: 180px;

    min-height: 230px;

    padding: 10px;

    display: flex;

    flex-direction: column;

    align-items: center;

    justify-content: flex-start;

    text-align: center;

    box-sizing: border-box;

    page-break-inside: avoid;

    break-inside: avoid;

}


/* =========================================================
   NAMA
   ========================================================= */

.label-name {

    width: 100%;

    font-size: 13px;

    font-weight: 700;

    line-height: 1.25;

    min-height: 32px;

    display: flex;

    align-items: center;

    justify-content: center;

    overflow: hidden;

}


/* =========================================================
   BRAND
   ========================================================= */

.label-brand {

    font-size: 10px;

    color: #6b7280;

    margin-top: 2px;

    margin-bottom: 5px;

}


/* =========================================================
   QR
   ========================================================= */

.qr-wrapper {

    display: flex;

    align-items: center;

    justify-content: center;

    margin: 5px 0;

}


.qr-wrapper svg {

    display: block;

    max-width: 100%;

}


/* =========================================================
   HARGA
   ========================================================= */

.label-price {

    font-size: 18px;

    font-weight: 800;

    color: #111827;

    margin-top: 3px;

}


/* =========================================================
   SKU
   ========================================================= */

.label-sku {

    font-size: 9px;

    color: #6b7280;

    margin-top: 3px;

}


/* =========================================================
   UKURAN KECIL
   ========================================================= */

.label-small {

    width: 150px;

    min-height: 190px;

}


.label-small .label-name {

    font-size: 11px;

}


.label-small .label-price {

    font-size: 15px;

}


.label-small .qr-wrapper svg {

    width: 85px;

    height: 85px;

}


/* =========================================================
   UKURAN SEDANG
   ========================================================= */

.label-medium {

    width: 180px;

    min-height: 230px;

}


/* =========================================================
   UKURAN BESAR
   ========================================================= */

.label-large {

    width: 220px;

    min-height: 280px;

}


.label-large .label-name {

    font-size: 15px;

}


.label-large .label-price {

    font-size: 21px;

}


.label-large .qr-wrapper svg {

    width: 140px;

    height: 140px;

}


/* =========================================================
   PRINT
   ========================================================= */

@media print {

    @page {

        size: A4;

        margin: 8mm;

    }


    body {

        background: white !important;

    }


    header,
    nav,
    aside,
    .no-print {

        display: none !important;

    }


    #printArea {

        display: block !important;

    }


    .label-container {

        gap: 5mm;

    }


    .price-label {

        border: 1px solid #999;

        border-radius: 0;

        box-shadow: none;

        page-break-inside: avoid;

        break-inside: avoid;

    }

}


/* =========================================================
   MOBILE
   ========================================================= */

@media (max-width: 640px) {

    .product-image {

        height: 120px;

    }


    .product-name {

        font-size: 12px;

    }


    .product-price {

        font-size: 13px;

    }

}

</style>


<script>

/* =========================================================
   PRODUK TERPILIH
   ========================================================= */

let selectedProduct = null;


/* =========================================================
   PILIH PRODUK
   ========================================================= */

function pilihProduk(element) {

    const semuaProduk =
        document.querySelectorAll('.product-card');


    // Hapus pilihan sebelumnya

    semuaProduk.forEach(function(card) {

        card.classList.remove('selected');

    });


    // Pilih produk

    element.classList.add('selected');


    selectedProduct = {
    id: element.dataset.productId,
    name: element.dataset.productName,
    sku: element.dataset.productSku,
    price: element.dataset.productPrice,
    brand: element.dataset.productBrand
};


    // Simpan ID

    document.getElementById(
        'selectedProductId'
    ).value = selectedProduct.id;


    // Tampilkan informasi

    document.getElementById(
        'selectedInfo'
    ).textContent =
        selectedProduct.name +
        ' • ' +
        selectedProduct.sku;

}


/* =========================================================
   VALIDASI JUMLAH
   ========================================================= */

function validasiJumlah() {

    let jumlah =
        parseInt(
            document.getElementById('jumlahLabel').value
        );


    if (isNaN(jumlah) || jumlah < 1) {

        jumlah = 1;

    }


    if (jumlah > 100) {

        jumlah = 100;

    }


    document.getElementById(
        'jumlahLabel'
    ).value = jumlah;

}


/* =========================================================
   UBAH UKURAN LABEL
   ========================================================= */

function ubahUkuranLabel() {

    const ukuran =
        document.getElementById('labelSize').value;


    const labels =
        document.querySelectorAll('.price-label');


    labels.forEach(function(label) {

        label.classList.remove(
            'label-small',
            'label-medium',
            'label-large'
        );


        if (ukuran === 'small') {

            label.classList.add(
                'label-small'
            );

        }

        else if (ukuran === 'large') {

            label.classList.add(
                'label-large'
            );

        }

        else {

            label.classList.add(
                'label-medium'
            );

        }

    });

}


/* =========================================================
   BUAT LABEL
   ========================================================= */

function buatLabel() {

    if (!selectedProduct) {

        return false;

    }


    const jumlah =
        parseInt(
            document.getElementById('jumlahLabel').value
        ) || 1;


    const ukuran =
        document.getElementById('labelSize').value;


    const container =
        document.getElementById('labelContainer');


    container.innerHTML = '';


    for (
        let i = 0;
        i < jumlah;
        i++
    ) {

        const label =
            document.createElement('div');


        label.className =
            'price-label';


        if (ukuran === 'small') {

            label.classList.add(
                'label-small'
            );

        }

        else if (ukuran === 'large') {

            label.classList.add(
                'label-large'
            );

        }

        else {

            label.classList.add(
                'label-medium'
            );

        }


        label.innerHTML = `

            <div class="label-name">
                ${escapeHtml(selectedProduct.name)}
            </div>

            ${
                selectedProduct.brand
                ? `
                    <div class="label-brand">
                        ${escapeHtml(selectedProduct.brand)}
                    </div>
                  `
                : ''
            }

            <div class="qr-wrapper">

                {!! QrCode::size(110)->margin(1)->generate('${selectedProduct.sku}') !!}

            </div>

            <div class="label-sku">
                ${escapeHtml(selectedProduct.sku)}
            </div>

        `;


        container.appendChild(label);

    }


    return true;

}


/* =========================================================
   CETAK LABEL
   ========================================================= */

function cetakLabel() {

    if (!selectedProduct) {

        alert(
            'Silakan pilih satu produk terlebih dahulu.'
        );

        return;

    }


    validasiJumlah();


    /*
    |--------------------------------------------------------------------------
    | Buat preview label
    |--------------------------------------------------------------------------
    */

    if (!buatLabel()) {

        return;

    }


    /*
    |--------------------------------------------------------------------------
    | Cetak
    |--------------------------------------------------------------------------
    */

    window.print();

}


/* =========================================================
   ESCAPE HTML
   ========================================================= */

function escapeHtml(text) {

    const div =
        document.createElement('div');

    div.textContent =
        text ?? '';

    return div.innerHTML;

}

</script>

@endsection