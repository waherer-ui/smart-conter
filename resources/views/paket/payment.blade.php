@extends('layouts.app')

@section('title', 'Pembayaran Paket')
@section('header', 'Pembayaran Paket')

@section('content')

<div class="max-w-3xl mx-auto space-y-6">{{-- HEADER --}}
<div>
    <h1 class="text-2xl font-bold text-white">
        Pembayaran Paket
    </h1>

    <p class="text-sm text-gray-400 mt-1">
        Periksa detail paket sebelum melanjutkan pembayaran.
    </p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-5 gap-5">

    {{-- DETAIL PAKET --}}
    <div class="lg:col-span-3
                bg-gray-800/80
                border border-white/10
                rounded-2xl
                p-6
                shadow-xl">

        <div class="flex items-start justify-between gap-4">

            <div>
                <p class="text-xs text-gray-500 uppercase font-semibold">
                    Paket pilihan
                </p>

                <h2 class="text-2xl font-bold text-white mt-1">
                    {{ strtoupper($plan->name) }}
                </h2>
            </div>

            <div class="text-right">

                <p class="text-2xl font-bold text-emerald-400">
                    Rp {{ number_format($plan->price, 0, ',', '.') }}
                </p>

                @if($plan->price > 0)
                    <p class="text-xs text-gray-500">
                        / bulan
                    </p>
                @endif

            </div>

        </div>

        @if($plan->description)
            <div class="mt-5 pt-5 border-t border-white/10">
                <p class="text-sm text-gray-400">
                    {{ $plan->description }}
                </p>
            </div>
        @endif

        {{-- FITUR --}}
        <div class="mt-5 pt-5 border-t border-white/10">

            <p class="text-xs font-semibold
                      text-gray-500 uppercase mb-3">
                Fitur paket
            </p>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">

                @foreach($plan->features as $feature)

                    <div class="flex items-start gap-2">

                        <span class="text-emerald-400">
                            ✓
                        </span>

                        <span class="text-sm text-gray-300">
                            {{ $feature->name }}
                        </span>

                    </div>

                @endforeach

            </div>

        </div>

    </div>

    {{-- PEMBAYARAN --}}
    <div class="lg:col-span-2
                bg-gray-800/80
                border border-white/10
                rounded-2xl
                p-6
                shadow-xl">

        <h2 class="text-lg font-bold text-white">
            Pembayaran
        </h2>

        <p class="text-sm text-gray-500 mt-1">
            Pilih durasi dan metode pembayaran.
        </p>

        @if($plan->price > 0)

            <form
                action="{{ route('paket.payment.create', $plan) }}"
                method="POST"
                class="mt-5"
            >

                @csrf

                {{-- DURASI PAKET --}}
                <div>

                    <p class="text-xs font-semibold
                              text-gray-500 uppercase mb-3">
                        Pilih Durasi
                    </p>

                    <div class="grid grid-cols-2 gap-2">

                        {{-- 1 BULAN --}}
                        <label
                            class="duration-option
                                   relative
                                   p-3
                                   rounded-xl
                                   border border-emerald-500/50
                                   bg-emerald-500/10
                                   cursor-pointer
                                   transition"
                        >

                            <input
                                type="radio"
                                name="duration_months"
                                value="1"
                                checked
                                class="duration-radio sr-only"
                                data-discount="0"
                            >

                            <div class="text-center">

                                <p class="text-sm font-semibold text-white">
                                    1 Bulan
                                </p>

                                <p class="text-xs text-gray-500 mt-1">
                                    Harga normal
                                </p>

                            </div>

                        </label>

                        {{-- 3 BULAN --}}
                        <label
                            class="duration-option
                                   relative
                                   p-3
                                   rounded-xl
                                   border border-white/10
                                   bg-gray-900/40
                                   cursor-pointer
                                   transition"
                        >

                            <input
                                type="radio"
                                name="duration_months"
                                value="3"
                                class="duration-radio sr-only"
                                data-discount="5"
                            >

                            <div class="text-center">

                                <p class="text-sm font-semibold text-white">
                                    3 Bulan
                                </p>

                                <p class="text-xs text-emerald-400 mt-1">
                                    Hemat 5%
                                </p>

                            </div>

                        </label>

                        {{-- 6 BULAN --}}
                        <label
                            class="duration-option
                                   relative
                                   p-3
                                   rounded-xl
                                   border border-white/10
                                   bg-gray-900/40
                                   cursor-pointer
                                   transition"
                        >

                            <input
                                type="radio"
                                name="duration_months"
                                value="6"
                                class="duration-radio sr-only"
                                data-discount="7"
                            >

                            <div class="text-center">

                                <p class="text-sm font-semibold text-white">
                                    6 Bulan
                                </p>

                                <p class="text-xs text-emerald-400 mt-1">
                                    Hemat 7%
                                </p>

                            </div>

                        </label>

                        {{-- 12 BULAN --}}
                        <label
                            class="duration-option
                                   relative
                                   p-3
                                   rounded-xl
                                   border border-white/10
                                   bg-gray-900/40
                                   cursor-pointer
                                   transition"
                        >

                            <input
                                type="radio"
                                name="duration_months"
                                value="12"
                                class="duration-radio sr-only"
                                data-discount="12"
                            >

                            <div class="text-center">

                                <p class="text-sm font-semibold text-white">
                                    1 Tahun
                                </p>

                                <p class="text-xs text-emerald-400 mt-1">
                                    Hemat 12%
                                </p>

                            </div>

                        </label>

                    </div>

                </div>
                
                {{-- KODE REFERRAL --}}
<div class="mt-5">

    <p class="text-xs font-semibold text-gray-500 uppercase mb-3">
        Kode Referral
    </p>

    <input
        type="text"
        name="referral_code"
        id="referral-code"
        value="{{ old('referral_code') }}"
        placeholder="Masukkan kode referral (opsional)"
        maxlength="50"
        class="w-full bg-gray-900 border border-white/10
               rounded-xl px-4 py-2.5
               text-white text-sm
               outline-none
               focus:ring-2 focus:ring-emerald-500
               uppercase"
    >
    <div
    id="referral-status"
    class="text-xs mt-2 hidden"
></div>

    <p class="text-xs text-gray-500 mt-2">
        Referral memberikan tambahan diskon 8% untuk durasi 6 bulan dan 1 tahun.
    </p>

    @error('referral_code')
        <p class="text-xs text-red-400 mt-2">
            {{ $message }}
        </p>
    @enderror

    @if(session('error'))
        <p class="text-xs text-red-400 mt-2">
            {{ session('error') }}
        </p>
    @endif

</div>

                {{-- METODE PEMBAYARAN --}}
                <div class="mt-5">

                    <p class="text-xs font-semibold
                              text-gray-500 uppercase mb-3">
                        Metode Pembayaran
                    </p>

                    {{-- METODE QRIS --}}
                    <label
                        class="flex items-center gap-3
                               p-3 rounded-xl
                               border border-white/10
                               bg-gray-900/40
                               cursor-pointer"
                    >

                        <input
                            type="radio"
                            name="payment_method"
                            value="qris"
                            checked
                            class="accent-emerald-500"
                        >

                        <div>
                            <p class="text-sm font-semibold text-white">
                                QRIS
                            </p>

                            <p class="text-xs text-gray-500">
                                Scan menggunakan aplikasi pembayaran
                            </p>
                        </div>

                    </label>

                    {{-- METODE TRANSFER --}}
                    <label
                        class="flex items-center gap-3
                               p-3 mt-3 rounded-xl
                               border border-white/10
                               bg-gray-900/40
                               cursor-pointer"
                    >

                        <input
                            type="radio"
                            name="payment_method"
                            value="transfer"
                            class="accent-emerald-500"
                        >

                        <div>
                            <p class="text-sm font-semibold text-white">
                                Transfer Bank
                            </p>

                            <p class="text-xs text-gray-500">
                                Transfer melalui rekening bank
                            </p>
                        </div>

                    </label>

                </div>

                {{-- TOTAL --}}
                <div class="mt-6 pt-5 border-t border-white/10">

                    <div class="flex items-center justify-between">

                        <span class="text-sm text-gray-400">
                            Total
                        </span>

                        <span
                            id="payment-total"
                            class="text-xl font-bold text-white"
                        >
                            Rp {{ number_format($plan->price, 0, ',', '.') }}
                        </span>

                    </div>

                    <p
                        id="payment-discount"
                        class="text-xs text-emerald-400 text-right mt-1"
                    ></p>

                </div>

                {{-- BUTTON --}}
                <button
                    type="submit"
                    class="mt-5 w-full px-4 py-3 rounded-xl
                           bg-emerald-500
                           hover:bg-emerald-400
                           text-gray-950
                           font-semibold
                           transition"
                >
                    Bayar Sekarang
                </button>

            </form>

        @else

            {{-- PAKET GRATIS --}}
            <div class="mt-6 pt-5 border-t border-white/10">

                <div class="flex items-center justify-between">

                    <span class="text-sm text-gray-400">
                        Total
                    </span>

                    <span class="text-xl font-bold text-white">
                        Gratis
                    </span>

                </div>

            </div>

            <div class="mt-5 p-3 rounded-xl
                        bg-emerald-500/10
                        border border-emerald-500/20">

                <p class="text-xs text-emerald-400 leading-relaxed">
                    Paket gratis tidak memerlukan pembayaran.
                </p>

            </div>

        @endif

        {{-- KEMBALI --}}
        <div class="mt-3 text-center">

            <a
                href="{{ route('paket') }}"
                class="text-sm text-gray-500
                       hover:text-gray-300 transition"
            >
                ← Kembali ke Paket
            </a>

        </div>

    </div>

</div>

</div>{{-- HITUNG HARGA DURASI --}}
@if($plan->price > 0)

<script>
document.addEventListener('DOMContentLoaded', function () {

    const basePrice = {{ (float) $plan->price }};

    const durationRadios = document.querySelectorAll('.duration-radio');
    const durationOptions = document.querySelectorAll('.duration-option');

    const totalElement = document.getElementById('payment-total');
    const discountElement = document.getElementById('payment-discount');
    const referralCodeElement = document.getElementById('referral-code');
    const referralStatusElement =
    document.getElementById('referral-status');

let referralIsValid = false;

    function formatRupiah(number) {
        return new Intl.NumberFormat('id-ID').format(number);
    }

    function updatePayment() {

        const selected = document.querySelector(
            '.duration-radio:checked'
        );

        if (!selected) {
            return;
        }

        const months = parseInt(selected.value);
        const discountPercent = parseFloat(
            selected.dataset.discount
        );

        const normalAmount = basePrice * months;

        const discountAmount = Math.round(
    normalAmount * (discountPercent / 100)
);

const amountAfterDurationDiscount =
    normalAmount - discountAmount;

/*
 * Referral hanya berlaku untuk:
 * 6 bulan dan 12 bulan.
 */
const referralDiscountPercent =
    [6, 12].includes(months) &&
    referralIsValid
        ? 8
        : 0;

const referralDiscountAmount = Math.round(
    amountAfterDurationDiscount *
    (referralDiscountPercent / 100)
);

const finalAmount = Math.round(
    amountAfterDurationDiscount -
    referralDiscountAmount
);

        totalElement.textContent =
            'Rp ' + formatRupiah(finalAmount);

        if (discountPercent > 0 || referralDiscountPercent > 0) {

    let discountText = '';

    if (discountPercent > 0) {
        discountText +=
            'Diskon durasi ' +
            discountPercent +
            '% • Hemat Rp ' +
            formatRupiah(discountAmount);
    }

    if (referralDiscountPercent > 0) {

        if (discountText !== '') {
            discountText += ' • ';
        }

        discountText +=
            'Referral 8% • Hemat Rp ' +
            formatRupiah(referralDiscountAmount);
    }

    discountElement.textContent = discountText;

} else {

    discountElement.textContent = '';
}

        durationOptions.forEach(function (option) {

            const radio = option.querySelector('.duration-radio');

            if (radio.checked) {

                option.classList.add(
                    'border-emerald-500/50',
                    'bg-emerald-500/10'
                );

                option.classList.remove(
                    'border-white/10',
                    'bg-gray-900/40'
                );

            } else {

                option.classList.remove(
                    'border-emerald-500/50',
                    'bg-emerald-500/10'
                );

                option.classList.add(
                    'border-white/10',
                    'bg-gray-900/40'
                );
            }

        });
    }

    durationRadios.forEach(function (radio) {

        radio.addEventListener(
            'change',
            updatePayment
        );

    });
    
    if (referralCodeElement) {

    referralCodeElement.addEventListener(
        'input',
        function () {

            referralIsValid = false;

            if (referralStatusElement) {
                referralStatusElement.textContent = '';
                referralStatusElement.classList.add('hidden');
            }

            updatePayment();
        }
    );

    referralCodeElement.addEventListener(
        'blur',
        async function () {

            const code =
                referralCodeElement.value.trim();

            if (code === '') {

                referralIsValid = false;

                if (referralStatusElement) {
                    referralStatusElement.textContent = '';
                    referralStatusElement.classList.add('hidden');
                }

                updatePayment();

                return;
            }

            try {

                const response = await fetch(
                    `{{ route('paket.referral.validate') }}?code=${encodeURIComponent(code)}`
                );

                const data = await response.json();

                if (data.valid) {

                    referralIsValid = true;

                    referralStatusElement.textContent =
                        '✓ ' + data.message;

                    referralStatusElement.classList.remove(
                        'hidden',
                        'text-red-400'
                    );

                    referralStatusElement.classList.add(
                        'text-emerald-400'
                    );

                } else {

                    referralIsValid = false;

                    referralStatusElement.textContent =
                        '✕ ' + data.message;

                    referralStatusElement.classList.remove(
                        'hidden',
                        'text-emerald-400'
                    );

                    referralStatusElement.classList.add(
                        'text-red-400'
                    );
                }

                updatePayment();

            } catch (error) {

                referralIsValid = false;

                referralStatusElement.textContent =
                    '✕ Gagal memvalidasi kode referral.';

                referralStatusElement.classList.remove(
                    'hidden',
                    'text-emerald-400'
                );

                referralStatusElement.classList.add(
                    'text-red-400'
                );

                updatePayment();
            }
        }
    );

}

    updatePayment();

});
</script>@endif

@endsection