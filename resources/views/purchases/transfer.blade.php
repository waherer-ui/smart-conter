@extends('layouts.app')

@section('title', 'Transfer Antar Toko')
@section('header', '🔄')

@section('content')

<div class="max-w-3xl mx-auto space-y-4">

    {{-- HEADER --}}
    <div>
        <h1 class="text-lg font-semibold text-white">
            Transfer Antar Toko
        </h1>

        <p class="text-xs text-gray-500 mt-1">
            Pindahkan produk dan stok dari satu toko ke toko lainnya.
        </p>
    </div>


    {{-- INFORMASI TRANSFER --}}
    <div
        class="bg-gray-800/80
               border border-white/10
               rounded-2xl
               shadow-xl
               p-5
               space-y-4"
    >

        <h2 class="text-sm font-semibold text-white">
            Informasi Transfer
        </h2>


{{-- TOKO ASAL --}}
<div>

    <label class="block text-xs font-medium text-gray-400 mb-2">
        Toko Asal
    </label>

    <div
        class="w-full
               bg-gray-900
               border border-white/10
               rounded-xl
               px-4 py-3"
    >

        <div class="flex items-center justify-between gap-3">

            <div>
                <p class="text-sm font-semibold text-white">
                    {{ $currentStore->name }}
                </p>

                <p class="text-[10px] text-gray-500 mt-0.5">
                    Toko aktif
                </p>
            </div>

            <span class="text-emerald-400 text-lg">
                🏪
            </span>

        </div>

    </div>

</div>


        {{-- TOKO TUJUAN --}}
        <div>

            <label class="block text-xs font-medium text-gray-400 mb-2">
                Toko Tujuan
            </label>

            <select
                id="destinationStore"
                name="destination_store_id"
                required
                class="w-full
                       bg-gray-900
                       border border-white/10
                       rounded-xl
                       px-4 py-3
                       text-sm text-white
                       focus:outline-none
                       focus:border-indigo-500"
            >

                <option value="">
                    Pilih Toko Tujuan
                </option>

                @foreach($stores as $store)

                    <option
                        value="{{ $store->id }}"
                        {{ old('destination_store_id') == $store->id ? 'selected' : '' }}
                    >
                        {{ $store->name }}
                    </option>

                @endforeach

            </select>

        </div>


        {{-- TANGGAL --}}
        <div>

            <label class="block text-xs font-medium text-gray-400 mb-2">
                Tanggal Transfer
            </label>

            <input
                type="date"
                name="transfer_date"
                value="{{ old('transfer_date', now()->format('Y-m-d')) }}"
                required
                class="w-full
                       bg-gray-900
                       border border-white/10
                       rounded-xl
                       px-4 py-3
                       text-sm text-white
                       focus:outline-none
                       focus:border-indigo-500"
            >

        </div>


        {{-- INFO TOKO --}}
        <div
            class="rounded-xl
                   border border-indigo-500/20
                   bg-indigo-500/5
                   px-4 py-3"
        >

            <div class="flex gap-3">

                <div class="text-lg">
                    💡
                </div>

                <div>

                    <p class="text-xs font-medium text-indigo-300">
                        Transfer antar toko
                    </p>

                    <p class="text-[11px] text-gray-500 mt-1">
                        Stok toko asal akan berkurang setelah transfer diproses.
                        Stok toko tujuan bertambah setelah barang diterima.
                    </p>

                </div>

            </div>

        </div>

    </div>


    {{-- =====================================================
    PRODUK TRANSFER
    ====================================================== --}}

    <div
        class="bg-gray-800/80
               border border-white/10
               rounded-2xl
               p-4
               shadow-xl"
    >

        <div class="flex items-center justify-between mb-4">

            <div>

                <h3 class="text-sm font-semibold text-white">
                    📦 Produk
                </h3>

                <p class="text-[11px] text-gray-400">
                    Pilih produk dari toko asal yang akan dipindahkan.
                </p>

            </div>

        </div>


        {{-- PILIH PRODUK --}}
        <div>

            <label class="block text-xs text-gray-400 mb-1">
                Pilih Produk
            </label>

            <select
                id="productSelect"
                class="w-full
                       bg-gray-900
                       border border-white/10
                       rounded-xl
                       px-3 py-2.5
                       text-sm text-white
                       focus:outline-none
                       focus:border-indigo-500"
            >

                <option value="">
                    Pilih Produk
                </option>

                @foreach($products as $product)

                    <option
                        value="{{ $product->id }}"
                        data-name="{{ $product->name }}"
                        data-sku="{{ $product->sku }}"
                        data-stock="{{ $product->stock }}"
                    >
                        {{ $product->name }}
                        @if($product->sku)
                            — {{ $product->sku }}
                        @endif
                    </option>

                @endforeach

            </select>

        </div>


        {{-- INFO PRODUK --}}
        <div
            id="productInfo"
            class="hidden
                   mt-3
                   rounded-xl
                   border border-white/5
                   bg-gray-900/70
                   p-3"
        >

            <div class="flex items-center justify-between gap-3">

                <div>

                    <p
                        id="selectedProductName"
                        class="text-sm font-semibold text-white"
                    >
                        -
                    </p>

                    <p
                        id="selectedProductSku"
                        class="text-[10px] text-gray-500 mt-0.5"
                    >
                        -
                    </p>

                </div>

                <div class="text-right">

                    <p class="text-[10px] text-gray-500">
                        Stok tersedia
                    </p>

                    <p
                        id="selectedProductStock"
                        class="text-sm font-semibold text-emerald-400"
                    >
                        0
                    </p>

                </div>

            </div>

        </div>


        {{-- JUMLAH --}}
        <div class="mt-3">

            <label class="block text-[11px] text-gray-400 mb-1">
                Jumlah Transfer
            </label>

            <input
                type="number"
                id="quantityInput"
                min="1"
                value="1"
                placeholder="Jumlah"
                class="w-full
                       bg-gray-900
                       border border-white/10
                       rounded-xl
                       px-3 py-2.5
                       text-sm text-white
                       focus:outline-none
                       focus:border-indigo-500"
            >

        </div>


        {{-- TOMBOL TAMBAH --}}
        <button
            type="button"
            onclick="addTransferItem()"
            class="w-full
                   mt-4
                   bg-indigo-600
                   hover:bg-indigo-500
                   text-white
                   rounded-xl
                   px-4
                   py-2.5
                   text-sm
                   font-medium
                   transition"
        >
            ＋ Tambahkan Produk
        </button>

    </div>


    {{-- =====================================================
    DAFTAR PRODUK TRANSFER
    ====================================================== --}}

    <div
        id="transferItems"
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
            Total Produk
        </span>

        <span
            id="transferTotal"
            class="text-base
                   font-bold
                   text-emerald-400"
        >
            0 item
        </span>

    </div>


    {{-- =====================================================
    CATATAN
    ====================================================== --}}

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
            id="notes"
            rows="3"
            class="w-full
                   bg-gray-900
                   border border-white/10
                   rounded-xl
                   px-4 py-3
                   text-sm text-white
                   placeholder-gray-600
                   focus:outline-none
                   focus:border-indigo-500"
            placeholder="Contoh: Restok untuk toko cabang..."
        >{{ old('notes') }}</textarea>

    </div>


    {{-- =====================================================
    ACTION
    ====================================================== --}}

    <div class="flex items-center justify-between gap-3">

        <a
            href="{{ route('transfer.index') }}"
            class="px-4
                   py-2.5
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
            type="button"
            onclick="submitTransfer()"
            class="px-4
                   py-2.5
                   rounded-xl
                   text-xs
                   font-semibold
                   bg-emerald-500
                   hover:bg-emerald-400
                   text-gray-950
                   transition"
        >
            🚚 Proses Transfer
        </button>

    </div>

</div>


<script>

let transferItems = [];


/*
|--------------------------------------------------------------------------
| TOKO
|--------------------------------------------------------------------------
*/

const destinationStore =
    document.getElementById('destinationStore');


function validateStores() {

    if (!destinationStore.value) {

        alert('Silakan pilih toko tujuan.');

        destinationStore.focus();

        return false;
    }

    return true;
}


/*
|--------------------------------------------------------------------------
| PILIH PRODUK
|--------------------------------------------------------------------------
*/

document
    .getElementById('productSelect')
    .addEventListener(
        'change',
        function () {

            const option =
                this.options[
                    this.selectedIndex
                ];

            const productInfo =
                document.getElementById(
                    'productInfo'
                );

            if (!this.value) {

                productInfo.classList.add(
                    'hidden'
                );

                return;
            }


            const name =
                option.dataset.name || '-';

            const sku =
                option.dataset.sku || 'Tanpa SKU';

            const stock =
                parseInt(
                    option.dataset.stock || 0
                );


            document
                .getElementById(
                    'selectedProductName'
                )
                .textContent = name;


            document
                .getElementById(
                    'selectedProductSku'
                )
                .textContent =
                    'SKU: ' + sku;


            document
                .getElementById(
                    'selectedProductStock'
                )
                .textContent =
                    stock;


            document
                .getElementById(
                    'quantityInput'
                )
                .max = stock;


            productInfo.classList.remove(
                'hidden'
            );

        }
    );


/*
|--------------------------------------------------------------------------
| TAMBAH PRODUK
|--------------------------------------------------------------------------
*/

function addTransferItem() {

    if (!validateStores()) {
        return;
    }


    const productSelect =
        document.getElementById(
            'productSelect'
        );

    const quantityInput =
        document.getElementById(
            'quantityInput'
        );


    const productId =
        productSelect.value;


    const selectedOption =
        productSelect.options[
            productSelect.selectedIndex
        ];


    const quantity =
        parseInt(
            quantityInput.value
        );


    if (!productId) {

        alert(
            'Silakan pilih produk.'
        );

        return;
    }


    const stock =
        parseInt(
            selectedOption.dataset.stock || 0
        );


    if (
        isNaN(quantity) ||
        quantity < 1
    ) {

        alert(
            'Jumlah transfer minimal 1.'
        );

        return;
    }


    if (quantity > stock) {

        alert(
            'Jumlah transfer melebihi stok yang tersedia.'
        );

        return;
    }


    const existingIndex =
        transferItems.findIndex(
            item =>
                item.product_id == productId
        );


    if (existingIndex !== -1) {

        alert(
            'Produk tersebut sudah ditambahkan.'
        );

        return;
    }


    transferItems.push({

        product_id: productId,

        name:
            selectedOption.dataset.name,

        sku:
            selectedOption.dataset.sku || '',

        stock:
            stock,

        quantity:
            quantity

    });


    renderTransferItems();


    productSelect.value = '';

    quantityInput.value = 1;

    document
        .getElementById(
            'productInfo'
        )
        .classList.add('hidden');

}


/*
|--------------------------------------------------------------------------
| HAPUS PRODUK
|--------------------------------------------------------------------------
*/

function removeTransferItem(index) {

    transferItems.splice(
        index,
        1
    );

    renderTransferItems();

}


/*
|--------------------------------------------------------------------------
| RENDER ITEM
|--------------------------------------------------------------------------
*/

function renderTransferItems() {

    const container =
        document.getElementById(
            'transferItems'
        );


    const totalElement =
        document.getElementById(
            'transferTotal'
        );


    container.innerHTML = '';


    let total = 0;


    transferItems.forEach(
        (item, index) => {

            total += item.quantity;


            const row =
                document.createElement(
                    'div'
                );


            row.className =
                'bg-gray-800/80 ' +
                'border border-white/10 ' +
                'rounded-2xl p-4 ' +
                'shadow-xl';


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
                            📦 Produk Transfer
                        </div>

                        <p
                            class="text-sm
                                   font-semibold
                                   text-white
                                   truncate"
                        >
                            ${escapeHtml(item.name)}
                        </p>

                        <p
                            class="text-[10px]
                                   text-gray-500
                                   mt-0.5"
                        >
                            ${item.sku
                                ? 'SKU: ' +
                                  escapeHtml(item.sku)
                                : 'Tanpa SKU'}
                        </p>

                        <p
                            class="text-[11px]
                                   text-gray-500
                                   mt-2"
                        >
                            Stok tersedia:
                            ${item.stock}
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
                            ${item.quantity} item
                        </p>

                        <button
                            type="button"
                            onclick="removeTransferItem(${index})"
                            class="text-[10px]
                                   text-red-400
                                   hover:text-red-300
                                   mt-1"
                        >
                            Hapus
                        </button>

                    </div>

                </div>


                <input
                    type="hidden"
                    name="items[${index}][product_id]"
                    value="${item.product_id}"
                >
                
                <input
                    type="hidden"
                    name="items[${index}][quantity]"
                    value="${item.quantity}"
                >

            `;


            container.appendChild(row);

        }
    );


    totalElement.textContent =
        total + ' item';

}


/*
|--------------------------------------------------------------------------
| SUBMIT
|--------------------------------------------------------------------------
*/

function submitTransfer() {

    if (!validateStores()) {
        return;
    }


    if (transferItems.length === 0) {

        alert(
            'Tambahkan minimal satu produk.'
        );

        return;
    }


    const transferForm =
        document.createElement(
            'form'
        );


    transferForm.method = 'POST';


    /*
     * Ganti route ini jika route final
     * menggunakan nama berbeda.
     */

    transferForm.action =
        "{{ route('transfer.store') }}";


    const csrf =
        document.createElement(
            'input'
        );

    csrf.type = 'hidden';

    csrf.name = '_token';

    csrf.value =
        "{{ csrf_token() }}";


    transferForm.appendChild(
        csrf
    );


    addHiddenInput(
        transferForm,
        'destination_store_id',
        destinationStore.value
    );


    addHiddenInput(
        transferForm,
        'transfer_date',
        document.querySelector(
            '[name="transfer_date"]'
        ).value
    );


    addHiddenInput(
        transferForm,
        'notes',
        document.getElementById(
            'notes'
        ).value
    );


    transferItems.forEach(
    (item, index) => {

        addHiddenInput(
            transferForm,
            `items[${index}][product_id]`,
            item.product_id
        );

        addHiddenInput(
            transferForm,
            `items[${index}][quantity]`,
            item.quantity
        );

    }
);


    document.body.appendChild(
        transferForm
    );


    transferForm.submit();

}


/*
|--------------------------------------------------------------------------
| HIDDEN INPUT
|--------------------------------------------------------------------------
*/

function addHiddenInput(
    form,
    name,
    value
) {

    const input =
        document.createElement(
            'input'
        );

    input.type = 'hidden';

    input.name = name;

    input.value =
        value ?? '';


    form.appendChild(
        input
    );

}


/*
|--------------------------------------------------------------------------
| ESCAPE HTML
|--------------------------------------------------------------------------
*/

function escapeHtml(value) {

    if (
        value === undefined ||
        value === null
    ) {

        return '';

    }


    return String(value)

        .replace(
            /&/g,
            '&amp;'
        )

        .replace(
            /</g,
            '&lt;'
        )

        .replace(
            />/g,
            '&gt;'
        )

        .replace(
            /"/g,
            '&quot;'
        )

        .replace(
            /'/g,
            '&#039;'
        );

}

</script>

@endsection