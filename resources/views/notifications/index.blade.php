@extends('layouts.app')

@section('content')

<div class="mx-auto max-w-5xl px-4 py-6 sm:px-6">{{-- HEADER --}}
<div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-white">
            Notifikasi
        </h1>

        <p class="mt-1 text-sm text-gray-400">
            Informasi penting dan aktivitas akun Kasir½M.
        </p>
    </div>

    @if($unreadCount > 0)
        <form
            action="{{ route('notifications.read-all') }}"
            method="POST"
        >
            @csrf

            <button
                type="submit"
                class="rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-2 text-sm font-semibold text-emerald-300 transition hover:bg-emerald-500/20"
            >
                Tandai semua dibaca
            </button>
        </form>
    @endif
</div>

{{-- RINGKASAN --}}
<div class="mb-5 rounded-2xl border border-white/10 bg-gray-900/70 p-4">
    <div class="flex items-center justify-between gap-4">
        <span class="text-sm text-gray-400">
            Belum dibaca
        </span>

        <span class="rounded-full bg-emerald-500/15 px-3 py-1 text-sm font-bold text-emerald-300">
            {{ $unreadCount }}
        </span>
    </div>
</div>

{{-- DAFTAR NOTIFIKASI --}}
<div class="space-y-3">

    @forelse($notifications as $notification)

        <div class="rounded-2xl border p-4 transition
            {{ $notification->read_at
                ? 'border-white/10 bg-gray-900/50'
                : 'border-emerald-500/30 bg-emerald-500/[0.06]' }}">

            <div class="flex items-start gap-3">

                {{-- IKON --}}
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl
                    {{ $notification->read_at
                        ? 'bg-gray-700/70 text-gray-300'
                        : 'bg-emerald-500/15 text-emerald-300' }}">

                    @if($notification->type === 'support_reply')
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="1.8">
                            <path stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M8 10h8m-8 4h5m-9 6 3.5-3H17a4 4 0 0 0 4-4V7a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v6a4 4 0 0 0 1.5 3.2L4 20z"/>
                        </svg>
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="1.8">
                            <path stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 0 0-12 0v3.2a2 2 0 0 1-.6 1.4L4 17h5m2 4a2 2 0 0 0 2-2h-4a2 2 0 0 0 2 2z"/>
                        </svg>
                    @endif

                </div>

                {{-- ISI --}}
                <div class="min-w-0 flex-1">

                    <div class="flex flex-wrap items-start justify-between gap-2">

                        <h2 class="font-semibold
                            {{ $notification->read_at
                                ? 'text-gray-200'
                                : 'text-white' }}">
                            {{ $notification->title }}
                        </h2>

                        @unless($notification->read_at)
                            <span class="inline-flex items-center gap-1.5 text-xs font-medium text-emerald-300">
                                <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                                Baru
                            </span>
                        @endunless

                    </div>

                    <p class="mt-1 whitespace-pre-line break-words text-sm leading-6 text-gray-400">
                        {{ $notification->message }}
                    </p>

                    <div class="mt-3 flex flex-wrap items-center justify-between gap-3">

                        <time class="text-xs text-gray-500">
                            {{ $notification->created_at->diffForHumans() }}
                        </time>

                        @if($notification->url)
                            <form
                                action="{{ route('notifications.read', $notification) }}"
                                method="POST"
                            >
                                @csrf

                                <button
                                    type="submit"
                                    class="inline-flex items-center gap-1 text-sm font-semibold text-emerald-300 transition hover:text-emerald-200"
                                >
                                    Lihat detail

                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="h-4 w-4"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="2">
                                        <path stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M9 5l7 7-7 7"/>
                                    </svg>
                                </button>
                            </form>
                        @endif

                    </div>

                </div>
            </div>
        </div>

    @empty

        <div class="rounded-2xl border border-dashed border-white/15 bg-gray-900/40 px-5 py-14 text-center">

            <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-gray-800 text-gray-400">
                <svg xmlns="http://www.w3.org/2000/svg"
                    class="h-7 w-7"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="1.7">
                    <path stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 0 0-12 0v3.2a2 2 0 0 1-.6 1.4L4 17H9m2 4a2 2 0 0 0 4 0"/>
                </svg>
            </div>

            <h2 class="text-lg font-semibold text-white">
                Belum ada notifikasi
            </h2>

            <p class="mt-2 text-sm text-gray-400">
                Informasi penting akan muncul di sini.
            </p>

        </div>

    @endforelse

</div>

{{-- PAGINATION --}}
@if($notifications->hasPages())
    <div class="mt-6">
        {{ $notifications->links() }}
    </div>
@endif

</div>
@endsection