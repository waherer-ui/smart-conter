@extends('layouts.app')

@section('title', 'Tambah Pembelian')
@section('header', '🛒')

@section('content')

<div class="max-w-3xl mx-auto space-y-4">

    {{-- HEADER --}}
    <div>
        <h1 class="text-lg font-semibold text-white">
            Tambah Pembelian
        </h1>

        <p class="text-xs text-gray-500 mt-1">
            Catat pembelian dan stok masuk dari supplier.
        </p>
    </div>

    {{-- FORM --}}
    <form
        action="{{ route('purchase.store') }}"
        method="POST"
        class="space-y-4"
    >

        @csrf

        {{-- INFORMASI PEMBELIAN --}}
        <div
            class="bg-gray-800/80
                   border border-white/10
                   rounded-2xl
                   shadow-xl
                   p-5
                   space-y-4"
        >

            <h2 class="text-sm font-semibold text-white">
                Informasi Pembelian
            </h2>

            {{-- SUPPLIER --}}
            <div>
                <label class="block text-xs font-medium text-gray-400 mb-2">
                    Supplier
                </label>

                <select
                    name="supplier_id"
                    required
                    class="w-full
                           bg-gray-900
                           border border-white/10
                           rounded-xl
                           px-4 py-3
                           text-sm text-white
                           focus:outline-none
                           focus:border-emerald-500"
                >

                    <option value="">
                        Pilih Supplier
                    </option>

                    @foreach($suppliers as $supplier)
                        <option
                            value="{{ $supplier->id }}"
                            {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}
                        >
                            {{ $supplier->name }}
                        </option>
                    @endforeach

                </select>
            </div>

            {{-- INVOICE --}}
            <div>
                <label class="block text-xs font-medium text-gray-400 mb-2">
                    Nomor Invoice
                </label>

                <input
                    type="text"
                    name="invoice_number"
                    value="{{ old('invoice_number') }}"
                    class="w-full
                           bg-gray-900
                           border border-white/10
                           rounded-xl
                           px-4 py-3
                           text-sm text-white
                           placeholder-gray-600
                           focus:outline-none
                           focus:border-emerald-500"
                    placeholder="Contoh: INV-001"
                >
            </div>

            {{-- TANGGAL --}}
            <div>
                <label class="block text-xs font-medium text-gray-400 mb-2">
                    Tanggal Pembelian
                </label>

                <input
                    type="date"
                    name="purchase_date"
                    value="{{ old('purchase_date', now()->format('Y-m-d')) }}"
                    required
                    class="w-full
                           bg-gray-900
                           border border-white/10
                           rounded-xl
                           px-4 py-3
                           text-sm text-white
                           focus:outline-none
                           focus:border-emerald-500"
                >
            </div>

        </div>

{{-- =====================================================
PRODUK PEMBELIAN
====================================================== --}}

<div class="bg-gray-800/80 border border-white/10
            rounded-2xl p-4 shadow-xl"><div class="flex items-center justify-between mb-4">
    <div>
        <h3 class="text-sm font-semibold text-white">
            📦 Produk
        </h3>
        <p class="text-[11px] text-gray-400">
            Pilih produk yang sudah ada atau buat produk baru.
        </p>
    </div>
</div>

{{-- PILIH MODE --}}
<div class="grid grid-cols-2 gap-2 mb-4">

    <button
        type="button"
        id="existingProductMode"
        onclick="setProductMode('existing')"
        class="rounded-xl px-3 py-2.5
               text-xs font-medium
               border border-indigo-500/40
               bg-indigo-600/20
               text-indigo-300
               transition"
    >
        📦 Produk Lama
        <div class="text-[10px] text-gray-400 mt-0.5">
            Produk sudah terdaftar
        </div>
    </button>

    <button
        type="button"
        id="newProductMode"
        onclick="setProductMode('new')"
        class="rounded-xl px-3 py-2.5
               text-xs font-medium
               border border-white/10
               bg-gray-700/40
               text-gray-400
               transition"
    >
        🆕 Produk Baru
        <div class="text-[10px] text-gray-500 mt-0.5">
            Belum ada di produk
        </div>
    </button>

</div>


{{-- =================================================
     PRODUK LAMA
================================================== --}}

<div id="existingProductForm">

    <label class="block text-xs text-gray-400 mb-1">
        Pilih Produk
    </label>

    <select
        id="productSelect"
        class="w-full bg-gray-900 border border-white/10
               rounded-xl px-3 py-2.5
               text-sm text-white
               focus:outline-none focus:border-indigo-500"
    >
        <option value="">Pilih Produk</option>

        @foreach($products as $product)
            <option
                value="{{ $product->id }}"
                data-name="{{ $product->name }}"
                data-sku="{{ $product->sku }}"
                data-capital="{{ $product->capital_price }}"
            >
                {{ $product->name }} — {{ $product->sku }}
            </option>
        @endforeach
    </select>

    <div class="grid grid-cols-2 gap-2 mt-3">

        <div>
            <label class="block text-[11px] text-gray-400 mb-1">
                Harga Modal
            </label>

            <input
                type="number"
                id="capitalPriceInput"
                min="0"
                step="0.01"
                placeholder="Harga modal"
                class="w-full bg-gray-900 border border-white/10
                       rounded-xl px-3 py-2.5
                       text-sm text-white"
            >
        </div>

        <div>
            <label class="block text-[11px] text-gray-400 mb-1">
                Jumlah
            </label>

            <input
                type="number"
                id="quantityInput"
                min="1"
                value="1"
                placeholder="Jumlah"
                class="w-full bg-gray-900 border border-white/10
                       rounded-xl px-3 py-2.5
                       text-sm text-white"
            >
        </div>

    </div>

</div>


{{-- =================================================
     PRODUK BARU
================================================== --}}

<div id="newProductForm" class="hidden">

    <div class="space-y-3">

        {{-- NAMA --}}
        <div>
            <label class="block text-[11px] text-gray-400 mb-1">
                Nama Produk
            </label>

            <input
                type="text"
                id="newProductName"
                placeholder="Contoh: Charger Type C 20W"
                class="w-full bg-gray-900 border border-white/10
                       rounded-xl px-3 py-2.5
                       text-sm text-white"
            >
        </div>

        {{-- KATEGORI + BRAND --}}
        <div class="grid grid-cols-2 gap-2">

            <div>
                <label class="block text-[11px] text-gray-400 mb-1">
                    Kategori
                </label>

                <input
                    type="text"
                    id="newProductCategory"
                    placeholder="Charger"
                    class="w-full bg-gray-900 border border-white/10
                           rounded-xl px-3 py-2.5
                           text-sm text-white"
                >
            </div>

            <div>
                <label class="block text-[11px] text-gray-400 mb-1">
                    Brand
                </label>

                <input
                    type="text"
                    id="newProductBrand"
                    placeholder="Optional"
                    class="w-full bg-gray-900 border border-white/10
                           rounded-xl px-3 py-2.5
                           text-sm text-white"
                >
            </div>

        </div>

        {{-- MODAL + JUAL --}}
        <div class="grid grid-cols-2 gap-2">

            <div>
                <label class="block text-[11px] text-gray-400 mb-1">
                    Harga Modal
                </label>

                <input
                    type="number"
                    id="newProductCapital"
                    min="0"
                    step="0.01"
                    placeholder="Harga modal"
                    class="w-full bg-gray-900 border border-white/10
                           rounded-xl px-3 py-2.5
                           text-sm text-white"
                >
            </div>

            <div>
                <label class="block text-[11px] text-gray-400 mb-1">
                    Harga Jual
                </label>

                <input
                    type="number"
                    id="newProductPrice"
                    min="0"
                    step="0.01"
                    placeholder="Harga jual"
                    class="w-full bg-gray-900 border border-white/10
                           rounded-xl px-3 py-2.5
                           text-sm text-white"
                >
            </div>

        </div>

        {{-- JUMLAH --}}
        <div>
            <label class="block text-[11px] text-gray-400 mb-1">
                Jumlah Pembelian
            </label>

            <input
                type="number"
                id="newProductQuantity"
                min="1"
                value="1"
                placeholder="Jumlah"
                class="w-full bg-gray-900 border border-white/10
                       rounded-xl px-3 py-2.5
                       text-sm text-white"
            >
        </div>

        {{-- GAMBAR --}}
        <div>
            <label class="block text-[11px] text-gray-400 mb-1">
                Foto Produk <span class="text-gray-500">(opsional)</span>
            </label>

            <input
                type="file"
                id="newProductImage"
                accept="image/jpeg,image/png,image/jpg,image/webp"
                class="w-full text-xs text-gray-400
                       bg-gray-900 border border-white/10
                       rounded-xl p-2"
            >
        </div>

    </div>

</div>


{{-- TOMBOL TAMBAH --}}
<button
    type="button"
    onclick="addPurchaseItem()"
    class="w-full mt-4
           bg-indigo-600 hover:bg-indigo-500
           text-white rounded-xl
           px-4 py-2.5
           text-sm font-medium
           transition"
>
    ＋ Tambahkan Produk
</button>

</div>{{-- =====================================================
DAFTAR PRODUK PEMBELIAN
====================================================== --}}

 {{-- DAFTAR ITEM --}}
    <div
        id="purchaseItems"
        class="space-y-2"
    ></div>


    {{-- TOTAL --}}
    <div
        class="flex items-center
               justify-between
               gap-3
               pt-3
               border-t border-white/10"
    >

        <span class="text-xs text-gray-500">
            Total Pembelian
        </span>

        <span
            id="purchaseTotal"
            class="text-base
                   font-bold
                   text-emerald-400"
        >
            Rp 0
        </span>

    </div>

</div>

        {{-- CATATAN --}}
        <div
            class="bg-gray-800/80
                   border border-white/10
                   rounded-2xl
                   shadow-xl
                   p-5"
        >

            <label class="block text-xs font-medium text-gray-400 mb-2">
                Catatan
            </label>

            <textarea
                name="notes"
                rows="3"
                class="w-full
                       bg-gray-900
                       border border-white/10
                       rounded-xl
                       px-4 py-3
                       text-sm text-white
                       placeholder-gray-600
                       focus:outline-none
                       focus:border-emerald-500"
                placeholder="Catatan pembelian (opsional)"
            >{{ old('notes') }}</textarea>

        </div>

        {{-- ACTION --}}
        <div class="flex items-center justify-between gap-3">

            <a
                href="{{ route('purchase.index') }}"
                class="px-4 py-2.5
                       rounded-xl
                       text-xs
                       font-semibold
                       text-gray-400
                       hover:text-white
                       hover:bg-white/5
                       transition"
            >
                ← Batal
            </a>

            <button
                type="submit"
                class="px-4 py-2.5
                       rounded-xl
                       text-xs
                       font-semibold
                       bg-emerald-500
                       hover:bg-emerald-400
                       text-gray-950
                       transition"
            >
                💾 Simpan Pembelian
            </button>

        </div>

    </form>

</div>


<script>

let purchaseItems = [];

let productMode = 'existing';

function setProductMode(mode) {

productMode = mode;

const existingForm = document.getElementById('existingProductForm');
const newForm = document.getElementById('newProductForm');

const existingButton = document.getElementById('existingProductMode');
const newButton = document.getElementById('newProductMode');

if (mode === 'existing') {

    existingForm.classList.remove('hidden');
    newForm.classList.add('hidden');

    existingButton.className =
        'rounded-xl px-3 py-2.5 text-xs font-medium ' +
        'border border-indigo-500/40 bg-indigo-600/20 ' +
        'text-indigo-300 transition';

    newButton.className =
        'rounded-xl px-3 py-2.5 text-xs font-medium ' +
        'border border-white/10 bg-gray-700/40 ' +
        'text-gray-400 transition';

} else {

    existingForm.classList.add('hidden');
    newForm.classList.remove('hidden');

    newButton.className =
        'rounded-xl px-3 py-2.5 text-xs font-medium ' +
        'border border-emerald-500/40 bg-emerald-600/20 ' +
        'text-emerald-300 transition';

    existingButton.className =
        'rounded-xl px-3 py-2.5 text-xs font-medium ' +
        'border border-white/10 bg-gray-700/40 ' +
        'text-gray-400 transition';
}

}

function addPurchaseItem() {

// =================================================
// PRODUK LAMA
// =================================================
if (productMode === 'existing') {

    const productSelect =
        document.getElementById('productSelect');

    const capitalPriceInput =
        document.getElementById('capitalPriceInput');

    const quantityInput =
        document.getElementById('quantityInput');

    const productId =
        productSelect.value;

    const selectedOption =
        productSelect.options[
            productSelect.selectedIndex
        ];

    const capitalPrice =
        parseFloat(capitalPriceInput.value);

    const quantity =
        parseInt(quantityInput.value);


    if (!productId) {
        alert('Silakan pilih produk.');
        return;
    }

    if (
        isNaN(capitalPrice) ||
        capitalPrice < 0
    ) {
        alert('Masukkan harga modal.');
        return;
    }

    if (
        isNaN(quantity) ||
        quantity < 1
    ) {
        alert('Jumlah produk minimal 1.');
        return;
    }


    const existingIndex =
        purchaseItems.findIndex(
            item =>
                item.type === 'existing' &&
                item.product_id == productId
        );


    if (existingIndex !== -1) {

        alert(
            'Produk tersebut sudah ditambahkan.'
        );

        return;
    }


    purchaseItems.push({

        type: 'existing',

        product_id: productId,

        name:
            selectedOption.dataset.name,

        sku:
            selectedOption.dataset.sku,

        capital_price:
            capitalPrice,

        quantity:
            quantity,

        subtotal:
            capitalPrice * quantity

    });


    renderPurchaseItems();


    productSelect.value = '';

    capitalPriceInput.value = '';

    quantityInput.value = 1;

    return;
}


// =================================================
// PRODUK BARU
// =================================================

const name =
    document.getElementById(
        'newProductName'
    ).value.trim();

const category =
    document.getElementById(
        'newProductCategory'
    ).value.trim();

const brand =
    document.getElementById(
        'newProductBrand'
    ).value.trim();

const capitalPrice =
    parseFloat(
        document.getElementById(
            'newProductCapital'
        ).value
    );

const price =
    parseFloat(
        document.getElementById(
            'newProductPrice'
        ).value
    );

const quantity =
    parseInt(
        document.getElementById(
            'newProductQuantity'
        ).value
    );


if (!name) {

    alert('Nama produk wajib diisi.');

    return;
}


if (!category) {

    alert('Kategori produk wajib diisi.');

    return;
}


if (
    isNaN(capitalPrice) ||
    capitalPrice < 0
) {

    alert('Masukkan harga modal.');

    return;
}


if (
    isNaN(price) ||
    price < 0
) {

    alert('Masukkan harga jual.');

    return;
}


if (
    isNaN(quantity) ||
    quantity < 1
) {

    alert('Jumlah pembelian minimal 1.');

    return;
}


// Cegah produk baru yang sama
// ditambahkan dua kali dalam pembelian
const duplicateNewProduct =
    purchaseItems.find(
        item =>
            item.type === 'new' &&
            item.name.toLowerCase() === name.toLowerCase() &&
            item.category.toLowerCase() === category.toLowerCase()
    );


if (duplicateNewProduct) {

    alert(
        'Produk baru dengan nama dan kategori tersebut sudah ditambahkan.'
    );

    return;
}


purchaseItems.push({

    type: 'new',

    product_id: null,

    name: name,

    category: category,

    brand: brand,

    sku: 'Akan dibuat otomatis',

    capital_price: capitalPrice,

    price: price,

    quantity: quantity,

    subtotal: capitalPrice * quantity

});


renderPurchaseItems();


// RESET FORM PRODUK BARU

document.getElementById(
    'newProductName'
).value = '';

document.getElementById(
    'newProductCategory'
).value = '';

document.getElementById(
    'newProductBrand'
).value = '';

document.getElementById(
    'newProductCapital'
).value = '';

document.getElementById(
    'newProductPrice'
).value = '';

document.getElementById(
    'newProductQuantity'
).value = 1;

document.getElementById(
    'newProductImage'
).value = '';

}


function removePurchaseItem(index) {

    purchaseItems.splice(
        index,
        1
    );

    renderPurchaseItems();
}


function renderPurchaseItems() {

const container =
    document.getElementById(
        'purchaseItems'
    );

const totalElement =
    document.getElementById(
        'purchaseTotal'
    );


container.innerHTML = '';


let total = 0;


purchaseItems.forEach(
    (item, index) => {

        total += item.subtotal;


        const row =
            document.createElement('div');


        row.className =
            'bg-gray-900/70 border border-white/5 rounded-xl p-3';


        const typeLabel =
            item.type === 'new'
                ? '🆕 Produk Baru'
                : '📦 Produk Lama';


        const skuText =
            item.type === 'new'
                ? 'SKU akan dibuat otomatis'
                : item.sku;


        row.innerHTML = `

            <div
                class="flex items-start
                       justify-between
                       gap-3"
            >

                <div class="min-w-0">

                    <div
                        class="text-[10px]
                               text-indigo-400
                               mb-1"
                    >
                        ${typeLabel}
                    </div>

                    <p
                        class="text-sm
                               font-semibold
                               text-white
                               truncate"
                    >
                        ${item.name}
                    </p>

                    <p
                        class="text-[10px]
                               text-gray-600
                               mt-0.5"
                    >
                        ${skuText}
                    </p>

                    ${
                        item.type === 'new'
                        ? `
                            <p
                                class="text-[10px]
                                       text-gray-500
                                       mt-1"
                            >
                                ${item.category}
                                ${item.brand
                                    ? ' • ' + item.brand
                                    : ''}
                            </p>
                          `
                        : ''
                    }

                    <p
                        class="text-[11px]
                               text-gray-500
                               mt-2"
                    >
                        ${formatRupiah(item.capital_price)}
                        ×
                        ${item.quantity}
                    </p>

                </div>


                <div
                    class="text-right
                           shrink-0"
                >

                    <p
                        class="text-sm
                               font-semibold
                               text-emerald-400"
                    >
                        ${formatRupiah(item.subtotal)}
                    </p>

                    <button
                        type="button"
                        onclick="removePurchaseItem(${index})"
                        class="text-[10px]
                               text-red-400
                               hover:text-red-300
                               mt-1"
                    >
                        Hapus
                    </button>

                </div>

            </div>


            ${
                item.type === 'existing'
                ? `

                    <input
                        type="hidden"
                        name="item_type[]"
                        value="existing"
                    >

                    <input
                        type="hidden"
                        name="product_id[]"
                        value="${item.product_id}"
                    >

                    <input
                        type="hidden"
                        name="capital_price[]"
                        value="${item.capital_price}"
                    >

                    <input
                        type="hidden"
                        name="quantity[]"
                        value="${item.quantity}"
                    >

                `
                : `

                    <input
                        type="hidden"
                        name="item_type[]"
                        value="new"
                    >

                    <input
                        type="hidden"
                        name="new_name[]"
                        value="${escapeHtmlAttribute(item.name)}"
                    >

                    <input
                        type="hidden"
                        name="new_category[]"
                        value="${escapeHtmlAttribute(item.category)}"
                    >

                    <input
                        type="hidden"
                        name="new_brand[]"
                        value="${escapeHtmlAttribute(item.brand || '')}"
                    >

                    <input
                        type="hidden"
                        name="new_capital_price[]"
                        value="${item.capital_price}"
                    >

                    <input
                        type="hidden"
                        name="new_price[]"
                        value="${item.price}"
                    >

                    <input
                        type="hidden"
                        name="new_quantity[]"
                        value="${item.quantity}"
                    >

                `
            }

        `;


        container.appendChild(row);

    }
);


totalElement.textContent =
    formatRupiah(total);

}

function escapeHtmlAttribute(value) {
    // Jika value kosong, undefined, atau null, langsung kembalikan string kosong
    if (value === undefined || value === null) {
        return '';
    }

    return String(value)
        .replace(/&/g, '&amp;')
        .replace(/"/g, '&quot;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');
}


function formatRupiah(value) {

    return 'Rp ' +
        Number(value).toLocaleString(
            'id-ID'
        );
}


document
    .getElementById('productSelect')
    .addEventListener(
        'change',
        function () {

            const option =
                this.options[
                    this.selectedIndex
                ];


            const capitalPrice =
                option.dataset.capital;


            if (
                capitalPrice !== undefined &&
                capitalPrice !== ''
            ) {

                document
                    .getElementById(
                        'capitalPriceInput'
                    )
                    .value =
                    capitalPrice;

            }

        }
    );

</script>

@endsection