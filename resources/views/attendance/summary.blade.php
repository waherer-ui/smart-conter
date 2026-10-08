@extends('layouts.app')

@section('content')

<div class="max-w-6xl mx-auto px-4 py-4">

    {{-- TOKO --}}
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

        <form method="GET" action="{{ route('attendance.summary') }}">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">

                {{-- BULAN --}}
                <div>

                    <label class="block text-xs text-gray-400 mb-1">
                        Bulan
                    </label>

                    <input
                        type="month"
                        name="month"
                        value="{{ $month }}"
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


    {{-- REKAP --}}
    <div class="space-y-4">

        @forelse($summary as $item)

            @php
                $hours = intdiv(
                    $item['total_minutes'],
                    60
                );

                $minutes = $item['total_minutes'] % 60;
            @endphp

            <div class="rounded-2xl border border-white/10
                        bg-gray-800 p-4">

                {{-- NAMA --}}
                <div class="flex items-center justify-between mb-4">

                    <div>
                        <h3 class="text-lg font-semibold text-white">
                            {{ $item['user']->name ?? $item['user']->username ?? '-' }}
                        </h3>

                        <p class="text-xs text-gray-400 mt-1">
                            Rekap {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->translatedFormat('F Y') }}
                        </p>
                    </div>

                    <div class="text-3xl">
                        📊
                    </div>

                </div>


                {{-- STATISTIK --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">

                    {{-- HADIR --}}
                    <div class="rounded-xl bg-gray-900/70 p-3">

                        <p class="text-xs text-gray-400">
                            Hadir
                        </p>

                        <p class="text-xl font-bold text-emerald-400 mt-1">
                            {{ $item['total_days'] }}
                        </p>

                        <p class="text-xs text-gray-500">
                            hari
                        </p>

                    </div>


                    {{-- LENGKAP --}}
                    <div class="rounded-xl bg-gray-900/70 p-3">

                        <p class="text-xs text-gray-400">
                            Lengkap
                        </p>

                        <p class="text-xl font-bold text-white mt-1">
                            {{ $item['complete_days'] }}
                        </p>

                        <p class="text-xs text-gray-500">
                            check-in + out
                        </p>

                    </div>


                    {{-- BELUM LENGKAP --}}
                    <div class="rounded-xl bg-gray-900/70 p-3">

                        <p class="text-xs text-gray-400">
                            Belum lengkap
                        </p>

                        <p class="text-xl font-bold text-yellow-400 mt-1">
                            {{ $item['incomplete_days'] }}
                        </p>

                        <p class="text-xs text-gray-500">
                            belum check-out
                        </p>

                    </div>


                    {{-- JAM KERJA --}}
                    <div class="rounded-xl bg-gray-900/70 p-3">

                        <p class="text-xs text-gray-400">
                            Jam kerja
                        </p>

                        <p class="text-xl font-bold text-white mt-1">
                            {{ $hours }}j {{ $minutes }}m
                        </p>

                        <p class="text-xs text-gray-500">
                            total
                        </p>

                    </div>

                </div>

            </div>

        @empty

            <div class="rounded-2xl border border-white/10
                        bg-gray-800 p-8 text-center">

                <div class="text-4xl mb-3">
                    📊
                </div>

                <p class="text-gray-400">
                    Belum ada data absensi pada bulan ini.
                </p>

            </div>

        @endforelse

    </div>

</div>

@endsection