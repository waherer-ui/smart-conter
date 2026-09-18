@extends('layouts.app')

@section('header', 'Pengaturan Profil')

@section('content')

<div class="max-w-xl mx-auto space-y-6 pb-12">@if(session('success'))
    <div class="bg-emerald-500/10 border border-emerald-500/35 text-emerald-400 px-4 py-3 rounded-xl text-sm">
        {{ session('success') }}
    </div>
@endif

{{-- =========================================================
     PROFIL
========================================================== --}}
<div class="bg-gray-800/80 border border-white/10 rounded-2xl p-6 shadow-xl backdrop-blur-md">

    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf
        @method('PUT')

        {{-- PREVIEW FOTO --}}
        <div class="flex flex-col items-center mb-6">

            <div class="w-20 h-20 rounded-full overflow-hidden bg-gray-700 border border-white/10 mb-3 flex items-center justify-center">

                @if($user->avatar ?? false)

                    <a
                        href="{{ Storage::disk('s3')->url($user->avatar) }}"
                        target="_blank"
                    >
                        <img
                            src="{{ Storage::disk('s3')->url($user->avatar) }}"
                            alt="Avatar"
                            class="w-full h-full object-cover"
                        >
                    </a>

                @else

                    <span class="text-2xl text-gray-400">👤</span>

                @endif

            </div>

            <label class="block text-xs font-medium text-gray-300 mb-1">
                Ganti Foto Profil
            </label>

            <input
                type="file"
                name="avatar"
                accept="image/*"
                class="w-full text-xs text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-500 cursor-pointer"
            >

        </div>


        {{-- NAMA --}}
        <div>

            <label class="block text-xs font-medium text-gray-300 mb-1">
                Nama Lengkap
            </label>

            <input
                type="text"
                name="name"
                value="{{ old('name', $user->name) }}"
                class="w-full bg-gray-900 border border-white/10 rounded-xl px-4 py-2.5 text-white text-sm outline-none focus:ring-2 focus:ring-indigo-500"
                required
            >

        </div>


        {{-- EMAIL --}}
        <div>

            <label class="block text-xs font-medium text-gray-300 mb-1">
                Email / Username
            </label>

            <input
                type="text"
                value="{{ $user->email }}"
                class="w-full bg-gray-900/50 border border-white/10 rounded-xl px-4 py-2.5 text-gray-400 text-sm outline-none cursor-not-allowed"
                disabled
            >

        </div>


        <hr class="border-white/10 my-4">


        {{-- PASSWORD BARU --}}
        <div>

            <label class="block text-xs font-medium text-gray-300 mb-1">
                Password Baru (Kosongkan jika tidak ingin diubah)
            </label>

            <input
                type="password"
                name="password"
                placeholder="••••••••"
                class="w-full bg-gray-900 border border-white/10 rounded-xl px-4 py-2.5 text-white text-sm outline-none focus:ring-2 focus:ring-indigo-500"
            >

        </div>


        <div class="flex justify-end pt-2">

            <button
                type="submit"
                class="bg-indigo-600 hover:bg-indigo-500 text-white px-5 py-2.5 rounded-xl text-xs font-medium transition shadow"
            >
                Simpan Perubahan
            </button>

        </div>

    </form>

</div>


{{-- =========================================================
     REFERRAL
========================================================== --}}
@if($referral)

    <div class="bg-gray-800/80 border border-emerald-500/20 rounded-2xl p-6 shadow-xl backdrop-blur-md">

        <div class="flex items-start gap-3 mb-5">

            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-xl shrink-0">
                🎁
            </div>

            <div>

                <h2 class="text-base font-semibold text-white">
                    Referral KasirKU
                </h2>

                <p class="text-xs text-gray-400 mt-1">
                    Bagikan kode referral Anda kepada pengguna lain.
                </p>

            </div>

        </div>


        {{-- KODE --}}
        <div class="bg-gray-900 border border-white/10 rounded-xl p-4">

            <p class="text-[11px] text-gray-500 mb-2">
                Kode Referral Anda
            </p>

            <div class="flex items-center gap-3">

                <div
                    id="referral-code"
                    class="flex-1 text-lg sm:text-xl font-bold tracking-widest text-emerald-400 truncate"
                >
                    {{ $referral->code }}
                </div>

                <button
                    type="button"
                    onclick="copyReferralCode()"
                    class="shrink-0 bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2.5 rounded-xl text-xs font-semibold transition"
                >
                    Salin
                </button>

            </div>

        </div>


        <div class="flex items-center gap-2 mt-4 text-xs">

            <span class="w-2 h-2 rounded-full {{ $referral->is_active ? 'bg-emerald-400' : 'bg-gray-500' }}"></span>

            <span class="{{ $referral->is_active ? 'text-emerald-400' : 'text-gray-500' }}">
                {{ $referral->is_active ? 'Referral aktif' : 'Referral tidak aktif' }}
            </span>

        </div>

        @php
    $referralPayments = $referral->payments()
        ->where('status', 'paid')
        ->get();

    $referralUsageCount = $referralPayments->count();

    $referralTransactionTotal = $referralPayments->sum('amount');

    $referralDiscountTotal = $referralPayments->sum(
        'referral_discount_amount'
    );
@endphp


{{-- STATISTIK REFERRAL --}}
<div class="grid grid-cols-3 gap-2 mt-5">

    {{-- DIGUNAKAN --}}
    <div class="bg-gray-900 border border-white/10 rounded-xl p-3 text-center">

        <p class="text-lg font-bold text-white">
            {{ $referralUsageCount }}
        </p>

        <p class="text-[10px] text-gray-500 mt-1">
            Digunakan
        </p>

    </div>


    {{-- TRANSAKSI --}}
    <div class="bg-gray-900 border border-white/10 rounded-xl p-3 text-center">

        <p class="text-sm font-bold text-emerald-400">
            Rp {{ number_format($referralTransactionTotal, 0, ',', '.') }}
        </p>

        <p class="text-[10px] text-gray-500 mt-1">
            Transaksi
        </p>

    </div>


    {{-- DISKON --}}
    <div class="bg-gray-900 border border-white/10 rounded-xl p-3 text-center">

        <p class="text-sm font-bold text-indigo-400">
            Rp {{ number_format($referralDiscountTotal, 0, ',', '.') }}
        </p>

        <p class="text-[10px] text-gray-500 mt-1">
            Total Diskon
        </p>

    </div>

</div>

    </div>

@endif

</div>{{-- =============================================================
COPY REFERRAL
============================================================= --}}

<script>
function copyReferralCode() {

    const code = document.getElementById('referral-code').innerText.trim();

    navigator.clipboard.writeText(code).then(() => {

        const button = event.currentTarget;

        const originalText = button.innerText;

        button.innerText = 'Tersalin ✓';

        setTimeout(() => {
            button.innerText = originalText;
        }, 1500);

    });

}
</script>@endsection