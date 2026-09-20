@extends('layouts.app')

@section('title', 'Semua Aktivitas')

@section('content')

<div class="min-h-screen bg-gray-900 text-white">

    {{-- HEADER --}}
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

            <div>
                <h1 class="text-2xl font-bold">
                    📋 Semua Aktivitas
                </h1>

                <p class="text-sm text-gray-400 mt-1">
                    Riwayat aktivitas seluruh platform KasirKU
                </p>
            </div>

            <div class="text-sm text-gray-400">
                {{ $logs->total() }} aktivitas
            </div>

        </div>
    </div>


    {{-- FILTER --}}
    <div class="bg-gray-800/80 border border-gray-700 rounded-2xl p-4 mb-6">

        <form method="GET"
              action="{{ route('admin-kasirku.audit-log.index') }}">

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">

                {{-- SEARCH --}}
                <div class="lg:col-span-2">

                    <label class="block text-xs text-gray-400 mb-1">
                        Cari aktivitas
                    </label>

                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari aktivitas, user, email..."
                        class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 py-2.5 text-sm text-white placeholder-gray-500 focus:outline-none focus:border-emerald-500"
                    >

                </div>


                {{-- OWNER --}}
                <div>

                    <label class="block text-xs text-gray-400 mb-1">
                        Owner
                    </label>

                    <select
                        name="owner_id"
                        class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 py-2.5 text-sm text-white focus:outline-none focus:border-emerald-500"
                    >

                        <option value="">Semua Owner</option>

                        @foreach($owners as $owner)

                            <option
                                value="{{ $owner->id }}"
                                @selected(request('owner_id') == $owner->id)
                            >
                                {{ $owner->name }}
                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- STORE --}}
                <div>

                    <label class="block text-xs text-gray-400 mb-1">
                        Toko
                    </label>

                    <select
                        name="store_id"
                        class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 py-2.5 text-sm text-white focus:outline-none focus:border-emerald-500"
                    >

                        <option value="">Semua Toko</option>

                        @foreach($stores as $store)

                            <option
                                value="{{ $store->id }}"
                                @selected(request('store_id') == $store->id)
                            >
                                {{ $store->name }}
                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- ACTION --}}
                <div>

                    <label class="block text-xs text-gray-400 mb-1">
                        Aktivitas
                    </label>

                    <select
                        name="action"
                        class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 py-2.5 text-sm text-white focus:outline-none focus:border-emerald-500"
                    >

                        <option value="">Semua Aktivitas</option>

                        @foreach($actions as $action)

                            <option
                                value="{{ $action }}"
                                @selected(request('action') == $action)
                            >
                                {{ ucfirst(str_replace('_', ' ', $action)) }}
                            </option>

                        @endforeach

                    </select>

                </div>

            </div>


            {{-- DATE + BUTTON --}}
            <div class="mt-3 flex flex-col sm:flex-row gap-3">

                <div class="sm:w-52">

                    <label class="block text-xs text-gray-400 mb-1">
                        Tanggal
                    </label>

                    <input
                        type="date"
                        name="date"
                        value="{{ request('date') }}"
                        class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 py-2.5 text-sm text-white focus:outline-none focus:border-emerald-500"
                    >

                </div>


                <div class="flex items-end gap-2">

                    <button
                        type="submit"
                        class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-sm font-semibold transition"
                    >
                        🔎 Cari
                    </button>

                    <a
                        href="{{ route('admin-kasirku.audit-log.index') }}"
                        class="px-4 py-2.5 rounded-xl bg-gray-700 hover:bg-gray-600 text-sm font-semibold transition"
                    >
                        Reset
                    </a>

                </div>

            </div>

        </form>

    </div>


    {{-- AKTIVITAS --}}
    <div class="bg-gray-800/80 border border-gray-700 rounded-2xl overflow-hidden">

        {{-- DESKTOP TABLE --}}
        <div class="hidden md:block overflow-x-auto">

            <table class="w-full text-sm">

                <thead class="bg-gray-900/70 border-b border-gray-700">

                    <tr class="text-left text-gray-400">

                        <th class="px-5 py-4 font-medium">
                            Waktu
                        </th>

                        <th class="px-5 py-4 font-medium">
                            Pelaku
                        </th>

                        <th class="px-5 py-4 font-medium">
                            Aktivitas
                        </th>

                        <th class="px-5 py-4 font-medium">
                            Owner
                        </th>

                        <th class="px-5 py-4 font-medium">
                            Toko
                        </th>

                    </tr>

                </thead>

                <tbody class="divide-y divide-gray-700">

                    @forelse($logs as $log)

                        <tr class="hover:bg-gray-700/30 transition">

                            {{-- WAKTU --}}
                            <td class="px-5 py-4 whitespace-nowrap">

                                <div class="text-white">
                                    {{ $log->created_at->format('d M Y') }}
                                </div>

                                <div class="text-xs text-gray-500 mt-1">
                                    {{ $log->created_at->format('H:i') }} WIB
                                </div>

                            </td>


                            {{-- PELAKU --}}
                            <td class="px-5 py-4">

                                <div class="flex items-center gap-3">

                                    <div class="w-9 h-9 rounded-full bg-emerald-600/20 text-emerald-400 flex items-center justify-center font-bold shrink-0">

                                        {{ strtoupper(substr($log->user->name ?? 'S', 0, 1)) }}

                                    </div>

                                    <div class="min-w-0">

                                        <div class="font-medium text-white truncate">
                                            {{ $log->user->name ?? 'Sistem' }}
                                        </div>

                                        @if($log->user)
                                            <div class="text-xs text-gray-500 truncate">
                                                {{ $log->user->email }}
                                            </div>
                                        @endif

                                    </div>

                                </div>

                            </td>


                            {{-- AKTIVITAS --}}
                            <td class="px-5 py-4">

                                <div class="inline-flex px-2.5 py-1 rounded-lg bg-indigo-500/10 text-indigo-400 text-xs font-semibold mb-2">

                                    {{ ucfirst(str_replace('_', ' ', $log->action)) }}

                                </div>

                                <div class="text-gray-300 max-w-md">
                                    {{ $log->description }}
                                </div>

                            </td>


                            {{-- OWNER --}}
                            <td class="px-5 py-4">

                                @if($log->owner)

                                    <div class="text-white">
                                        {{ $log->owner->name }}
                                    </div>

                                    <div class="text-xs text-gray-500">
                                        {{ $log->owner->email }}
                                    </div>

                                @else

                                    <span class="text-gray-500">
                                        —
                                    </span>

                                @endif

                            </td>


                            {{-- TOKO --}}
                            <td class="px-5 py-4">

                                @if($log->store)

                                    <div class="text-white">
                                        🏪 {{ $log->store->name }}
                                    </div>

                                    <div class="text-xs text-gray-500">
                                        ID #{{ $log->store->id }}
                                    </div>

                                @else

                                    <span class="text-gray-500">
                                        Platform
                                    </span>

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="5" class="px-5 py-16 text-center">

                                <div class="text-5xl mb-4">
                                    📋
                                </div>

                                <h3 class="text-lg font-semibold text-white">
                                    Belum ada aktivitas
                                </h3>

                                <p class="text-sm text-gray-500 mt-1 max-w-md mx-auto">
                                    Aktivitas penting pengguna dan platform
                                    akan muncul di halaman ini setelah sistem
                                    audit mulai mencatat aktivitas.
                                </p>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- MOBILE --}}
        <div class="md:hidden divide-y divide-gray-700">

            @forelse($logs as $log)

                <div class="p-4">

                    <div class="flex items-start gap-3">

                        <div class="w-9 h-9 rounded-full bg-emerald-600/20 text-emerald-400 flex items-center justify-center font-bold shrink-0">

                            {{ strtoupper(substr($log->user->name ?? 'S', 0, 1)) }}

                        </div>

                        <div class="flex-1 min-w-0">

                            <div class="flex items-start justify-between gap-2">

                                <div class="font-semibold text-white">
                                    {{ $log->user->name ?? 'Sistem' }}
                                </div>

                                <div class="text-xs text-gray-500 whitespace-nowrap">
                                    {{ $log->created_at->format('d/m H:i') }}
                                </div>

                            </div>


                            <div class="mt-2">

                                <span class="inline-flex px-2.5 py-1 rounded-lg bg-indigo-500/10 text-indigo-400 text-xs font-semibold">

                                    {{ ucfirst(str_replace('_', ' ', $log->action)) }}

                                </span>

                            </div>


                            <p class="text-sm text-gray-300 mt-2">
                                {{ $log->description }}
                            </p>


                            <div class="flex flex-wrap gap-2 mt-3 text-xs">

                                @if($log->owner)

                                    <span class="px-2 py-1 rounded-lg bg-gray-700 text-gray-300">
                                        👤 {{ $log->owner->name }}
                                    </span>

                                @endif

                                @if($log->store)

                                    <span class="px-2 py-1 rounded-lg bg-gray-700 text-gray-300">
                                        🏪 {{ $log->store->name }}
                                    </span>

                                @else

                                    <span class="px-2 py-1 rounded-lg bg-gray-700 text-gray-400">
                                        🖥️ Platform
                                    </span>

                                @endif

                            </div>

                        </div>

                    </div>

                </div>

            @empty

                <div class="p-12 text-center">

                    <div class="text-5xl mb-4">
                        📋
                    </div>

                    <h3 class="text-lg font-semibold">
                        Belum ada aktivitas
                    </h3>

                    <p class="text-sm text-gray-500 mt-1">
                        Belum ada aktivitas yang tercatat.
                    </p>

                </div>

            @endforelse

        </div>

    </div>


    {{-- PAGINATION --}}
    @if($logs->hasPages())

        <div class="mt-5">
            {{ $logs->links() }}
        </div>

    @endif

</div>

@endsection