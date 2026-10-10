@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-5">

    {{-- HEADER --}}
    <div class="mb-6">
        <a href="{{ route('admin-kasirku.announcements.index') }}"
           class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-emerald-600 mb-4">
            ← Kembali ke Pengumuman
        </a>

        <h1 class="text-2xl font-bold text-gray-800">
            Buat Pengumuman
        </h1>

        <p class="text-sm text-gray-500 mt-1">
            Sampaikan informasi penting kepada pengguna Kasir½M.
        </p>
    </div>

    {{-- VALIDATION ERRORS --}}
    @if($errors->any())
        <div class="mb-5 rounded-xl border border-red-200 bg-red-50 p-4">
            <p class="font-semibold text-red-700 text-sm mb-2">
                Ada data yang perlu diperbaiki:
            </p>

            <ul class="list-disc pl-5 text-sm text-red-600 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- FORM --}}
    <form method="POST"
          action="{{ route('admin-kasirku.announcements.store') }}"
          class="space-y-5">
        @csrf

        {{-- TITLE --}}
        <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm">
            <label for="title"
                   class="block text-sm font-semibold text-gray-800 mb-2">
                Judul Pengumuman
                <span class="text-red-500">*</span>
            </label>

            <input
                type="text"
                id="title"
                name="title"
                value="{{ old('title') }}"
                maxlength="255"
                required
                placeholder="Contoh: Pembaruan Fitur Kasir½M"
                class="w-full rounded-xl border border-gray-200 px-4 py-3
                       text-sm text-gray-800 placeholder-gray-400
                       focus:border-emerald-500 focus:ring-2
                       focus:ring-emerald-100 outline-none"
            >

            <p class="text-xs text-gray-400 mt-2">
                Gunakan judul singkat dan mudah dipahami.
            </p>
        </div>

        {{-- CONTENT --}}
        <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm">
            <label for="content"
                   class="block text-sm font-semibold text-gray-800 mb-2">
                Isi Pengumuman
                <span class="text-red-500">*</span>
            </label>

            <textarea
                id="content"
                name="content"
                rows="9"
                maxlength="50000"
                required
                placeholder="Tuliskan informasi yang ingin disampaikan kepada pengguna..."
                class="w-full rounded-xl border border-gray-200 px-4 py-3
                       text-sm text-gray-800 placeholder-gray-400
                       focus:border-emerald-500 focus:ring-2
                       focus:ring-emerald-100 outline-none resize-y"
            >{{ old('content') }}</textarea>

            <div class="flex justify-between gap-3 mt-2">
                <p class="text-xs text-gray-400">
                    Maksimal 50.000 karakter.
                </p>

                <p class="text-xs text-gray-400">
                    <span id="content-count">0</span>/50.000
                </p>
            </div>
        </div>

        {{-- TARGET AUDIENCE --}}
        <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm">
            <label for="target_audience"
                   class="block text-sm font-semibold text-gray-800 mb-2">
                Target Penerima
                <span class="text-red-500">*</span>
            </label>

            <select
                id="target_audience"
                name="target_audience"
                required
                class="w-full rounded-xl border border-gray-200 px-4 py-3
                       text-sm text-gray-800 bg-white
                       focus:border-emerald-500 focus:ring-2
                       focus:ring-emerald-100 outline-none"
            >
                <option value="all" {{ old('target_audience', 'all') === 'all' ? 'selected' : '' }}>
                    Semua Pengguna
                </option>

                <option value="owners" {{ old('target_audience') === 'owners' ? 'selected' : '' }}>
                    Pemilik Toko
                </option>

                <option value="free" {{ old('target_audience') === 'free' ? 'selected' : '' }}>
                    Pengguna Paket Free
                </option>

                <option value="pro" {{ old('target_audience') === 'pro' ? 'selected' : '' }}>
                    Pengguna Paket Pro
                </option>

                <option value="premium" {{ old('target_audience') === 'premium' ? 'selected' : '' }}>
                    Pengguna Paket Premium
                </option>
            </select>

            <div class="mt-4 rounded-xl bg-blue-50 border border-blue-100 p-4">
                <p class="text-sm font-semibold text-blue-800 mb-1">
                    ℹ️ Informasi Target Penerima
                </p>

                <p id="audience-description"
                   class="text-xs leading-5 text-blue-700">
                    Pengumuman ditujukan kepada pemilik toko dan staf kasir yang terdaftar.
                </p>
            </div>
        </div>

        {{-- IMPORTANT NOTICE --}}
        <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
            <p class="text-sm font-semibold text-amber-800">
                ⚠️ Pengumuman disimpan sebagai draf
            </p>

            <p class="text-xs text-amber-700 mt-1 leading-5">
                Menyimpan formulir ini belum mengirim notifikasi.
                Notifikasi baru dibuat setelah pengumuman diterbitkan dari halaman daftar pengumuman.
            </p>
        </div>

        {{-- ACTIONS --}}
        <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
            <a href="{{ route('admin-kasirku.announcements.index') }}"
               class="inline-flex justify-center items-center px-5 py-3
                      rounded-xl border border-gray-200 text-gray-600
                      hover:bg-gray-50 text-sm font-semibold">
                Batal
            </a>

            <button
                type="submit"
                class="inline-flex justify-center items-center gap-2
                       px-5 py-3 rounded-xl bg-emerald-600
                       hover:bg-emerald-700 text-white text-sm
                       font-semibold shadow-sm transition"
            >
                💾 Simpan sebagai Draf
            </button>
        </div>

    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const content = document.getElementById('content');
    const counter = document.getElementById('content-count');
    const audience = document.getElementById('target_audience');
    const description = document.getElementById('audience-description');

    function updateCount() {
        counter.textContent = content.value.length.toLocaleString('id-ID');
    }

    const descriptions = {
        all: 'Pengumuman ditujukan kepada pemilik toko dan staf kasir yang terdaftar.',
        owners: 'Pengumuman hanya ditujukan kepada akun pemilik toko.',
        free: 'Pengumuman ditujukan kepada pemilik toko dengan paket Free yang aktif.',
        pro: 'Pengumuman ditujukan kepada pemilik toko dengan paket Pro yang aktif.',
        premium: 'Pengumuman ditujukan kepada pemilik toko dengan paket Premium yang aktif.'
    };

    function updateAudienceDescription() {
        description.textContent = descriptions[audience.value] || '';
    }

    content.addEventListener('input', updateCount);
    audience.addEventListener('change', updateAudienceDescription);

    updateCount();
    updateAudienceDescription();
});
</script>
@endsection