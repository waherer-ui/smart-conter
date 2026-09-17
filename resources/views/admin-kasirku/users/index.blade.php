@extends('layouts.app')

@section('title', 'Pengguna KasirKU')
@section('header', 'Pengguna')

@section('content')

<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

        <div>
            <h1 class="text-2xl font-bold text-white">
                Pengguna KasirKU
            </h1>

            <p class="text-sm text-gray-400 mt-1">
                Kelola dan pantau seluruh pengguna yang terdaftar di platform KasirKU.
            </p>
        </div>

        <a
            href="{{ route('admin-kasirku.dashboard') }}"
            class="inline-flex items-center justify-center gap-2
                   px-4 py-2 rounded-xl
                   bg-gray-700 hover:bg-gray-600
                   text-white text-sm font-medium transition"
        >
            ← Dashboard
        </a>

    </div>


    {{-- TABEL --}}
    <div class="bg-gray-800/80 border border-white/10 rounded-2xl shadow-xl overflow-hidden">

        <div class="overflow-x-auto">

            <table class="w-full text-sm text-left">

                <thead class="bg-gray-900/70 border-b border-white/10">
                    <tr>

                        <th class="px-5 py-4 text-gray-400 font-medium">
                            Pengguna
                        </th>

                        <th class="px-5 py-4 text-gray-400 font-medium">
                            Email
                        </th>

                        <th class="px-5 py-4 text-gray-400 font-medium">
                            Role
                        </th>

                        <th class="px-5 py-4 text-gray-400 font-medium">
                            Toko
                        </th>

                        <th class="px-5 py-4 text-gray-400 font-medium">
                            Terdaftar
                        </th>

                    </tr>
                </thead>

                <tbody class="divide-y divide-white/5">

                    @forelse($users as $user)

                        <tr class="hover:bg-white/5 transition">

                            {{-- PENGGUNA --}}
                            <td class="px-5 py-4">

                                <div class="flex items-center gap-3">

                                    <div class="w-9 h-9 rounded-full
                                                bg-emerald-600/20
                                                border border-emerald-500/20
                                                flex items-center justify-center
                                                text-emerald-400 font-semibold">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>

                                    <div>
                                        <p class="text-white font-medium">
                                            {{ $user->name }}
                                        </p>

                                        @if($user->is_platform_admin)
                                            <span class="text-xs text-emerald-400">
                                                Admin Platform
                                            </span>
                                        @endif
                                    </div>

                                </div>

                            </td>


                            {{-- EMAIL --}}
                            <td class="px-5 py-4 text-gray-300">
                                {{ $user->email }}
                            </td>


                            {{-- ROLE --}}
                            <td class="px-5 py-4">

                                @if($user->is_platform_admin)

                                    <span class="inline-flex px-2.5 py-1 rounded-lg
                                                 bg-emerald-500/10
                                                 text-emerald-400 text-xs font-medium">
                                        Platform Admin
                                    </span>

                                @else

                                    <span class="inline-flex px-2.5 py-1 rounded-lg
                                                 bg-gray-700
                                                 text-gray-300 text-xs font-medium">
                                        {{ ucfirst($user->role) }}
                                    </span>

                                @endif

                            </td>


                            {{-- TOKO --}}
                            <td class="px-5 py-4">

                                @if($user->stores->count())

                                    <div class="space-y-1">

                                        @foreach($user->stores as $store)

                                            <div class="text-gray-300">
                                                🏪 {{ $store->name }}
                                            </div>

                                        @endforeach

                                    </div>

                                @else

                                    <span class="text-gray-500">
                                        Tidak ada toko
                                    </span>

                                @endif

                            </td>


                            {{-- TANGGAL --}}
                            <td class="px-5 py-4 text-gray-400 whitespace-nowrap">

                                {{ $user->created_at?->format('d M Y') }}

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="5"
                                class="px-5 py-10 text-center text-gray-500"
                            >
                                Belum ada pengguna.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- PAGINATION --}}
        @if($users->hasPages())

            <div class="px-5 py-4 border-t border-white/10">
                {{ $users->links() }}
            </div>

        @endif

    </div>

</div>

@endsection