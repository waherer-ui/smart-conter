@extends('layouts.app')

@section('title', 'Dashboard')
@section('header', 'Dashboard')

@section('content')

<div class="space-y-6 pb-24">

{{-- =========================================================
         HERO DASHBOARD / ETALASE KONTER
    ========================================================== --}}
    <div class="relative overflow-hidden bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl p-5 sm:p-6 text-white shadow-xl">

        {{-- Efek dekorasi ringan --}}
        <div class="absolute -top-10 -right-10 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
        <div class="absolute -bottom-12 -left-8 w-32 h-32 bg-indigo-300/10 rounded-full blur-2xl"></div>

        <div class="relative">

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-5">

                {{-- INFORMASI KONTER --}}
                <div class="min-w-0">

                    <div class="flex items-center gap-2 mb-2">
                        <span class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/10 border border-white/10 text-[10px] font-medium">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-300 animate-pulse"></span>
                            Sistem Aktif
                        </span>
                    </div>

                    <h2 class="text-xl sm:text-2xl font-bold tracking-tight">
                        Etalase & Servis Konter HP
                    </h2>

                    <p class="text-sm text-blue-100 mt-1 leading-5">
                        Lihat produk, layanan, harga, dan ketersediaan stok.
                    </p>

                </div>
  </div>


            {{-- =================================================
                 INFORMASI BERJALAN
            ================================================== --}}
            <div class="mt-5 pt-3 border-t border-white/10 overflow-hidden">

                <div class="dashboard-marquee flex items-center gap-8 whitespace-nowrap text-[11px] text-blue-100">

                    <span>
                        ✦ Produk berkualitas untuk kebutuhan konter
                    </span>

                    <span>
                        ✦ Cek stok sebelum melakukan transaksi
                    </span>

                    <span>
                        ✦ Kelola produk dan penjualan dengan mudah
                    </span>

                    <span>
                        ✦ Smart POS — sederhana, cepat, dan ringan
                    </span>

                </div>

            </div>

        </div>

    </div>
    
    {{-- =========================================================
     PENCARIAN PRODUK
========================================================== --}}
<div class="bg-gray-800/80 border border-white/10 rounded-2xl p-4 shadow-xl backdrop-blur-md">

    <form
        action="{{ route('dashboard') }}"
        method="GET"
        class="flex gap-2"
    >

        {{-- SEARCH --}}
        <div class="relative flex-1">

            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">
                🔎
            </span>

            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Cari nama produk / SKU..."
                autocomplete="off"
                class="w-full
                       bg-gray-900
                       border border-white/10
                       rounded-xl
                       pl-10 pr-4
                       py-3
                       text-white
                       text-sm
                       outline-none
                       focus:ring-2
                       focus:ring-indigo-500"
            >

        </div>


        {{-- KAMERA --}}
        <button
            type="button"
            onclick="openQrScanner()"
            class="w-12
                   shrink-0
                   bg-gray-900
                   border border-white/10
                   rounded-xl
                   text-xl
                   flex items-center justify-center
                   hover:bg-gray-700
                   active:scale-95
                   transition"
            title="Scan QR / Barcode"
        >
            📷
        </button>


        {{-- CARI --}}
        <button
            type="submit"
            class="px-5
                   bg-indigo-600
                   hover:bg-indigo-500
                   active:scale-95
                   text-white
                   text-sm
                   font-semibold
                   rounded-xl
                   transition"
        >
            Cari
        </button>

    </form>

</div>


    {{-- =========================================================
         FILTER KATEGORI
    ========================================================== --}}
    <div class="bg-gray-800/80 border border-white/10 rounded-2xl p-4 shadow-xl backdrop-blur-md">

        <form
            action="{{ route('dashboard') }}"
            method="GET"
            id="dashboardCategoryForm"
        >

            <div class="flex items-center gap-2">

                <span class="text-xs text-gray-400 font-medium whitespace-nowrap">
                    Filter Kategori:
                </span>
                
                                          @if(request('search'))
                              <input
                                  type="hidden"
                                  name="search"
                                  value="{{ request('search') }}"
                              >
                          @endif
                          
                <select
                    name="category"
                    onchange="document.getElementById('dashboardCategoryForm').submit()"
                    class="w-full bg-gray-900 border border-white/10 rounded-xl px-4 py-2.5 text-white text-xs outline-none focus:ring-2 focus:ring-indigo-500 cursor-pointer"
                >

                    <option
                        value="all"
                        {{ (!$category || $category == 'all') ? 'selected' : '' }}
                    >
                        Semua Kategori
                    </option>

                    @isset($categories)

                        @foreach($categories as $cat)

                            <option
                                value="{{ $cat }}"
                                {{ ($category == $cat) ? 'selected' : '' }}
                            >
                                {{ $cat }}
                            </option>

                        @endforeach

                    @endisset

                </select>

            </div>

        </form>

    </div>


    {{-- =========================================================
         JUDUL PRODUK
    ========================================================== --}}
    <div class="flex items-center justify-between">

        <div>

            <h2 class="text-lg font-semibold text-white">
                Produk Tersedia
            </h2>

            <p class="text-sm text-gray-400 mt-1">
                {{ $products->count() }} produk ditampilkan
            </p>

        </div>

    </div>


    {{-- =========================================================
         DAFTAR PRODUK
    ========================================================== --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">

        @forelse($products as $p)

            <div
                class="product-card bg-gray-800/80
                       border border-white/10
                       rounded-2xl
                       p-4
                       shadow-lg
                       flex flex-col
                       justify-between
                       hover:border-indigo-500/50
                       hover:bg-gray-800
                       transition
                       cursor-pointer"
                onclick="openProductModal(
                    {{ $p->id }},
                    @js($p->name),
                    @js($p->sku),
                    @js($p->category),
                    @js($p->brand),
                    {{ (float) $p->price }},
                    {{ (int) $p->stock }},
                    @js($p->image)
                )"
            >

                {{-- =================================================
                     INFORMASI PRODUK
                ================================================== --}}
                <div>

                    {{-- Kategori & SKU --}}
                    <div class="flex items-center justify-between gap-2 mb-3">

                        <span
                            class="px-2.5 py-1
                                   rounded-full
                                   text-[10px]
                                   font-semibold
                                   bg-indigo-500/10
                                   text-indigo-400
                                   border border-indigo-500/20
                                   truncate"
                        >
                            {{ $p->category }}
                        </span>

                        <span
                            class="text-[10px]
                                   font-mono
                                   text-gray-500
                                   truncate"
                        >
                            {{ $p->sku }}
                        </span>

                    </div>


                    {{-- GAMBAR PRODUK --}}
                    <div class="w-full h-24 mb-3 bg-gray-800 rounded-xl overflow-hidden flex items-center justify-center border border-white/5">

                        @if($p->image)

                            <img
                                src="{{ asset('products/' . $p->image) }}"
                                alt="{{ $p->name }}"
                                class="w-full h-full object-cover transition duration-300"
                            >

                        @else

                            <span class="text-[10px] text-gray-500">
                                No Image
                            </span>

                        @endif

                    </div>


                    {{-- Nama Produk --}}
                    <h3
                        class="text-white
                               font-semibold
                               text-sm
                               leading-5"
                    >
                        {{ $p->name }}
                    </h3>


                    {{-- Brand --}}
                    @if($p->brand)

                        <p class="text-xs text-gray-400 mt-1">
                            {{ $p->brand }}
                        </p>

                    @endif


                    {{-- Harga --}}
                    <div class="mt-4">

                        <p class="text-[11px] text-gray-500">
                            Harga Jual
                        </p>

                        <p class="text-lg font-bold text-emerald-400">
                            Rp {{ number_format($p->price, 0, ',', '.') }}
                        </p>

                    </div>

                </div>


                {{-- =================================================
                     STOK + TOMBOL
                ================================================== --}}
                <div class="mt-4 pt-3 border-t border-white/10">

                    <div class="flex items-center justify-between mb-3">

                        <span class="text-xs text-gray-400">
                            Stok
                        </span>


                        @if($p->stock <= 0)

                            <span
                                class="px-2.5 py-1
                                       rounded-full
                                       text-[10px]
                                       font-semibold
                                       bg-red-500/10
                                       text-red-400
                                       border border-red-500/20"
                            >
                                Habis
                            </span>

                        @elseif($p->stock <= 5)

                            <span
                                class="px-2.5 py-1
                                       rounded-full
                                       text-[10px]
                                       font-semibold
                                       bg-yellow-500/10
                                       text-yellow-400
                                       border border-yellow-500/20"
                            >
                                {{ $p->stock }} pcs · Menipis
                            </span>

                        @else

                            <span
                                class="px-2.5 py-1
                                       rounded-full
                                       text-[10px]
                                       font-semibold
                                       bg-emerald-500/10
                                       text-emerald-400
                                       border border-emerald-500/20"
                            >
                                {{ $p->stock }} pcs
                            </span>

                        @endif

                    </div>


                    {{-- Tombol --}}
                    @if($p->stock > 0)

                        <button
                            type="button"
                            onclick="event.stopPropagation(); openProductModal(
                                {{ $p->id }},
                                @js($p->name),
                                @js($p->sku),
                                @js($p->category),
                                @js($p->brand),
                                {{ (float) $p->price }},
                                {{ (int) $p->stock }},
                                @js($p->image)
                            )"
                            class="w-full
                                   bg-indigo-600
                                   hover:bg-indigo-500
                                   active:scale-95
                                   text-white
                                   text-xs
                                   font-semibold
                                   rounded-xl
                                   py-2.5
                                   transition"
                        >
                            🛒 Masukkan Keranjang
                        </button>

                    @else

                        <button
                            type="button"
                            disabled
                            class="w-full
                                   bg-gray-700
                                   text-gray-500
                                   text-xs
                                   font-semibold
                                   rounded-xl
                                   py-2.5
                                   cursor-not-allowed"
                        >
                            Stok Habis
                        </button>

                    @endif

                </div>

            </div>

        @empty

            {{-- =====================================================
                 TIDAK ADA PRODUK
            ====================================================== --}}
            <div
                class="col-span-full
                       bg-gray-800/50
                       border border-white/10
                       rounded-2xl
                       py-14
                       px-6
                       text-center"
            >

                <div class="text-4xl mb-3">
                    📦
                </div>

                <h3 class="text-white font-semibold">
                    Belum ada produk
                </h3>

                <p class="text-sm text-gray-500 mt-1">
                    Belum ada produk pada kategori yang dipilih.
                </p>

            </div>

        @endforelse

    </div>

</div>

{{-- =============================================================
     MODAL QR / BARCODE SCANNER
============================================================== --}}
<div
    id="qrScannerModal"
    class="fixed inset-0 z-[110]
           hidden
           items-center justify-center
           bg-black/80
           backdrop-blur-sm
           px-4"
>

    <div
        class="w-full max-w-md
               bg-gray-900
               border border-white/10
               rounded-3xl
               shadow-2xl
               overflow-hidden"
    >

        <div class="flex items-center justify-between p-4 border-b border-white/10">

            <div>
                <h3 class="text-white font-semibold">
                    📷 Scan Produk
                </h3>

                <p class="text-xs text-gray-500 mt-1">
                    Arahkan kamera ke QR / barcode SKU
                </p>
            </div>

            <button
                type="button"
                onclick="closeQrScanner()"
                class="w-9 h-9 rounded-full
                       bg-gray-800
                       text-white
                       hover:bg-gray-700"
            >
                ✕
            </button>

        </div>


        <div class="p-4">

            <div
                id="qr-reader"
                class="w-full overflow-hidden rounded-2xl bg-black"
            ></div>

            <p
                id="qrScannerStatus"
                class="text-xs text-gray-400 text-center mt-3"
            >
                Menyiapkan kamera...
            </p>

        </div>

    </div>

</div>

{{-- =============================================================
     MODAL DETAIL PRODUK
============================================================== --}}
<div
    id="productModal"
    class="fixed inset-0 z-[100]
           hidden
           items-end sm:items-center
           justify-center
           bg-black/70
           backdrop-blur-sm"
>

    <div
        id="productModalContent"
        class="w-full sm:max-w-md
               bg-gray-900
               border border-white/10
               rounded-t-3xl sm:rounded-3xl
               shadow-2xl
               overflow-hidden
               max-h-[90vh]
               overflow-y-auto"
    >

        {{-- HEADER MODAL --}}
        <div class="relative">

            <button
                type="button"
                onclick="closeProductModal()"
                class="absolute top-3 right-3 z-10
                       w-9 h-9
                       rounded-full
                       bg-black/60
                       text-white
                       flex items-center justify-center
                       hover:bg-black/80"
            >
                ✕
            </button>


            {{-- GAMBAR --}}
            <div
                id="modalProductImageBox"
                class="w-full h-56 bg-gray-800 flex items-center justify-center"
            >

                <img
                    id="modalProductImage"
                    src=""
                    alt=""
                    class="w-full h-full object-contain hidden"
                >

                <span
                    id="modalNoImage"
                    class="text-gray-500 text-sm"
                >
                    No Image
                </span>

            </div>

        </div>


        {{-- DETAIL --}}
        <div class="p-5">

            <div class="flex items-start justify-between gap-3">

                <div>

                    <p
                        id="modalProductCategory"
                        class="text-xs text-indigo-400 mb-1"
                    ></p>

                    <h3
                        id="modalProductName"
                        class="text-xl font-bold text-white"
                    ></h3>

                    <p
                        id="modalProductBrand"
                        class="text-sm text-gray-400 mt-1"
                    ></p>

                </div>

                <span
                    id="modalProductSku"
                    class="text-[10px] font-mono text-gray-500"
                ></span>

            </div>


            {{-- HARGA --}}
            <div class="mt-5">

                <p class="text-xs text-gray-500">
                    Harga
                </p>

                <p
                    id="modalProductPrice"
                    class="text-2xl font-bold text-emerald-400"
                ></p>

            </div>


            {{-- STOK --}}
            <div
                class="mt-3
                       flex items-center justify-between
                       bg-gray-800
                       rounded-xl
                       px-4
                       py-3"
            >

                <span class="text-sm text-gray-400">
                    Stok tersedia
                </span>

                <span
                    id="modalProductStock"
                    class="text-sm font-semibold text-white"
                ></span>

            </div>


            {{-- JUMLAH --}}
            <div class="mt-6">

                <p class="text-sm text-gray-300 mb-2">
                    Jumlah
                </p>

                <div class="flex items-center">

                    <button
                        type="button"
                        onclick="changeModalQty(-1)"
                        class="w-12 h-12
                               bg-gray-800
                               border border-white/10
                               rounded-l-xl
                               text-xl text-white
                               hover:bg-gray-700"
                    >
                        −
                    </button>

                    <div
                        id="modalQty"
                        class="h-12 flex-1
                               bg-gray-900
                               border-y border-white/10
                               flex items-center justify-center
                               text-white font-semibold"
                    >
                        1
                    </div>

                    <button
                        type="button"
                        onclick="changeModalQty(1)"
                        class="w-12 h-12
                               bg-gray-800
                               border border-white/10
                               rounded-r-xl
                               text-xl text-white
                               hover:bg-gray-700"
                    >
                        +
                    </button>

                </div>

            </div>


            {{-- TOTAL --}}
            <div
                class="mt-5
                       flex items-center justify-between
                       bg-gray-800/80
                       rounded-xl
                       px-4
                       py-3"
            >

                <span class="text-sm text-gray-400">
                    Total
                </span>

                <span
                    id="modalTotal"
                    class="text-lg font-bold text-white"
                >
                    Rp 0
                </span>

            </div>


            {{-- TOMBOL --}}
            <button
                id="modalAddButton"
                type="button"
                onclick="addModalToCart()"
                class="w-full
                       mt-5
                       bg-indigo-600
                       hover:bg-indigo-500
                       active:scale-[0.98]
                       text-white
                       font-semibold
                       rounded-xl
                       py-3.5
                       transition"
            >
                🛒 Masukkan Keranjang
            </button>

        </div>

    </div>

</div>


{{-- =============================================================
     FLOATING CART
============================================================== --}}
<div
    id="floatingCart"
    class="fixed
           bottom-4
           left-3
           right-3
           sm:left-auto
           sm:right-6
           sm:w-[380px]
           z-[90]
           hidden"
>

    <button
        type="button"
        onclick="goToKasir()"
        class="w-full
               bg-gray-950
               border border-white/10
               shadow-2xl
               rounded-2xl
               px-4
               py-3.5
               flex items-center justify-between
               text-white
               hover:bg-gray-900
               active:scale-[0.98]
               transition"
    >

        <div class="flex items-center gap-3">

            <div
                class="w-10 h-10
                       rounded-xl
                       bg-indigo-600
                       flex items-center justify-center"
            >
                🛒
            </div>

            <div class="text-left">

                <p
                    id="floatingCartTotal"
                    class="font-bold text-sm"
                >
                    Rp 0
                </p>

                <p
                    id="floatingCartItems"
                    class="text-[11px] text-gray-400"
                >
                    0 item
                </p>

            </div>

        </div>


        <div class="text-sm font-semibold text-indigo-400">
            Lihat Keranjang →
        </div>

    </button>

</div>


{{-- =============================================================
     JAVASCRIPT
============================================================== --}}
<script src="https://unpkg.com/html5-qrcode"></script>

<script>

const CART_STORAGE_KEY = 'smart_pos_cart';

let dashboardCart = [];

let selectedProduct = {
    id: null,
    name: '',
    sku: '',
    category: '',
    brand: '',
    price: 0,
    stock: 0,
    image: null
};

let modalQty = 1;


/* =============================================================
   FORMAT RUPIAH
============================================================= */

function formatRupiah(value) {

    return 'Rp ' + Number(value || 0).toLocaleString('id-ID');

}


/* =============================================================
   LOAD CART DARI LOCAL STORAGE
============================================================= */

function loadDashboardCart() {

    try {

        const savedCart =
            localStorage.getItem(CART_STORAGE_KEY);

        if (!savedCart) {

            dashboardCart = [];

            return;
        }

        const parsed =
            JSON.parse(savedCart);

        if (!Array.isArray(parsed)) {

            dashboardCart = [];

            return;
        }

        dashboardCart = parsed
            .map(item => ({

                id: item.id,

                name: item.name,

                price: Number(item.price) || 0,

                qty: Number(item.qty) || 0,

                stock: Number(item.stock) || 0

            }))
            .filter(item =>
                item.qty > 0 &&
                item.stock > 0
            );

    } catch (error) {

        console.error(
            'Gagal memuat keranjang:',
            error
        );

        dashboardCart = [];

    }

}


/* =============================================================
   SAVE CART
============================================================= */

function saveDashboardCart() {

    try {

        localStorage.setItem(
            CART_STORAGE_KEY,
            JSON.stringify(dashboardCart)
        );

    } catch (error) {

        console.error(
            'Gagal menyimpan keranjang:',
            error
        );

    }

}


/* =============================================================
   UPDATE FLOATING CART
============================================================= */

function updateFloatingCart() {

    const floatingCart =
        document.getElementById('floatingCart');

    const totalElement =
        document.getElementById('floatingCartTotal');

    const itemsElement =
        document.getElementById('floatingCartItems');


    let total = 0;
    let itemCount = 0;


    dashboardCart.forEach(item => {

        total +=
            Number(item.price) *
            Number(item.qty);

        itemCount +=
            Number(item.qty);

    });


    if (itemCount <= 0) {

        floatingCart.classList.add('hidden');

        return;
    }


    floatingCart.classList.remove('hidden');

    totalElement.textContent =
        formatRupiah(total);

    itemsElement.textContent =
        itemCount + (itemCount === 1 ? ' item' : ' item');

}

/* =============================================================
   QR / BARCODE SCANNER
============================================================= */

let qrScanner = null;
let scannerRunning = false;

const scanUrlTemplate = @json(
    route('produk.scan', ['sku' => '__SKU__'])
);


/* =============================================================
   BUKA SCANNER
============================================================= */

function openQrScanner() {
    const modal = document.getElementById('qrScannerModal');
    const status = document.getElementById('qrScannerStatus');

    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.classList.add('overflow-hidden');

    status.textContent = 'Memeriksa scanner...';

    // Cek library
    if (typeof Html5Qrcode === 'undefined') {
        status.textContent =
            'Library scanner belum berhasil dimuat.';
        console.error('Html5Qrcode tidak ditemukan.');
        return;
    }

    status.textContent = 'Menyiapkan kamera...';

    if (scannerRunning) {
        return;
    }

    try {
        qrScanner = new Html5Qrcode('qr-reader');

        qrScanner.start(
            {
                facingMode: 'environment'
            },
            {
                fps: 10,
                qrbox: {
                    width: 250,
                    height: 150
                }
            },
            function(decodedText) {
                handleScannedSku(decodedText);
            },
            function(errorMessage) {
                // Abaikan error scan sementara
            }
        )
        .then(function() {

            scannerRunning = true;

            status.textContent =
                'Arahkan kamera ke QR / barcode produk.';

        })
        .catch(function(error) {

            console.error(
                'Gagal membuka kamera:',
                error
            );

            status.textContent =
                'Kamera gagal dibuka. Izinkan akses kamera di Chrome.';
        });

    } catch (error) {

        console.error(
            'Scanner error:',
            error
        );

        status.textContent =
            'Scanner gagal dijalankan.';
    }
}


/* =============================================================
   TUTUP SCANNER
============================================================= */

function closeQrScanner() {

    const modal =
        document.getElementById('qrScannerModal');

    if (
        qrScanner &&
        scannerRunning
    ) {

        qrScanner.stop()
            .then(function() {

                qrScanner.clear();

                scannerRunning = false;

                qrScanner = null;

            })
            .catch(function(error) {

                console.error(
                    'Gagal menghentikan scanner:',
                    error
                );

                scannerRunning = false;

                qrScanner = null;

            });

    }

    modal.classList.add('hidden');
    modal.classList.remove('flex');

    document.body.classList.remove('overflow-hidden');

}


/* =============================================================
   HASIL SCAN SKU
============================================================= */

function handleScannedSku(sku) {

    sku =
        String(sku).trim();

    if (!sku) {
        return;
    }


    closeQrScanner();


    fetch(
    scanUrlTemplate.replace(
        '__SKU__',
        encodeURIComponent(sku)
    )
)
    .then(response => {

        return response.json()
            .then(data => ({
                ok: response.ok,
                data: data
            }));

    })
    .then(result => {

        if (!result.ok) {

            alert(
                result.data.message ||
                'Produk tidak ditemukan.'
            );

            return;
        }


        const product =
            result.data.product;


        openProductModal(

            product.id,

            product.name,

            product.sku,

            product.category || '',

            product.brand || '',

            product.price,

            product.stock,

            product.image || null

        );

    })
    .catch(error => {

        console.error(
            'Gagal mencari produk:',
            error
        );

        alert(
            'Terjadi kesalahan saat mencari produk.'
        );

    });

}



/* =============================================================
   BUKA DETAIL PRODUK
============================================================= */

function openProductModal(
    id,
    name,
    sku,
    category,
    brand,
    price,
    stock,
    image
) {

    selectedProduct = {

        id: id,

        name: name,

        sku: sku,

        category: category,

        brand: brand,

        price: Number(price) || 0,

        stock: Number(stock) || 0,

        image: image

    };


    modalQty = 1;


    document.getElementById(
        'modalProductName'
    ).textContent = name;


    document.getElementById(
        'modalProductSku'
    ).textContent = sku;


    document.getElementById(
        'modalProductCategory'
    ).textContent = category;


    const brandElement =
        document.getElementById(
            'modalProductBrand'
        );


    if (brand) {

        brandElement.textContent =
            brand;

        brandElement.classList.remove(
            'hidden'
        );

    } else {

        brandElement.textContent = '';

        brandElement.classList.add(
            'hidden'
        );

    }


    document.getElementById(
        'modalProductPrice'
    ).textContent =
        formatRupiah(price);


    document.getElementById(
        'modalProductStock'
    ).textContent =
        stock + ' pcs';


    const imageElement =
        document.getElementById(
            'modalProductImage'
        );

    const noImageElement =
        document.getElementById(
            'modalNoImage'
        );


    if (image) {

        imageElement.src =
            "{{ asset('products') }}/" + image;

        imageElement.alt =
            name;

        imageElement.classList.remove(
            'hidden'
        );

        noImageElement.classList.add(
            'hidden'
        );

    } else {

        imageElement.src = '';

        imageElement.classList.add(
            'hidden'
        );

        noImageElement.classList.remove(
            'hidden'
        );

    }


    updateModalQty();


    const modal =
        document.getElementById(
            'productModal'
        );

    modal.classList.remove(
        'hidden'
    );

    modal.classList.add(
        'flex'
    );

    document.body.classList.add(
        'overflow-hidden'
    );

}


/* =============================================================
   TUTUP MODAL
============================================================= */

function closeProductModal() {

    const modal =
        document.getElementById(
            'productModal'
        );

    modal.classList.add(
        'hidden'
    );

    modal.classList.remove(
        'flex'
    );

    document.body.classList.remove(
        'overflow-hidden'
    );

}


/* =============================================================
   JUMLAH DI MODAL
============================================================= */

function changeModalQty(change) {

    let newQty =
        modalQty + change;


    if (newQty < 1) {

        newQty = 1;

    }


    if (
        selectedProduct.stock > 0 &&
        newQty > selectedProduct.stock
    ) {

        newQty =
            selectedProduct.stock;

    }


    modalQty =
        newQty;


    updateModalQty();

}


/* =============================================================
   UPDATE TAMPILAN JUMLAH
============================================================= */

function updateModalQty() {

    document.getElementById(
        'modalQty'
    ).textContent =
        modalQty;


    document.getElementById(
        'modalTotal'
    ).textContent =
        formatRupiah(
            selectedProduct.price *
            modalQty
        );

}


/* =============================================================
   MASUKKAN PRODUK KE CART
============================================================= */

function addModalToCart() {

    if (
        !selectedProduct.id ||
        selectedProduct.stock <= 0
    ) {

        return;

    }


    const existingIndex =
        dashboardCart.findIndex(
            item =>
                String(item.id) ===
                String(selectedProduct.id)
        );


    if (existingIndex !== -1) {

        const newQty =
            dashboardCart[existingIndex].qty +
            modalQty;


        if (
            newQty >
            selectedProduct.stock
        ) {

            alert(
                'Jumlah melebihi stok tersedia.'
            );

            return;

        }


        dashboardCart[existingIndex].qty =
            newQty;

    } else {

        dashboardCart.push({

            id: selectedProduct.id,

            name: selectedProduct.name,

            price: selectedProduct.price,

            qty: modalQty,

            stock: selectedProduct.stock

        });

    }


    saveDashboardCart();

    updateFloatingCart();

    closeProductModal();

}


/* =============================================================
   KE HALAMAN KASIR
============================================================= */

function goToKasir() {

    window.location.href =
        "{{ route('kasir.index') }}";

}


/* =============================================================
   KLIK AREA LUAR MODAL
============================================================= */

document.getElementById(
    'productModal'
).addEventListener(
    'click',
    function(event) {

        if (
            event.target === this
        ) {

            closeProductModal();

        }

    }
);


/* =============================================================
   ESC UNTUK TUTUP MODAL
============================================================= */

document.addEventListener(
    'keydown',
    function(event) {

        if (
            event.key === 'Escape'
        ) {

            closeProductModal();

        }

    }
);


/* =============================================================
   INIT
============================================================= */

document.addEventListener(
    'DOMContentLoaded',
    function() {

        loadDashboardCart();

        updateFloatingCart();

    }
);

</script>


{{-- =============================================================
     STYLE
============================================================== --}}
{{-- =============================================================
     ANIMASI INFORMASI DASHBOARD
============================================================== --}}
<style>

    .dashboard-marquee {
        width: max-content;
        animation: dashboardMarquee 22s linear infinite;
    }

    @keyframes dashboardMarquee {

        from {
            transform: translateX(100%);
        }

        to {
            transform: translateX(-100%);
        }

    }

    @media (prefers-reduced-motion: reduce) {

        .dashboard-marquee {
            animation: none;
        }

    }

</style>

@endsection