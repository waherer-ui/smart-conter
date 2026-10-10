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
            ✏️ Edit Pengumuman
        </h1>

        <p class="text-sm text-gray-500 mt-1">
            Perbarui informasi pengumuman Kasir½M.
        </p>
    </div>

    {{-- STATUS --}}
    <div class="mb-5 rounded-xl border border-blue-100 bg-blue-50 p-4">
        <p class="text-sm font-semibold text-blue-800">
            Status:
            @if($announcement->status === 'published')
                <span class="text-emerald-700">Diterbitkan</span>
            @elseif($announcement->status === 'archived')
                <span class="text-gray-600">Diarsipkan</span>
            @else
                <span class="text-amber-700">Draf</span>
            @endif
        </p>

        <p class="text-xs text-blue-700 mt-1">
            Pengumuman yang diarsipkan tidak dapat diedit.
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
          action="{{ route('admin-kasirku.announcements.update', $announcement) }}"
          class="space-y-5">
        @csrf
        @method('PUT')

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
                value="{{ old('title', $announcement->title) }}"
                maxlength="255"
                required
                class="w-full rounded-xl border border-gray-200 px-4 py-3
                       text-sm text-gray-800
                       focus:border-emerald-500 focus:ring-2
                       focus:ring-emerald-100 outline-none"
            >
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
                rows="10"
                maxlength="50000"
                required
                class="w-full rounded-xl border border-gray-200 px-4 py-3
                       text-sm text-gray-800
                       focus:border-emerald-500 focus:ring-2
                       focus:ring-emerald-100 outline-none resize-y"
            >{{ old('content', $announcement->content) }}</textarea>

            <div class="flex justify-between gap-3 mt-2">
                <p class="text-xs text-gray-400">
                    Maksimal 50.000 karakter.
                </p>

                <p class="text-xs text-gray-400">
                    <span id="content-count">0</span>/50.000
                </p>
            </div>
        </div>

        {{-- TARGET --}}
        <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm">
            <label for="target_audience"
                   class="block text-sm font-semibold text-gray-800 mb-2">
                Target Penerima
                <span class="text-red-500">*</span>
            </label>

            @php
                $audienceLabels = [
                    'all' => 'Semua Pengguna',
                    'owners' => 'Pemilik Toko',
                    'free' => 'Pengguna Paket Free',
                    'pro' => 'Pengguna Paket Pro',
                    'premium' => 'Pengguna Paket Premium',
                ];
            @endphp

            <select
                id="target_audience"
                name="target_audience"
                required
                class="w-full rounded-xl border border-gray-200 px-4 py-3
                       text-sm text-gray-800 bg-white
                       focus:border-emerald-500 focus:ring-2
                       focus:ring-emerald-100 outline-none"
            >
                @foreach($audienceLabels as $value => $label)
                    <option value="{{ $value }}"
                        {{ old('target_audience', $announcement->target_audience) === $value ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>

            <div class="mt-4 rounded-xl bg-blue-50 border border-blue-100 p-4">
                <p class="text-sm font-semibold text-blue-800 mb-1">
                    ℹ️ Informasi Target Penerima
                </p>

                <p id="audience-description"
                   class="text-xs leading-5 text-blue-700"></p>
            </div>
        </div>

        {{-- PUBLISHED WARNING --}}
        @if($announcement->status === 'published')
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
                <p class="text-sm font-semibold text-amber-800">
                    ⚠️ Pengumuman sudah diterbitkan
                </p>

                <p class="text-xs text-amber-700 mt-1 leading-5">
                    Mengubah judul, isi, atau target tidak otomatis mengirim
                    notifikasi baru kepada penerima. Notifikasi sebelumnya
                    tetap tersimpan.
                </p>
            </div>
        @else
            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                <p class="text-sm text-gray-700">
                    💡 Perubahan akan disimpan tanpa menerbitkan pengumuman.
                    Kamu bisa menerbitkannya dari halaman daftar pengumuman.
                </p>
            </div>
        @endif

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
                💾 Simpan Perubahan
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

    const descriptions = {
        all: 'Untuk pemilik toko dan staf kasir yang terdaftar.',
        owners: 'Khusus akun pemilik toko.',
        free: 'Khusus pemilik toko dengan paket Free aktif.',
        pro: 'Khusus pemilik toko dengan paket Pro aktif.',
        premium: 'Khusus pemilik toko dengan paket Premium aktif.'
    };

    function updateCount() {
        counter.textContent = content.value.length.toLocaleString('id-ID');
    }

    function updateDescription() {
        description.textContent = descriptions[audience.value] || '';
    }

    content.addEventListener('input', updateCount);
    audience.addEventListener('change', updateDescription);

    updateCount();
    updateDescription();
});
</script>
@endsection