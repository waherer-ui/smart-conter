@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                🎬 Video Tutorial Kasir½M
            </h1>

            <p class="text-sm text-gray-500 mt-1">
                Kelola video panduan penggunaan untuk pengguna Kasir½M.
            </p>
        </div>

        <a
            href="{{ route('admin-kasirku.tutorial-videos.create') }}"
            class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-emerald-600 text-white font-semibold hover:bg-emerald-700"
        >
            <span>＋</span>
            Tambah Video
        </a>
    </div>

    {{-- NOTIFIKASI --}}
    @if(session('success'))
        <div class="mb-5 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-5 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700">
            {{ session('error') }}
        </div>
    @endif

    {{-- RINGKASAN --}}
    <div class="grid grid-cols-2 gap-4 mb-6">

        <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
            <p class="text-sm text-gray-500">Total Video</p>

            <p class="text-3xl font-bold text-gray-800 mt-2">
                {{ $videos->total() }}
            </p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
            <p class="text-sm text-gray-500">Video Aktif</p>

            <p class="text-3xl font-bold text-emerald-600 mt-2">
                {{ \App\Models\TutorialVideo::where('is_active', true)->count() }}
            </p>
        </div>

    </div>

    {{-- DAFTAR VIDEO --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

        <div class="p-5 border-b border-gray-100">
            <h2 class="font-bold text-gray-800">
                Daftar Tutorial
            </h2>

            <p class="text-sm text-gray-500 mt-1">
                Video aktif akan ditampilkan di Pusat Bantuan.
            </p>
        </div>

        @forelse($videos as $video)

            <div class="p-5 border-b border-gray-100 last:border-b-0">

                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">

                    <div class="flex-1 min-w-0">

                        <div class="flex flex-wrap items-center gap-2 mb-2">

                            <h3 class="font-bold text-gray-800 text-lg">
                                {{ $video->title }}
                            </h3>

                            @if($video->is_active)
                                <span class="px-2.5 py-1 text-xs rounded-full bg-emerald-100 text-emerald-700 font-semibold">
                                    Aktif
                                </span>
                            @else
                                <span class="px-2.5 py-1 text-xs rounded-full bg-gray-100 text-gray-600 font-semibold">
                                    Nonaktif
                                </span>
                            @endif

                        </div>

                        <p class="text-sm text-emerald-700 font-medium mb-2">
                            {{ $video->category }}
                        </p>

                        @if($video->description)
                            <p class="text-sm text-gray-600 mb-3">
                                {{ $video->description }}
                            </p>
                        @endif

                        <div class="flex flex-wrap gap-2 text-xs text-gray-500">

                            <span class="px-3 py-1 rounded-full bg-gray-100">
                                {{ $video->video_provider === 'youtube' ? 'YouTube' : 'Google Drive' }}
                            </span>

                            <span class="px-3 py-1 rounded-full bg-gray-100">
                                Urutan: {{ $video->sort_order }}
                            </span>

                        </div>

                        <a
                            href="{{ $video->video_url }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex mt-4 text-sm text-blue-600 hover:text-blue-800 font-medium break-all"
                        >
                            Buka video ↗
                        </a>

                    </div>

                    {{-- AKSI --}}
                    <div class="flex flex-wrap gap-2">

                        <a
                            href="{{ route('admin-kasirku.tutorial-videos.edit', $video) }}"
                            class="px-4 py-2 rounded-lg bg-blue-50 text-blue-700 text-sm font-semibold hover:bg-blue-100"
                        >
                            Edit
                        </a>

                        <form
                            action="{{ route('admin-kasirku.tutorial-videos.toggle', $video) }}"
                            method="POST"
                        >
                            @csrf

                            <button
                                type="submit"
                                class="px-4 py-2 rounded-lg {{ $video->is_active ? 'bg-amber-50 text-amber-700 hover:bg-amber-100' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }} text-sm font-semibold"
                            >
                                {{ $video->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                            </button>
                        </form>

                        <form
                            action="{{ route('admin-kasirku.tutorial-videos.destroy', $video) }}"
                            method="POST"
                            onsubmit="return confirm('Yakin ingin menghapus video tutorial ini?')"
                        >
                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                class="px-4 py-2 rounded-lg bg-red-50 text-red-700 text-sm font-semibold hover:bg-red-100"
                            >
                                Hapus
                            </button>
                        </form>

                    </div>

                </div>

            </div>

        @empty

            <div class="p-10 text-center">

                <div class="text-5xl mb-4">🎬</div>

                <h3 class="font-bold text-gray-800 text-lg">
                    Belum Ada Video Tutorial
                </h3>

                <p class="text-sm text-gray-500 mt-2 mb-5">
                    Tambahkan video YouTube atau Google Drive untuk panduan pengguna Kasir½M.
                </p>

                <a
                    href="{{ route('admin-kasirku.tutorial-videos.create') }}"
                    class="inline-flex px-5 py-3 rounded-xl bg-emerald-600 text-white font-semibold hover:bg-emerald-700"
                >
                    Tambah Video Pertama
                </a>

            </div>

        @endforelse

    </div>

    {{-- PAGINATION --}}
    <div class="mt-5">
        {{ $videos->links() }}
    </div>

</div>
@endsection