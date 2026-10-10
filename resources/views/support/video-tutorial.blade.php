@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6 text-white">

    {{-- Header --}}
    <div class="mb-6">
        <a
            href="{{ route('support.index') }}"
            class="inline-flex items-center gap-2 text-sm text-emerald-400 hover:text-emerald-300 mb-4"
        >
            ← Kembali ke Pusat Bantuan
        </a>

        <h1 class="text-2xl font-bold">
            🎬 Video Tutorial Kasir½M
        </h1>

        <p class="text-sm text-gray-400 mt-2">
            Pelajari cara menggunakan Kasir½M melalui panduan video.
        </p>
    </div>

{{-- =========================================================
KATEGORI VIDEO TUTORIAL
========================================================== --}}

<section class="dashboard-category mb-4"><div class="category-scroll">

    {{-- SEMUA --}}
    <a
        href="{{ route('support.videos', array_filter([
            'search' => request('search'),
        ])) }}"
        class="category-chip {{ (!$category || $category === 'all') ? 'active' : '' }}"
    >
        Semua
    </a>

    {{-- KATEGORI DARI DATABASE --}}
    @foreach($categories as $cat)

        <a
            href="{{ route('support.videos', array_filter([
                'search' => request('search'),
                'kategori' => $cat,
            ])) }}"
            class="category-chip {{ $category === $cat ? 'active' : '' }}"
        >
            {{ $cat }}
        </a>

    @endforeach

</div>

</section>

    {{-- Daftar video --}}
    @if($videos->isNotEmpty())

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">

            @foreach($videos as $video)

                @php
                    $url = $video->video_url;
                    $host = strtolower(parse_url($url, PHP_URL_HOST) ?? '');
                    $path = parse_url($url, PHP_URL_PATH) ?? '';
                    $embedUrl = null;

                    if (in_array($host, [
                        'youtube.com',
                        'www.youtube.com',
                        'm.youtube.com',
                        'youtu.be',
                    ], true)) {
                        parse_str(
                            parse_url($url, PHP_URL_QUERY) ?? '',
                            $query
                        );

                        if ($host === 'youtu.be') {
                            $videoId = trim($path, '/');
                        } elseif (!empty($query['v'])) {
                            $videoId = $query['v'];
                        } elseif (preg_match(
                            '~/(?:embed|shorts|live)/([a-zA-Z0-9_-]+)~',
                            $path,
                            $matches
                        )) {
                            $videoId = $matches[1];
                        } else {
                            $videoId = null;
                        }

                        if (
                            $videoId &&
                            preg_match('/^[a-zA-Z0-9_-]{11}$/', $videoId)
                        ) {
                            $embedUrl =
                                'https://www.youtube-nocookie.com/embed/' .
                                $videoId;
                        }
                    } elseif (in_array($host, [
                        'drive.google.com',
                        'docs.google.com',
                    ], true)) {
                        if (preg_match(
                            '~/file/d/([a-zA-Z0-9_-]+)~',
                            $path,
                            $matches
                        )) {
                            $embedUrl =
                                'https://drive.google.com/file/d/' .
                                $matches[1] .
                                '/preview';
                        } else {
                            parse_str(
                                parse_url($url, PHP_URL_QUERY) ?? '',
                                $query
                            );

                            if (
                                !empty($query['id']) &&
                                preg_match(
                                    '/^[a-zA-Z0-9_-]+$/',
                                    $query['id']
                                )
                            ) {
                                $embedUrl =
                                    'https://drive.google.com/file/d/' .
                                    $query['id'] .
                                    '/preview';
                            }
                        }
                    }
                @endphp

                <article class="overflow-hidden rounded-2xl border border-gray-700 bg-gray-800/80">

                    {{-- Pemutar video --}}
                    @if($embedUrl)

                        <div class="aspect-video bg-black">
                            <iframe
                                src="{{ $embedUrl }}"
                                title="{{ $video->title }}"
                                class="h-full w-full"
                                loading="lazy"
                                referrerpolicy="strict-origin-when-cross-origin"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen
                            ></iframe>
                        </div>

                    @elseif($video->thumbnail_url)

                        <a
                            href="{{ $video->video_url }}"
                            target="_blank"
                            rel="noopener noreferrer"
                        >
                            <img
                                src="{{ $video->thumbnail_url }}"
                                alt="{{ $video->title }}"
                                class="aspect-video w-full object-cover"
                                loading="lazy"
                            >
                        </a>

                    @else

                        <a
                            href="{{ $video->video_url }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="flex aspect-video items-center justify-center bg-gray-900 text-emerald-400"
                        >
                            <div class="text-center p-4">
                                <div class="text-4xl mb-2">▶️</div>
                                <p class="text-sm font-semibold">
                                    Buka Video Tutorial
                                </p>
                            </div>
                        </a>

                    @endif

                    {{-- Detail video --}}
                    <div class="p-4">

                        <span class="inline-block rounded-full bg-emerald-500/10 px-3 py-1 text-xs font-medium text-emerald-400">
                            {{ $video->category }}
                        </span>

                        <h2 class="mt-3 text-base font-bold">
                            {{ $video->title }}
                        </h2>

                        @if($video->description)
                            <p class="mt-2 text-sm leading-6 text-gray-400">
                                {{ $video->description }}
                            </p>
                        @endif

                        <a
                            href="{{ $video->video_url }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-emerald-400 hover:text-emerald-300"
                        >
                            Buka di
                            {{ $video->video_provider === 'youtube' ? 'YouTube' : 'Google Drive' }}
                            ↗
                        </a>

                    </div>
                </article>

            @endforeach

        </div>

    @else

        <div class="rounded-2xl border border-gray-700 bg-gray-800/60 p-8 text-center">

            <div class="text-4xl mb-3">🎬</div>

            <h2 class="text-lg font-bold">
                Belum Ada Video Tutorial
            </h2>

            <p class="mt-2 text-sm text-gray-400">
                Video tutorial untuk kategori ini belum tersedia.
                Silakan periksa kembali nanti.
            </p>

        </div>

    @endif

</div>

<style>
  .dashboard-category {
    width: 100%;
    margin-bottom: 1rem;
}

.category-scroll {
    display: flex;
    gap: 0.6rem;
    overflow-x: auto;
    padding: 0.25rem 0 0.75rem;
    scrollbar-width: none;
    -webkit-overflow-scrolling: touch;
}

.category-scroll::-webkit-scrollbar {
    display: none;
}

.category-chip {
    flex: 0 0 auto;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.6rem 1rem;
    border: 1px solid #e5e7eb;
    border-radius: 999px;
    background: #fff;
    color: #374151;
    font-size: 0.875rem;
    font-weight: 600;
    text-decoration: none;
    white-space: nowrap;
    transition: all 0.2s ease;
}

.category-chip:hover {
    border-color: #059669;
    color: #059669;
}

.category-chip.active {
    border-color: #059669;
    background: #059669;
    color: #fff;
}
  
</style>
@endsection