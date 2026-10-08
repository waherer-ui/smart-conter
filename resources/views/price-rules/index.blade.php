@extends('layouts.app')

@section('content')

<div class="max-w-2xl mx-auto px-4 py-4">

    <div class="mb-4">
        <h1 class="text-xl font-bold text-white">
            🏷️ Harga Khusus & Promosi
        </h1>

        <p class="text-sm text-gray-400 mt-1">
            Atur harga reseller/grosir dan promosi tanpa mengubah harga normal produk.
        </p>
    </div>

    {{-- FORM TAMBAH ATURAN --}}
    <div class="rounded-2xl border border-white/10 bg-gray-800 p-4 mb-5">

        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-white">
                ➕ Tambah Aturan
            </h2>
        </div>

        @if(session('success'))
            <div class="mb-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 p-3 text-sm text-emerald-300">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 rounded-xl bg-red-500/10 border border-red-500/30 p-3 text-sm text-red-300">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 rounded-xl bg-red-500/10 border border-red-500/30 p-3 text-sm text-red-300">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('price-rules.store') }}">
            @csrf

            {{-- JENIS ATURAN --}}
            <div class="mb-4">
                <label class="block text-sm text-gray-300 mb-1">
                    Jenis Aturan
                </label>

                <select
                    name="type"
                    id="rule-type"
                    class="w-full rounded-xl bg-gray-900 border border-white/10 text-white px-3 py-3"
                >
                    <option value="reseller">
                        Harga Reseller / Grosir
                    </option>

                    <option value="promotion">
                        Promosi
                    </option>
                </select>
            </div>

            {{-- PRODUK --}}
            <div class="mb-4">
                <label class="block text-sm text-gray-300 mb-1">
                    Berlaku Untuk
                </label>

                <select
                    name="product_id"
                    id="product-id"
                    class="w-full rounded-xl bg-gray-900 border border-white/10 text-white px-3 py-3"
                >
                    <option value="">
                        Semua Produk
                    </option>

                    @foreach($products as $product)
                        <option value="{{ $product->id }}">
                            {{ $product->name }}
                            — Rp{{ number_format($product->price, 0, ',', '.') }}
                        </option>
                    @endforeach
                </select>

                <p class="text-xs text-gray-500 mt-1">
                    Kosongkan jika aturan berlaku untuk semua produk.
                </p>
            </div>

            {{-- ========================= --}}
            {{-- RESELLER / GROSIR --}}
            {{-- ========================= --}}
            <div id="reseller-fields">

                <div class="mb-4">
                    <label class="block text-sm text-gray-300 mb-1">
                        Minimal Quantity
                    </label>

                    <input
                        type="number"
                        name="min_quantity"
                        min="1"
                        placeholder="Contoh: 3"
                        value="{{ old('min_quantity') }}"
                        class="w-full rounded-xl bg-gray-900 border border-white/10 text-white px-3 py-3"
                    >
                </div>

                {{-- KHUSUS SEMUA PRODUK --}}
                <div id="global-reseller-fields">

                    <div class="mb-4">
                        <label class="block text-sm text-gray-300 mb-1">
                            Jenis Potongan
                        </label>

                        <select
                            name="discount_type"
                            id="reseller-discount-type"
                            disabled
                            class="w-full rounded-xl bg-gray-900 border border-white/10 text-white px-3 py-3"
                        >
                            <option value="nominal">
                                Potongan Rp / Produk
                            </option>

                            <option value="percent">
                                Potongan %
                            </option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm text-gray-300 mb-1">
                            Nilai Potongan
                        </label>

                        <input
                            type="number"
                            name="discount_value"
                            id="reseller-discount-value"
                            disabled
                            min="0"
                            step="0.01"
                            placeholder="Contoh: 2000"
                            value="{{ old('discount_value') }}"
                            class="w-full rounded-xl bg-gray-900 border border-white/10 text-white px-3 py-3"
                        >

                        <p
                            id="reseller-discount-help"
                            class="text-xs text-gray-500 mt-1"
                        >
                            Contoh: Rp2.000 dipotong dari harga setiap produk.
                        </p>
                    </div>

                </div>

                {{-- KHUSUS PRODUK --}}
                <div id="product-reseller-fields" class="hidden">

                    <div class="mb-4">
                        <label class="block text-sm text-gray-300 mb-1">
                            Harga Khusus Reseller / Grosir
                        </label>

                        <input
                            type="number"
                            name="special_price"
                            min="0"
                            step="0.01"
                            placeholder="Contoh: 348000"
                            value="{{ old('special_price') }}"
                            class="w-full rounded-xl bg-gray-900 border border-white/10 text-white px-3 py-3"
                        >

                        <p class="text-xs text-gray-500 mt-1">
                            Harga tetap untuk produk yang dipilih.
                        </p>
                    </div>

                </div>

            </div>

            {{-- ========================= --}}
            {{-- PROMOSI --}}
            {{-- ========================= --}}
            <div id="promotion-fields" class="hidden">

                <div class="mb-4">
                    <label class="block text-sm text-gray-300 mb-1">
                        Jenis Diskon
                    </label>

                    <select
                        name="discount_type"
                        id="promotion-discount-type"
                        class="w-full rounded-xl bg-gray-900 border border-white/10 text-white px-3 py-3"
                    >
                        <option value="nominal">
                            Potongan Rp
                        </option>

                        <option value="percent">
                            Potongan %
                        </option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm text-gray-300 mb-1">
                        Nilai Diskon
                    </label>

                    <input
                        type="number"
                        name="discount_value"
                        min="0"
                        step="0.01"
                        placeholder="Contoh: 5000"
                        value="{{ old('discount_value') }}"
                        class="w-full rounded-xl bg-gray-900 border border-white/10 text-white px-3 py-3"
                    >
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">

                    <div>
                        <label class="block text-sm text-gray-300 mb-1">
                            Mulai
                        </label>

                        <input
                            type="datetime-local"
                            name="start_at"
                            value="{{ old('start_at') }}"
                            class="w-full rounded-xl bg-gray-900 border border-white/10 text-white px-3 py-3"
                        >
                    </div>

                    <div>
                        <label class="block text-sm text-gray-300 mb-1">
                            Berakhir
                        </label>

                        <input
                            type="datetime-local"
                            name="end_at"
                            value="{{ old('end_at') }}"
                            class="w-full rounded-xl bg-gray-900 border border-white/10 text-white px-3 py-3"
                        >
                    </div>

                </div>

            </div>

            <button
                type="submit"
                class="w-full mt-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold py-3 transition"
            >
                Simpan Aturan
            </button>

        </form>

    </div>

    {{-- DAFTAR ATURAN --}}
    <div>

        <h2 class="font-semibold text-white mb-3">
            📋 Aturan Aktif & Tersimpan
        </h2>

        @forelse($priceRules as $rule)

            <div class="rounded-2xl border border-white/10 bg-gray-800 p-4 mb-3">

                <div class="flex items-start justify-between gap-3">

                    <div class="min-w-0">

                        <div class="flex items-center gap-2 mb-2">

                            @if($rule->type === 'reseller')
                                <span class="text-sm">
                                    🛒 Reseller / Grosir
                                </span>
                            @else
                                <span class="text-sm">
                                    🎉 Promosi
                                </span>
                            @endif

                        </div>

                        <p class="text-white font-medium">
                            {{ $rule->product?->name ?? 'Semua Produk' }}
                        </p>

                        @if($rule->type === 'reseller')

                            <p class="text-xs text-gray-400 mt-1">
                                Minimal:
                                {{ $rule->min_quantity }} pcs
                            </p>

                            @if($rule->product_id)

                                <p class="text-sm text-emerald-400 mt-1">
                                    Harga:
                                    Rp{{ number_format($rule->special_price, 0, ',', '.') }}
                                </p>

                            @else

                                <p class="text-sm text-emerald-400 mt-1">

                                    @if($rule->discount_type === 'percent')
                                        Potongan:
                                        {{ rtrim(rtrim(number_format($rule->discount_value, 2, ',', '.'), '0'), ',') }}%
                                        / produk
                                    @else
                                        Potongan:
                                        Rp{{ number_format($rule->discount_value, 0, ',', '.') }}
                                        / produk
                                    @endif

                                </p>

                            @endif

                        @else

                            <p class="text-sm text-emerald-400 mt-1">

                                Diskon:

                                @if($rule->discount_type === 'percent')
                                    {{ rtrim(rtrim(number_format($rule->discount_value, 2, ',', '.'), '0'), ',') }}%
                                @else
                                    Rp{{ number_format($rule->discount_value, 0, ',', '.') }}
                                @endif

                            </p>

                            @if($rule->start_at || $rule->end_at)

                                <p class="text-xs text-gray-400 mt-1">

                                    {{ $rule->start_at?->format('d/m/Y H:i') ?? '-' }}

                                    s/d

                                    {{ $rule->end_at?->format('d/m/Y H:i') ?? '-' }}

                                </p>

                            @endif

                        @endif

                    </div>

                    <div class="shrink-0">

                        @if($rule->is_active)
                            <span class="text-xs rounded-full bg-emerald-500/10 text-emerald-400 px-2 py-1">
                                Aktif
                            </span>
                        @else
                            <span class="text-xs rounded-full bg-gray-700 text-gray-400 px-2 py-1">
                                Nonaktif
                            </span>
                        @endif

                    </div>

                </div>

                <div class="flex gap-2 mt-4">

                    <form
                        method="POST"
                        action="{{ route('price-rules.toggle', $rule) }}"
                    >
                        @csrf
                        @method('PATCH')

                        <button
                            type="submit"
                            class="text-xs px-3 py-2 rounded-lg bg-gray-700 hover:bg-gray-600 text-white"
                        >
                            {{ $rule->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                        </button>
                    </form>

                    <form
                        method="POST"
                        action="{{ route('price-rules.destroy', $rule) }}"
                        onsubmit="return confirm('Hapus aturan harga ini?')"
                    >
                        @csrf
                        @method('DELETE')

                        <button
                            type="submit"
                            class="text-xs px-3 py-2 rounded-lg bg-red-500/10 hover:bg-red-500/20 text-red-400"
                        >
                            Hapus
                        </button>
                    </form>

                </div>

            </div>

        @empty

            <div class="rounded-2xl border border-white/10 bg-gray-800 p-5 text-center">
                <p class="text-gray-400 text-sm">
                    Belum ada aturan harga.
                </p>
            </div>

        @endforelse

    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const typeSelect = document.getElementById('rule-type');
    const productSelect = document.getElementById('product-id');

    const resellerFields = document.getElementById('reseller-fields');
    const promotionFields = document.getElementById('promotion-fields');

    const globalResellerFields =
        document.getElementById('global-reseller-fields');

    const productResellerFields =
        document.getElementById('product-reseller-fields');

    const resellerDiscountType =
        document.getElementById('reseller-discount-type');

    const resellerDiscountValue =
        document.getElementById('reseller-discount-value');

    const resellerDiscountHelp =
        document.getElementById('reseller-discount-help');

    const resellerSpecialPrice =
        document.querySelector(
            '#product-reseller-fields input[name="special_price"]'
        );

    const promotionDiscountType =
        document.getElementById('promotion-discount-type');

    const promotionDiscountValue =
        document.querySelector(
            '#promotion-fields input[name="discount_value"]'
        );

    const promotionStart =
        document.querySelector(
            '#promotion-fields input[name="start_at"]'
        );

    const promotionEnd =
        document.querySelector(
            '#promotion-fields input[name="end_at"]'
        );


    function updateResellerHelp() {

        if (!resellerDiscountType || !resellerDiscountHelp) {
            return;
        }

        if (resellerDiscountType.value === 'percent') {

            resellerDiscountHelp.textContent =
                'Contoh: 1% akan mengurangi harga normal setiap produk sebesar 1%.';

        } else {

            resellerDiscountHelp.textContent =
                'Contoh: Rp2.000 dipotong dari harga setiap produk.';

        }
    }


    function updateFields() {

        const type = typeSelect.value;
        const hasProduct = productSelect.value !== '';


        /*
        |--------------------------------------------------------------------------
        | RESET
        |--------------------------------------------------------------------------
        */

        resellerFields.classList.add('hidden');
        promotionFields.classList.add('hidden');

        globalResellerFields.classList.add('hidden');
        productResellerFields.classList.add('hidden');


        // Semua field dinonaktifkan dulu
        resellerDiscountType.disabled = true;
        resellerDiscountValue.disabled = true;

        if (resellerSpecialPrice) {
            resellerSpecialPrice.disabled = true;
        }

        promotionDiscountType.disabled = true;
        promotionDiscountValue.disabled = true;

        if (promotionStart) {
            promotionStart.disabled = true;
        }

        if (promotionEnd) {
            promotionEnd.disabled = true;
        }


        /*
        |--------------------------------------------------------------------------
        | RESELLER / GROSIR
        |--------------------------------------------------------------------------
        */

        if (type === 'reseller') {

            resellerFields.classList.remove('hidden');

            if (hasProduct) {

                // Produk tertentu
                productResellerFields.classList.remove('hidden');

                if (resellerSpecialPrice) {
                    resellerSpecialPrice.disabled = false;
                }

            } else {

                // Semua produk
                globalResellerFields.classList.remove('hidden');

                resellerDiscountType.disabled = false;
                resellerDiscountValue.disabled = false;

                updateResellerHelp();
            }

        }


        /*
        |--------------------------------------------------------------------------
        | PROMOSI
        |--------------------------------------------------------------------------
        */

        if (type === 'promotion') {

            promotionFields.classList.remove('hidden');

            promotionDiscountType.disabled = false;
            promotionDiscountValue.disabled = false;

            if (promotionStart) {
                promotionStart.disabled = false;
            }

            if (promotionEnd) {
                promotionEnd.disabled = false;
            }
        }
    }


    /*
    |--------------------------------------------------------------------------
    | EVENT
    |--------------------------------------------------------------------------
    */

    typeSelect.addEventListener('change', updateFields);

    productSelect.addEventListener('change', updateFields);

    resellerDiscountType.addEventListener(
        'change',
        updateResellerHelp
    );


    /*
    |--------------------------------------------------------------------------
    | INIT
    |--------------------------------------------------------------------------
    */

    updateFields();

});
</script>

@endsection