@extends('layouts.app')

@section('title', 'Menunggu Pembayaran')
@section('header', 'Menunggu Pembayaran')

@section('content')

<div class="max-w-xl mx-auto">

    <div class="bg-gray-800/80
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
                    ⏳
                </span>

            </div>

            <h1 class="mt-5 text-2xl font-bold text-white">
                Menunggu Pembayaran
            </h1>

            <p class="mt-2 text-sm text-gray-400">
                Selesaikan pembayaran untuk mengaktifkan paket Anda.
            </p>

        </div>


        {{-- DETAIL PAKET --}}
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
                    Metode
                </span>

                <span class="text-sm font-semibold text-white">
                    {{ strtoupper($payment->payment_method) }}
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


        {{-- INSTRUKSI QRIS --}}
        @if($payment->status === 'pending' && $payment->payment_method === 'qris')

            <div class="mt-5
                        rounded-xl
                        bg-gray-900/50
                        border border-white/10
                        p-5">

                <div class="text-center">

                    <p class="text-xs font-semibold
                              text-gray-500 uppercase">
                        Pembayaran QRIS
                    </p>

                    <h2 class="mt-1 text-lg font-bold text-white">
                        Scan QRIS untuk membayar
                    </h2>

                </div>


                {{-- QRIS SIMULASI --}}
                <div class="mt-5
                            mx-auto
                            w-48 h-48
                            rounded-2xl
                            bg-white
                            flex items-center justify-center">

                    <div class="text-center px-4">

                        <div class="text-5xl">
                            ▦
                        </div>

                        <p class="mt-2 text-xs
                                  font-semibold text-gray-700">
                            QRIS
                        </p>

                        <p class="mt-1 text-[10px] text-gray-500">
                            QRIS SIMULASI
                        </p>

                    </div>

                </div>


                <div class="mt-5 space-y-3">

                    <div class="flex items-center
                                justify-between gap-4">

                        <span class="text-sm text-gray-500">
                            Nominal
                        </span>

                        <span class="text-sm
                                     font-bold text-emerald-400">
                            Rp {{ number_format($payment->amount, 0, ',', '.') }}
                        </span>

                    </div>

                    <div class="flex items-center
                                justify-between gap-4">

                        <span class="text-sm text-gray-500">
                            Referensi
                        </span>

                        <span class="text-xs font-mono
                                     text-gray-300 text-right">
                            {{ $payment->reference }}
                        </span>

                    </div>

                </div>

                <p class="mt-4 text-xs
                          text-gray-500 text-center
                          leading-relaxed">

                    Gunakan aplikasi pembayaran yang mendukung QRIS
                    untuk melakukan pembayaran.

                </p>

            </div>

        @endif


        {{-- INSTRUKSI TRANSFER --}}
        @if($payment->status === 'pending' && $payment->payment_method === 'transfer')

            <div class="mt-5
                        rounded-xl
                        bg-gray-900/50
                        border border-white/10
                        p-5">

                <div>

                    <p class="text-xs font-semibold
                              text-gray-500 uppercase">
                        Transfer Bank
                    </p>

                    <h2 class="mt-1 text-lg font-bold text-white">
                        Silakan lakukan transfer
                    </h2>

                </div>


                <div class="mt-5
                            rounded-xl
                            bg-gray-800
                            border border-white/10
                            p-4
                            space-y-4">

                    <div>

                        <p class="text-xs text-gray-500">
                            Bank
                        </p>

                        <p class="mt-1 text-sm
                                  font-semibold text-white">
                            BCA
                        </p>

                    </div>

                    <div>

                        <p class="text-xs text-gray-500">
                            Nomor Rekening
                        </p>

                        <p class="mt-1 text-lg
                                  font-bold text-white
                                  tracking-wider">
                            XXXX XXXX XXXX
                        </p>

                    </div>

                    <div>

                        <p class="text-xs text-gray-500">
                            Atas Nama
                        </p>

                        <p class="mt-1 text-sm
                                  font-semibold text-white">
                            KasirKU
                        </p>

                    </div>

                </div>


                <div class="mt-4
                            flex items-center
                            justify-between gap-4">

                    <span class="text-sm text-gray-500">
                        Total Transfer
                    </span>

                    <span class="text-lg
                                 font-bold text-emerald-400">
                        Rp {{ number_format($payment->amount, 0, ',', '.') }}
                    </span>

                </div>


                <div class="mt-3
                            p-3 rounded-xl
                            bg-yellow-500/10
                            border border-yellow-500/20">

                    <p class="text-xs
                              text-yellow-400
                              leading-relaxed">

                        Pastikan nominal transfer sesuai dengan jumlah
                        pembayaran dan simpan bukti transfer Anda.

                    </p>

                </div>

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


        {{-- SIMULASI PEMBAYARAN --}}
        @if($payment->status === 'pending')

            <form
                action="{{ route('paket.payment.success', $payment) }}"
                method="POST"
                class="mt-5"
            >

                @csrf

                <button
                    type="submit"
                    class="w-full px-4 py-3 rounded-xl
                           bg-emerald-500
                           hover:bg-emerald-400
                           text-gray-950
                           font-semibold
                           transition"
                >
                    Simulasikan Pembayaran Berhasil
                </button>

            </form>

            <p class="mt-2 text-[11px]
                      text-gray-600 text-center">
                Tombol ini hanya untuk pengujian sistem.
            </p>

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

</div>

@endsection
