@extends('layouts.app')

@section('header', '📋 Riwayat Absensi')

@section('content')

<div class="max-w-6xl mx-auto px-4 py-4">

    {{-- INFORMASI TOKO --}}
    <div class="rounded-2xl border border-white/10 bg-gray-800 p-4 mb-4">

        <p class="text-xs text-gray-400">
            Toko
        </p>

        <h2 class="text-lg font-semibold text-white">
            {{ $store->name }}
        </h2>

    </div>


    {{-- FILTER --}}
    <div class="rounded-2xl border border-white/10 bg-gray-800 p-4 mb-4">

        <form method="GET" action="{{ route('attendance.history') }}">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">

                {{-- DARI --}}
                <div>
                    <label class="block text-xs text-gray-400 mb-1">
                        Dari tanggal
                    </label>

                    <input
                        type="date"
                        name="date_from"
                        value="{{ $dateFrom }}"
                        class="w-full rounded-xl bg-gray-900
                               border border-white/10
                               text-white px-3 py-2
                               text-sm"
                    >
                </div>


                {{-- SAMPAI --}}
                <div>
                    <label class="block text-xs text-gray-400 mb-1">
                        Sampai tanggal
                    </label>

                    <input
                        type="date"
                        name="date_to"
                        value="{{ $dateTo }}"
                        class="w-full rounded-xl bg-gray-900
                               border border-white/10
                               text-white px-3 py-2
                               text-sm"
                    >
                </div>


                {{-- STAF --}}
                <div>
                    <label class="block text-xs text-gray-400 mb-1">
                        Staf
                    </label>

                    <select
                        name="staff_id"
                        class="w-full rounded-xl bg-gray-900
                               border border-white/10
                               text-white px-3 py-2
                               text-sm"
                    >

                        <option value="">
                            Semua staf
                        </option>

                        @foreach($staff as $member)

                            <option
                                value="{{ $member->id }}"
                                @selected($staffId == $member->id)
                            >
                                {{ $member->name ?? $member->username }}
                            </option>

                        @endforeach

                    </select>
                </div>

            </div>


            <button
                type="submit"
                class="mt-3 w-full md:w-auto
                       rounded-xl bg-emerald-600
                       hover:bg-emerald-500
                       text-white font-semibold
                       px-5 py-2.5 transition"
            >
                🔎 Tampilkan
            </button>

        </form>

    </div>


    {{-- DATA --}}
    <div class="rounded-2xl border border-white/10 bg-gray-800 overflow-hidden">

        <div class="p-4 border-b border-white/10">

            <h3 class="font-semibold text-white">
                📋 Data Absensi
            </h3>

            <p class="text-xs text-gray-400 mt-1">
                {{ $attendances->total() }} data ditemukan
            </p>

        </div>


        {{-- MOBILE CARD --}}
        <div class="md:hidden divide-y divide-white/10">

            @forelse($attendances as $attendance)

                <div class="p-4">

                    <div class="flex items-start justify-between gap-3">

                        <div>
                            <p class="font-semibold text-white">
                                {{ $attendance->user->name ?? $attendance->user->username ?? '-' }}
                            </p>

                            <p class="text-xs text-gray-400 mt-1">
                                {{ $attendance->date?->translatedFormat('d F Y') }}
                            </p>
                        </div>


                        @if($attendance->status === 'hadir')
                            <span class="rounded-full
                                         bg-emerald-500/15
                                         text-emerald-400
                                         px-2 py-1 text-xs">
                                Hadir
                            </span>
                        @else
                            <span class="rounded-full
                                         bg-gray-500/15
                                         text-gray-400
                                         px-2 py-1 text-xs">
                                {{ ucfirst($attendance->status ?? '-') }}
                            </span>
                        @endif

                    </div>


                    <div class="grid grid-cols-2 gap-2 mt-3">

                        <div class="rounded-xl bg-gray-900/70 p-3">

                            <p class="text-xs text-gray-500">
                                Check-in
                            </p>

                            <p class="text-sm font-semibold text-emerald-400 mt-1">
                                {{ $attendance->check_in?->format('H:i') ?? '-' }}
                            </p>

                        </div>


                        <div class="rounded-xl bg-gray-900/70 p-3">

                            <p class="text-xs text-gray-500">
                                Check-out
                            </p>

                            <p class="text-sm font-semibold text-white mt-1">
                                {{ $attendance->check_out?->format('H:i') ?? '-' }}
                            </p>

                        </div>

                    </div>

                </div>

            @empty

                <div class="p-8 text-center text-gray-400">
                    Belum ada data absensi.
                </div>

            @endforelse

        </div>


        {{-- DESKTOP TABLE --}}
        <div class="hidden md:block overflow-x-auto">

            <table class="w-full text-sm">

                <thead class="bg-gray-900/70">

                    <tr>

                        <th class="text-left px-4 py-3 text-gray-400">
                            Tanggal
                        </th>

                        <th class="text-left px-4 py-3 text-gray-400">
                            Staf
                        </th>

                        <th class="text-center px-4 py-3 text-gray-400">
                            Check-in
                        </th>

                        <th class="text-center px-4 py-3 text-gray-400">
                            Check-out
                        </th>

                        <th class="text-center px-4 py-3 text-gray-400">
                            Status
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-white/10">

                    @forelse($attendances as $attendance)

                        <tr class="hover:bg-white/5">

                            <td class="px-4 py-3 text-gray-300">
                                {{ $attendance->date?->translatedFormat('d M Y') }}
                            </td>

                            <td class="px-4 py-3 text-white font-medium">
                                {{ $attendance->user->name ?? $attendance->user->username ?? '-' }}
                            </td>

                            <td class="px-4 py-3 text-center text-emerald-400">
                                {{ $attendance->check_in?->format('H:i') ?? '-' }}
                            </td>

                            <td class="px-4 py-3 text-center text-white">
                                {{ $attendance->check_out?->format('H:i') ?? '-' }}
                            </td>

                            <td class="px-4 py-3 text-center">

                                @if($attendance->status === 'hadir')

                                    <span class="inline-flex rounded-full
                                                 bg-emerald-500/15
                                                 text-emerald-400
                                                 px-2 py-1 text-xs">
                                        Hadir
                                    </span>

                                @else

                                    <span class="inline-flex rounded-full
                                                 bg-gray-500/15
                                                 text-gray-400
                                                 px-2 py-1 text-xs">
                                        {{ ucfirst($attendance->status ?? '-') }}
                                    </span>

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="5"
                                class="px-4 py-10
                                       text-center text-gray-400"
                            >
                                Belum ada data absensi.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- PAGINATION --}}
        @if($attendances->hasPages())

            <div class="p-4 border-t border-white/10">
                {{ $attendances->links() }}
            </div>

        @endif

    </div>

</div>

@endsection