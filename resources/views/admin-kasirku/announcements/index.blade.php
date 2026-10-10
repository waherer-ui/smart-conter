@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-5 space-y-5">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                📢 Pengumuman Kasir½M
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                Kelola informasi dan pengumuman untuk pengguna Kasir½M.
            </p>
        </div>

        <a href="{{ route('admin-kasirku.announcements.create') }}"
           class="inline-flex items-center justify-center gap-2 px-4 py-3
                  bg-emerald-600 hover:bg-emerald-700 text-white
                  rounded-xl font-semibold text-sm shadow-sm transition">
            <span class="text-lg">＋</span>
            Buat Pengumuman
        </a>
    </div>

    {{-- FLASH MESSAGE --}}
    @if(session('success'))
        <div class="p-4 rounded-xl border border-emerald-200 bg-emerald-50
                    text-emerald-800 text-sm">
            ✅ {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-xl border border-red-200 bg-red-50
                    text-red-700 text-sm">
            ⚠️ {{ session('error') }}
        </div>
    @endif

    @if(session('info'))
        <div class="p-4 rounded-xl border border-blue-200 bg-blue-50
                    text-blue-700 text-sm">
            ℹ️ {{ session('info') }}
        </div>
    @endif

    {{-- SUMMARY --}}
    @php
        $total = $announcements->total();
    @endphp

    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
        <div class="bg-white border border-gray-100 rounded-2xl p-4 shadow-sm">
            <p class="text-xs text-gray-500">Total ditampilkan</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">
                {{ $total }}
            </p>
        </div>

        <div class="bg-white border border-gray-100 rounded-2xl p-4 shadow-sm">
            <p class="text-xs text-gray-500">Halaman</p>
            <p class="text-2xl font-bold text-emerald-600 mt-1">
                {{ $announcements->currentPage() }}
                <span class="text-sm font-normal text-gray-400">
                    / {{ max(1, $announcements->lastPage()) }}
                </span>
            </p>
        </div>

        <div class="col-span-2 sm:col-span-1 bg-white border border-gray-100
                    rounded-2xl p-4 shadow-sm">
            <p class="text-xs text-gray-500">Informasi</p>
            <p class="text-sm font-semibold text-gray-700 mt-2">
                Kelola informasi pengguna
            </p>
        </div>
    </div>

    {{-- ANNOUNCEMENT LIST --}}
    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">

        <div class="px-4 sm:px-5 py-4 border-b border-gray-100">
            <h2 class="font-bold text-gray-800">Daftar Pengumuman</h2>
            <p class="text-xs text-gray-500 mt-1">
                Periksa status sebelum menerbitkan pengumuman.
            </p>
        </div>

        @forelse($announcements as $announcement)
            <div class="p-4 sm:p-5 border-b border-gray-100 last:border-b-0">

                <div class="flex flex-col sm:flex-row sm:items-start
                            sm:justify-between gap-3">

                    <div class="min-w-0 flex-1">

                        <div class="flex flex-wrap items-center gap-2 mb-2">
                            @if($announcement->status === 'published')
                                <span class="px-2.5 py-1 rounded-full text-xs
                                             font-semibold bg-emerald-100 text-emerald-700">
                                    ● Diterbitkan
                                </span>
                            @elseif($announcement->status === 'archived')
                                <span class="px-2.5 py-1 rounded-full text-xs
                                             font-semibold bg-gray-100 text-gray-600">
                                    Diarsipkan
                                </span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-xs
                                             font-semibold bg-amber-100 text-amber-700">
                                    Draf
                                </span>
                            @endif

                            @php
                                $audienceLabels = [
                                    'all' => 'Semua Pengguna',
                                    'owners' => 'Pemilik Toko',
                                    'free' => 'Paket Free',
                                    'pro' => 'Paket Pro',
                                    'premium' => 'Paket Premium',
                                ];
                            @endphp

                            <span class="px-2.5 py-1 rounded-full text-xs
                                         font-medium bg-blue-50 text-blue-700">
                                {{ $audienceLabels[$announcement->target_audience] ?? $announcement->target_audience }}
                            </span>
                        </div>

                        <h3 class="font-bold text-gray-800 text-base break-words">
                            {{ $announcement->title }}
                        </h3>

                        <p class="text-sm text-gray-600 mt-2 whitespace-pre-line break-words">{{ \Illuminate\Support\Str::limit(strip_tags($announcement->content), 180) }}</p>

                        <div class="flex flex-wrap gap-x-4 gap-y-1 mt-3
                                    text-xs text-gray-400">
                            <span>
                                Dibuat:
                                {{ $announcement->created_at?->format('d M Y, H:i') ?? '-' }}
                            </span>

                            <span>
                                Oleh:
                                {{ $announcement->creator?->username
                                    ?? $announcement->creator?->name
                                    ?? 'Admin Kasir½M' }}
                            </span>

                            @if($announcement->published_at)
                                <span>
                                    Terbit:
                                    {{ $announcement->published_at->format('d M Y, H:i') }}
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- ACTIONS --}}
                    <div class="flex flex-wrap sm:flex-col gap-2 sm:min-w-32">

                        @if($announcement->status !== 'archived')
                            <a href="{{ route('admin-kasirku.announcements.edit', $announcement) }}"
                               class="flex-1 sm:flex-none text-center px-3 py-2
                                      rounded-lg border border-gray-200
                                      text-gray-700 hover:bg-gray-50
                                      text-xs font-semibold">
                                ✏️ Edit
                            </a>
                        @endif

                        @if($announcement->status === 'draft')
                            <form method="POST"
                                  action="{{ route('admin-kasirku.announcements.publish', $announcement) }}"
                                  onsubmit="return confirm('Terbitkan pengumuman ini sekarang? Notifikasi akan dikirim kepada target penerima.');">
                                @csrf
                                <button type="submit"
                                        class="w-full px-3 py-2 rounded-lg
                                               bg-emerald-600 hover:bg-emerald-700
                                               text-white text-xs font-semibold">
                                    📢 Terbitkan
                                </button>
                            </form>
                        @endif

                        @if($announcement->status !== 'archived')
                            <form method="POST"
                                  action="{{ route('admin-kasirku.announcements.archive', $announcement) }}"
                                  onsubmit="return confirm('Arsipkan pengumuman ini?');">
                                @csrf
                                <button type="submit"
                                        class="w-full px-3 py-2 rounded-lg
                                               border border-red-200 text-red-600
                                               hover:bg-red-50 text-xs font-semibold">
                                    🗄️ Arsipkan
                                </button>
                            </form>
                        @endif

                    </div>
                </div>
            </div>
        @empty
            <div class="px-5 py-14 text-center">
                <div class="text-5xl mb-4">📢</div>
                <h3 class="font-bold text-gray-800">
                    Belum Ada Pengumuman
                </h3>
                <p class="text-sm text-gray-500 mt-2 max-w-sm mx-auto">
                    Buat pengumuman pertama untuk menyampaikan informasi,
                    pembaruan fitur, atau pemberitahuan penting kepada pengguna.
                </p>
                <a href="{{ route('admin-kasirku.announcements.create') }}"
                   class="inline-flex mt-5 px-4 py-2.5 rounded-xl
                          bg-emerald-600 text-white text-sm font-semibold
                          hover:bg-emerald-700">
                    ＋ Buat Pengumuman Pertama
                </a>
            </div>
        @endforelse

    </div>

    {{-- PAGINATION --}}
    @if($announcements->hasPages())
        <div class="pt-2">
            {{ $announcements->links() }}
        </div>
    @endif

</div>
@endsection