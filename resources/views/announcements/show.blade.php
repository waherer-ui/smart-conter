@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-5">

    {{-- NAVIGASI KEMBALI --}}
    <div class="mb-5">
        <a href="{{ route('notifications.index') }}"
           class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-emerald-600 transition">
            <span>←</span>
            Kembali ke Notifikasi
        </a>
    </div>

    {{-- KARTU PENGUMUMAN --}}
    <article class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">

        {{-- HEADER --}}
        <div class="bg-gradient-to-br from-emerald-600 to-emerald-700 px-5 py-7 sm:px-8 sm:py-9 text-white">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-12 h-12 rounded-2xl bg-white/15 flex items-center justify-center text-2xl">
                    📢
                </div>

                <div>
                    <p class="text-xs uppercase tracking-wider text-emerald-100">
                        Informasi Resmi
                    </p>
                    <p class="text-sm font-semibold">
                        Kasir½M
                    </p>
                </div>
            </div>

            <h1 class="text-2xl sm:text-3xl font-bold leading-tight break-words">
                {{ $announcement->title }}
            </h1>

            <div class="flex flex-wrap items-center gap-x-4 gap-y-2 mt-4 text-xs text-emerald-50">
                <span>
                    📅
                    {{ $announcement->published_at?->translatedFormat('d F Y, H:i') ?? '-' }}
                </span>

                <span>
                    ✓ Pengumuman diterbitkan
                </span>
            </div>
        </div>

        {{-- ISI --}}
        <div class="px-5 py-6 sm:px-8 sm:py-8">

            <div class="flex items-start gap-3 mb-6 rounded-xl border border-emerald-100 bg-emerald-50 p-4">
                <span class="text-xl">ℹ️</span>

                <div>
                    <p class="text-sm font-semibold text-emerald-800">
                        Informasi untuk pengguna
                    </p>

                    <p class="text-xs leading-5 text-emerald-700 mt-1">
                        Baca informasi berikut dengan saksama.
                        Jika diperlukan, simpan informasi penting untuk referensi kamu.
                    </p>
                </div>
            </div>

            <div class="text-sm sm:text-base text-gray-700 leading-7 whitespace-pre-line break-words">{{ $announcement->content }}</div>

            <div class="border-t border-gray-100 mt-8 pt-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-xl">
                        💚
                    </div>

                    <div>
                        <p class="text-sm font-bold text-gray-800">
                            Kasir½M
                        </p>

                        <p class="text-xs text-gray-500">
                            Solusi kasir untuk usaha Anda.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </article>
@endsection