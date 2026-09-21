@extends('layouts.app')

@section('title', 'Pembayaran Paket')
@section('header', 'Pembayaran Paket')

@section('content')

<div class="max-w-xl mx-auto"><div class="bg-gray-800/80
            border border-white/10
            rounded-2xl
            p-6 sm:p-8
            shadow-xl">

    {{-- STATUS --}}
    <div class="text-center">

        <div class="mx-auto
                    w-16 h-16
                    flex items-center justify-center
                    rounded-full
                    bg-yellow-500/10
                    border border-yellow-500/20">

            <span class="text-2xl text-yellow-400">
                💳
            </span>

        </div>

        <h1 class="mt-5 text-2xl font-bold text-white">
            Pembayaran Paket
        </h1>

        <p class="mt-2 text-sm text-gray-400">
            Selesaikan pembayaran untuk mengaktifkan paket Anda.
        </p>

    </div>


    {{-- DETAIL PEMBAYARAN --}}
    <div class="mt-6
                rounded-xl
                bg-gray-900/50
                border border-white/10
                p-4
                space-y-3">

        <div class="flex items-center justify-between gap-4">

            <span class="text-sm text-gray-500">
                Paket
            </span>

            <span class="text-sm font-semibold text-white">
                {{ strtoupper($payment->plan->name) }}
            </span>

        </div>

        <div class="flex items-center justify-between gap-4">

            <span class="text-sm text-gray-500">
                Durasi
            </span>

            <span class="text-sm font-semibold text-white">
                {{ $payment->duration_months }} bulan
            </span>

        </div>

        <div class="flex items-center justify-between gap-4">

            <span class="text-sm text-gray-500">
                Referensi
            </span>

            <span class="text-xs font-mono text-gray-300 text-right">
                {{ $payment->reference }}
            </span>

        </div>

        @if($payment->midtrans_order_id)

            <div class="flex items-center justify-between gap-4">

                <span class="text-sm text-gray-500">
                    Order ID
                </span>

                <span class="text-xs font-mono text-gray-300 text-right">
                    {{ $payment->midtrans_order_id }}
                </span>

            </div>

        @endif

        <div class="pt-3 border-t border-white/10
                    flex items-center justify-between gap-4">

            <span class="text-sm text-gray-400">
                Total
            </span>

            <span class="text-xl font-bold text-emerald-400">
                Rp {{ number_format($payment->amount, 0, ',', '.') }}
            </span>

        </div>

    </div>


    {{-- PEMBAYARAN MIDTRANS --}}
    @if(
        $payment->status === 'pending' &&
        $payment->midtrans_snap_token
    )

        <div class="mt-5
                    rounded-xl
                    bg-gray-900/50
                    border border-white/10
                    p-5">

            <div class="text-center">

                <p class="text-xs font-semibold
                          text-gray-500 uppercase">
                    Pembayaran Aman
                </p>

                <h2 class="mt-1 text-lg font-bold text-white">
                    Lanjutkan Pembayaran
                </h2>

                <p class="mt-2 text-xs
                          text-gray-500
                          leading-relaxed">

                    Anda akan diarahkan ke halaman pembayaran
                    Midtrans untuk memilih metode pembayaran.

                </p>

            </div>


            <button
                id="pay-button"
                type="button"
                class="mt-5 w-full px-4 py-3 rounded-xl
                       bg-emerald-500
                       hover:bg-emerald-400
                       text-gray-950
                       font-semibold
                       transition"
            >
                Bayar Sekarang
            </button>
            
            <form method="POST"
      action="{{ route('paket.payment.check-status', $payment) }}"
      class="mt-3">
    @csrf

    <button type="submit"
            class="w-full rounded-lg bg-gray-700 px-4 py-3 text-sm font-semibold text-white hover:bg-gray-600">
        🔄 Cek Status Pembayaran
    </button>
</form>

            <p class="mt-3 text-[11px]
                      text-gray-600 text-center">

                Pembayaran menggunakan Midtrans Sandbox
                untuk pengujian.

            </p>

        </div>

    @endif


    {{-- STATUS INFO --}}
    @if($payment->status === 'pending')

        <div class="mt-5
                    p-4 rounded-xl
                    bg-yellow-500/10
                    border border-yellow-500/20">

            <p class="text-sm text-yellow-400 font-medium">
                Status pembayaran: Menunggu Pembayaran
            </p>

            <p class="mt-1 text-xs
                      text-yellow-400/70
                      leading-relaxed">

                Paket belum diaktifkan. Paket akan aktif setelah
                pembayaran berhasil dikonfirmasi.

            </p>

        </div>

    @elseif($payment->status === 'paid')

        <div class="mt-5
                    p-4 rounded-xl
                    bg-emerald-500/10
                    border border-emerald-500/20">

            <p class="text-sm
                      text-emerald-400
                      font-medium">

                ✓ Pembayaran berhasil

            </p>

            <p class="mt-1 text-xs
                      text-emerald-400/70
                      leading-relaxed">

                Pembayaran telah dikonfirmasi dan paket Anda
                sudah diaktifkan.

            </p>

        </div>

    @else

        <div class="mt-5
                    p-4 rounded-xl
                    bg-gray-500/10
                    border border-white/10">

            <p class="text-sm text-gray-300 font-medium">
                Pembayaran sudah diproses.
            </p>

        </div>

    @endif


    {{-- KEMBALI --}}
    <div class="mt-4 text-center">

        <a
            href="{{ route('paket') }}"
            class="text-sm text-gray-500
                   hover:text-gray-300 transition"
        >
            ← Kembali ke Paket
        </a>

    </div>

</div>

</div>{{-- MIDTRANS SNAP --}}
@if(
$payment->status === 'pending' &&
$payment->midtrans_snap_token
)

<script
    src="https://app.sandbox.midtrans.com/snap/snap.js"
    data-client-key="{{ config('midtrans.client_key') }}"
></script>

<script>

    document
        .getElementById('pay-button')
        .addEventListener('click', function () {

            window.snap.pay(
                '{{ $payment->midtrans_snap_token }}',
                {

                    onSuccess: function (result) {

                        console.log(
                            'Midtrans success:',
                            result
                        );

                        window.location.href =
                            '{{ route('paket.payment.pending', $payment) }}';

                    },

                    onPending: function (result) {

                        console.log(
                            'Midtrans pending:',
                            result
                        );

                        window.location.href =
                            '{{ route('paket.payment.pending', $payment) }}';

                    },

                    onError: function (result) {

                        console.log(
                            'Midtrans error:',
                            result
                        );

                        alert(
                            'Pembayaran gagal. Silakan coba lagi.'
                        );

                    },

                    onClose: function () {

                        console.log(
                            'Midtrans popup ditutup.'
                        );

                    }

                }
            );

        });

</script>

@endif

@endsection