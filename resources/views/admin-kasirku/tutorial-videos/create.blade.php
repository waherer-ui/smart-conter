@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-6">

    <div class="mb-6">
        <a href="{{ route('admin-kasirku.tutorial-videos.index') }}"
           class="text-sm text-emerald-700 font-semibold">
            ← Kembali ke Video Tutorial
        </a>

        <h1 class="text-2xl font-bold text-gray-800 mt-3">
            Tambah Video Tutorial
        </h1>

        <p class="text-sm text-gray-500 mt-1">
            Tambahkan video panduan untuk pengguna Kasir½M.
        </p>
    </div>

    @if ($errors->any())
        <div class="mb-5 rounded-xl border border-red-200 bg-red-50 p-4 text-red-700">
            <p class="font-semibold mb-2">Periksa kembali data berikut:</p>
            <ul class="list-disc list-inside text-sm space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin-kasirku.tutorial-videos.store') }}"
          method="POST"
          class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 sm:p-7 space-y-5">

        @csrf

        <div>
            <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">
                Judul Video *
            </label>
            <input type="text" id="title" name="title"
                   value="{{ old('title') }}"
                   placeholder="Contoh: Cara Mengelola Produk"
                   required maxlength="255"
                   class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">
        </div>

        <div>
            <label for="category" class="block text-sm font-semibold text-gray-700 mb-2">
                Kategori *
            </label>
            <select id="category" name="category" required
                    class="w-full rounded-xl border border-gray-300 px-4 py-3">
                <option value="">Pilih kategori</option>
                @foreach ([
                    'Pengenalan Kasir½M',
                    'Pengaturan Toko',
                    'Manajemen Produk',
                    'Transaksi Kasir',
                    'Utang dan Pembayaran',
                    'Laporan Penjualan',
                    'Manajemen Staf',
                    'Absensi',
                    'Langganan dan Pembayaran',
                    'Lainnya'
                ] as $category)
                    <option value="{{ $category }}"
                        @selected(old('category') === $category)>
                        {{ $category }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                Deskripsi
            </label>
            <textarea id="description" name="description" rows="4"
                      maxlength="5000"
                      placeholder="Jelaskan isi dan manfaat video ini."
                      class="w-full rounded-xl border border-gray-300 px-4 py-3">{{ old('description') }}</textarea>
        </div>

        <div>
            <label for="video_provider" class="block text-sm font-semibold text-gray-700 mb-2">
                Sumber Video *
            </label>
            <select id="video_provider" name="video_provider" required
                    class="w-full rounded-xl border border-gray-300 px-4 py-3">
                <option value="youtube" @selected(old('video_provider', 'youtube') === 'youtube')>
                    YouTube
                </option>
                <option value="google_drive" @selected(old('video_provider') === 'google_drive')>
                    Google Drive
                </option>
            </select>
        </div>

        <div>
            <label for="video_url" class="block text-sm font-semibold text-gray-700 mb-2">
                Link Video *
            </label>
            <input type="url" id="video_url" name="video_url"
                   value="{{ old('video_url') }}"
                   placeholder="https://www.youtube.com/watch?v=..."
                   required maxlength="2048"
                   class="w-full rounded-xl border border-gray-300 px-4 py-3">

            <p id="video-help" class="text-xs text-gray-500 mt-2">
                Masukkan link video YouTube. Video harus dapat diputar oleh pengguna.
            </p>
        </div>

        <div>
            <label for="thumbnail_url" class="block text-sm font-semibold text-gray-700 mb-2">
                URL Thumbnail (Opsional)
            </label>
            <input type="url" id="thumbnail_url" name="thumbnail_url"
                   value="{{ old('thumbnail_url') }}"
                   placeholder="https://..."
                   maxlength="2048"
                   class="w-full rounded-xl border border-gray-300 px-4 py-3">
        </div>

        <div>
            <label for="sort_order" class="block text-sm font-semibold text-gray-700 mb-2">
                Urutan Tampilan
            </label>
            <input type="number" id="sort_order" name="sort_order"
                   value="{{ old('sort_order', 1) }}"
                   min="0" step="1"
                   class="w-full rounded-xl border border-gray-300 px-4 py-3">
            <p class="text-xs text-gray-500 mt-2">
                Angka lebih kecil ditampilkan lebih dahulu.
            </p>
        </div>

        <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
            <p class="text-sm font-semibold text-amber-800">
                Status awal: Nonaktif
            </p>
            <p class="text-sm text-amber-700 mt-1">
                Setelah disimpan, aktifkan video dari halaman daftar jika sudah siap ditampilkan di Pusat Bantuan.
            </p>
        </div>

        <div class="flex flex-col-reverse sm:flex-row gap-3 pt-2">
            <a href="{{ route('admin-kasirku.tutorial-videos.index') }}"
               class="text-center px-5 py-3 rounded-xl border border-gray-300 text-gray-700 font-semibold">
                Batal
            </a>

            <button type="submit"
                    class="px-5 py-3 rounded-xl bg-emerald-600 text-white font-semibold hover:bg-emerald-700">
                Simpan Video
            </button>
        </div>

    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const provider = document.getElementById('video_provider');
    const help = document.getElementById('video-help');
    const url = document.getElementById('video_url');

    function updateVideoHelp() {
        if (provider.value === 'google_drive') {
            help.textContent =
                'Masukkan link berbagi Google Drive. Pastikan akses file diatur menjadi Siapa saja yang memiliki link.';
            url.placeholder =
                'https://drive.google.com/file/d/ID_VIDEO/view';
        } else {
            help.textContent =
                'Masukkan link video YouTube. Video harus dapat diputar oleh pengguna.';
            url.placeholder =
                'https://www.youtube.com/watch?v=...';
        }
    }

    provider.addEventListener('change', updateVideoHelp);
    updateVideoHelp();
});
</script>
@endsection