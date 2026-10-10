@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-6">

    <a
        href="{{ route('admin-kasirku.tutorial-videos.index') }}"
        class="inline-flex text-sm text-emerald-700 font-semibold mb-5"
    >
        ← Kembali ke Video Tutorial
    </a>

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

        <div class="p-6 border-b border-gray-100">
            <h1 class="text-2xl font-bold text-gray-800">
                Edit Video Tutorial
            </h1>

            <p class="text-sm text-gray-500 mt-2">
                Perbarui informasi video panduan Kasir½M.
            </p>
        </div>

        <form
            action="{{ route('admin-kasirku.tutorial-videos.update', $tutorialVideo) }}"
            method="POST"
            class="p-6 space-y-5"
        >
            @csrf
            @method('PUT')

            {{-- JUDUL --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Judul Tutorial *
                </label>

                <input
                    type="text"
                    name="title"
                    value="{{ old('title', $tutorialVideo->title) }}"
                    required
                    maxlength="255"
                    class="w-full rounded-xl border border-gray-300 px-4 py-3"
                >

                @error('title')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- KATEGORI --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Kategori Panduan *
                </label>

                <select
                    name="category"
                    required
                    class="w-full rounded-xl border border-gray-300 px-4 py-3"
                >
                    @foreach([
                        'Pengenalan Kasir½M',
                        'Pengaturan Toko',
                        'Manajemen Produk',
                        'Transaksi Kasir',
                        'Utang dan Pembayaran',
                        'Laporan Penjualan',
                        'Manajemen Staf',
                        'Absensi',
                        'Langganan dan Pembayaran',
                        'Lainnya',
                    ] as $category)
                        <option
                            value="{{ $category }}"
                            @selected(old('category', $tutorialVideo->category) === $category)
                        >
                            {{ $category }}
                        </option>
                    @endforeach
                </select>

                @error('category')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- DESKRIPSI --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Deskripsi
                </label>

                <textarea
                    name="description"
                    rows="4"
                    maxlength="5000"
                    class="w-full rounded-xl border border-gray-300 px-4 py-3"
                >{{ old('description', $tutorialVideo->description) }}</textarea>

                @error('description')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- SUMBER --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Sumber Video *
                </label>

                <select
                    name="video_provider"
                    required
                    class="w-full rounded-xl border border-gray-300 px-4 py-3"
                >
                    <option
                        value="youtube"
                        @selected(old('video_provider', $tutorialVideo->video_provider) === 'youtube')
                    >
                        YouTube
                    </option>

                    <option
                        value="google_drive"
                        @selected(old('video_provider', $tutorialVideo->video_provider) === 'google_drive')
                    >
                        Google Drive
                    </option>
                </select>

                @error('video_provider')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- LINK VIDEO --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Link Video *
                </label>

                <input
                    type="url"
                    name="video_url"
                    value="{{ old('video_url', $tutorialVideo->video_url) }}"
                    required
                    maxlength="2048"
                    class="w-full rounded-xl border border-gray-300 px-4 py-3"
                >

                @error('video_url')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- THUMBNAIL --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Link Thumbnail (Opsional)
                </label>

                <input
                    type="url"
                    name="thumbnail_url"
                    value="{{ old('thumbnail_url', $tutorialVideo->thumbnail_url) }}"
                    maxlength="2048"
                    class="w-full rounded-xl border border-gray-300 px-4 py-3"
                >

                @error('thumbnail_url')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- URUTAN --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Urutan Tampilan
                </label>

                <input
                    type="number"
                    name="sort_order"
                    value="{{ old('sort_order', $tutorialVideo->sort_order) }}"
                    min="0"
                    class="w-full rounded-xl border border-gray-300 px-4 py-3"
                >

                @error('sort_order')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- STATUS SAAT INI --}}
            <div class="rounded-xl {{ $tutorialVideo->is_active ? 'bg-emerald-50 border-emerald-200' : 'bg-gray-50 border-gray-200' }} border p-4">

                <p class="text-sm font-semibold {{ $tutorialVideo->is_active ? 'text-emerald-800' : 'text-gray-700' }}">
                    Status saat ini:
                    {{ $tutorialVideo->is_active ? 'Aktif' : 'Nonaktif' }}
                </p>

                <p class="text-sm text-gray-600 mt-1">
                    Status dapat diubah melalui tombol Aktifkan atau Nonaktifkan pada daftar video.
                </p>

            </div>

            {{-- ACTION --}}
            <div class="flex flex-col sm:flex-row gap-3 pt-2">

                <button
                    type="submit"
                    class="flex-1 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-3"
                >
                    Simpan Perubahan
                </button>

                <a
                    href="{{ route('admin-kasirku.tutorial-videos.index') }}"
                    class="text-center rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-3 px-5"
                >
                    Batal
                </a>

            </div>

        </form>

    </div>

</div>
@endsection