@extends('layouts.app')

@section('header', '🕘 Absensi')

@section('content')

<div class="max-w-lg mx-auto px-4 py-4">

    {{-- =====================================================
         INFORMASI TOKO
         ===================================================== --}}
    <div class="rounded-2xl border border-white/10 bg-gray-800 p-4 mb-4">

        <div class="flex items-center justify-between gap-3">

            <div>
                <p class="text-xs text-gray-400">
                    Toko
                </p>

                <h2 class="text-lg font-semibold text-white mt-1">
                    {{ $store->name }}
                </h2>
            </div>

            <div class="text-right">

                <p class="text-xs text-gray-400">
                    Hari ini
                </p>

                <p class="text-sm text-gray-300 mt-1">
                    {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d F Y') }}
                </p>

            </div>

        </div>

    </div>


    {{-- =====================================================
         STATUS ABSENSI
         ===================================================== --}}
    <div class="rounded-2xl border border-white/10 bg-gray-800 p-5">

        {{-- HEADER STATUS --}}
        <div class="text-center">

            <div class="text-5xl mb-3">

                @if(!$attendance)
                    🕘
                @elseif(!$attendance->check_out)
                    🟢
                @else
                    ✅
                @endif

            </div>

            <h2 class="text-xl font-bold text-white">

                @if(!$attendance)
                    Belum Absen
                @elseif(!$attendance->check_out)
                    Sedang Bekerja
                @else
                    Absensi Selesai
                @endif

            </h2>

            <p class="text-sm text-gray-400 mt-1">

                @if(!$attendance)
                    Silakan lakukan check-in untuk memulai pekerjaan.
                @elseif(!$attendance->check_out)
                    Anda sedang tercatat bekerja hari ini.
                @else
                    Absensi hari ini sudah selesai.
                @endif

            </p>

        </div>


        {{-- =================================================
             DETAIL ABSENSI
             ================================================= --}}
        @if($attendance)

            <div class="grid grid-cols-2 gap-3 mt-6">

                {{-- CHECK-IN --}}
                <div class="rounded-xl bg-gray-900/70 border border-white/5 p-4">

                    <div class="flex items-center gap-2">

                        <span class="text-lg">
                            🟢
                        </span>

                        <p class="text-xs text-gray-400">
                            Check-in
                        </p>

                    </div>

                    <p class="text-xl font-bold text-emerald-400 mt-2">
                        {{ $attendance->check_in?->format('H:i') ?? '-' }}
                    </p>

                    @if($attendance->status === 'late')

                        <p class="text-[11px] text-amber-400 mt-1">
                            ⚠️ Terlambat
                        </p>

                    @else

                        <p class="text-[11px] text-gray-500 mt-1">
                            Tepat waktu
                        </p>

                    @endif

                </div>


                {{-- CHECK-OUT --}}
                <div class="rounded-xl bg-gray-900/70 border border-white/5 p-4">

                    <div class="flex items-center gap-2">

                        <span class="text-lg">

                            @if($attendance->check_out)
                                🔴
                            @else
                                ⏳
                            @endif

                        </span>

                        <p class="text-xs text-gray-400">
                            Check-out
                        </p>

                    </div>

                    <p class="text-xl font-bold text-white mt-2">
                        {{ $attendance->check_out?->format('H:i') ?? '-' }}
                    </p>

                    @if($attendance->check_out)

                        <p class="text-[11px] text-gray-500 mt-1">
                            Selesai
                        </p>

                    @else

                        <p class="text-[11px] text-amber-400 mt-1">
                            Belum check-out
                        </p>

                    @endif

                </div>

            </div>


            {{-- STATUS KEHADIRAN --}}
            <div class="rounded-xl bg-gray-900/50 border border-white/5 p-3 mt-3">

                <div class="flex items-center justify-between">

                    <span class="text-xs text-gray-400">
                        Status kehadiran
                    </span>

                    @if($attendance->status === 'late')

                        <span class="text-xs font-semibold text-amber-400">
                            ⚠️ Terlambat
                        </span>

                    @else

                        <span class="text-xs font-semibold text-emerald-400">
                            ✓ Tepat waktu
                        </span>

                    @endif

                </div>

            </div>

        @endif


        {{-- =================================================
             BELUM ABSEN
             ================================================= --}}
        @if(!$attendance)

            <div class="mt-6">

                <form
                    method="GET"
                    action="{{ route('attendance.face-check') }}"
                    id="checkInForm"
                >

                    <input
                        type="hidden"
                        name="latitude"
                        id="latitude"
                    >

                    <input
                        type="hidden"
                        name="longitude"
                        id="longitude"
                    >

                    <button
                        type="button"
                        id="checkInButton"
                        class="w-full rounded-xl bg-emerald-600
                               hover:bg-emerald-500
                               active:bg-emerald-700
                               text-white font-semibold
                               py-3 transition
                               disabled:opacity-60
                               disabled:cursor-not-allowed"
                    >
                        🟢 Check-in
                    </button>

                    <p
                        id="locationStatus"
                        class="text-center text-xs text-gray-400 mt-3 min-h-[18px]"
                    ></p>

                </form>

            </div>


        {{-- =================================================
             SUDAH CHECK-IN
             ================================================= --}}
        @elseif(!$attendance->check_out)

            <div class="mt-6">

                <form
                    method="POST"
                    action="{{ route('attendance.check-out') }}"
                    id="checkOutForm"
                >

                    @csrf

                    <input
                        type="hidden"
                        name="latitude"
                        id="checkOutLatitude"
                    >

                    <input
                        type="hidden"
                        name="longitude"
                        id="checkOutLongitude"
                    >

                    <button
                        type="button"
                        id="checkOutButton"
                        class="w-full rounded-xl bg-red-600
                               hover:bg-red-500
                               active:bg-red-700
                               text-white font-semibold
                               py-3 transition
                               disabled:opacity-60
                               disabled:cursor-not-allowed"
                    >
                        🔴 Check-out
                    </button>

                    <p
                        id="checkOutLocationStatus"
                        class="text-center text-xs text-gray-400 mt-3 min-h-[18px]"
                    ></p>

                </form>

            </div>


        {{-- =================================================
             SELESAI
             ================================================= --}}
        @else

            <div
                class="mt-6 w-full rounded-xl
                       border border-emerald-500/20
                       bg-emerald-500/10
                       text-emerald-400
                       text-center py-3 font-medium"
            >
                ✅ Absensi hari ini selesai
            </div>

        @endif

    </div>

</div>


<script>

document.addEventListener('DOMContentLoaded', function () {

    // =========================================================
    // CHECK-IN
    // GPS → FACE CHECK
    // =========================================================

    const checkInButton =
        document.getElementById('checkInButton');

    const checkInForm =
        document.getElementById('checkInForm');

    const checkInLatitude =
        document.getElementById('latitude');

    const checkInLongitude =
        document.getElementById('longitude');

    const checkInStatus =
        document.getElementById('locationStatus');


    if (checkInButton) {

        checkInButton.addEventListener('click', function () {

            if (!navigator.geolocation) {

                checkInStatus.textContent =
                    '❌ Browser tidak mendukung GPS.';

                checkInStatus.className =
                    'text-center text-xs text-red-400 mt-3 min-h-[18px]';

                return;
            }

            checkInButton.disabled = true;

            checkInButton.textContent =
                '📍 Mengambil lokasi...';

            checkInStatus.textContent =
                'Mohon izinkan akses lokasi.';

            checkInStatus.className =
                'text-center text-xs text-gray-400 mt-3 min-h-[18px]';


            navigator.geolocation.getCurrentPosition(

                function (position) {

                    checkInLatitude.value =
                        position.coords.latitude;

                    checkInLongitude.value =
                        position.coords.longitude;

                    checkInStatus.textContent =
                        '✅ Lokasi ditemukan. Membuka verifikasi wajah...';

                    checkInStatus.className =
                        'text-center text-xs text-emerald-400 mt-3 min-h-[18px]';

                    checkInForm.submit();

                },


                function (error) {

                    checkInButton.disabled = false;

                    checkInButton.textContent =
                        '🟢 Check-in';

                    checkInStatus.className =
                        'text-center text-xs text-red-400 mt-3 min-h-[18px]';


                    if (error.code === 1) {

                        checkInStatus.textContent =
                            '❌ Izin lokasi ditolak. Izinkan lokasi untuk check-in.';

                    } else if (error.code === 2) {

                        checkInStatus.textContent =
                            '❌ Lokasi tidak tersedia. Pastikan GPS aktif.';

                    } else if (error.code === 3) {

                        checkInStatus.textContent =
                            '❌ Pengambilan lokasi terlalu lama. Coba lagi.';

                    } else {

                        checkInStatus.textContent =
                            '❌ Lokasi tidak dapat diperoleh. Silakan coba lagi.';

                    }

                },


                {
                    enableHighAccuracy: true,
                    timeout: 15000,
                    maximumAge: 0
                }

            );

        });

    }


    // =========================================================
    // CHECK-OUT
    // GPS SAJA
    // =========================================================

    const checkOutButton =
        document.getElementById('checkOutButton');

    const checkOutForm =
        document.getElementById('checkOutForm');

    const checkOutLatitude =
        document.getElementById('checkOutLatitude');

    const checkOutLongitude =
        document.getElementById('checkOutLongitude');

    const checkOutStatus =
        document.getElementById('checkOutLocationStatus');


    if (checkOutButton) {

        checkOutButton.addEventListener('click', function () {

            if (!navigator.geolocation) {

                checkOutStatus.textContent =
                    '❌ Browser tidak mendukung GPS.';

                checkOutStatus.className =
                    'text-center text-xs text-red-400 mt-3 min-h-[18px]';

                return;
            }

            checkOutButton.disabled = true;

            checkOutButton.textContent =
                '📍 Mengambil lokasi...';

            checkOutStatus.textContent =
                'Mohon izinkan akses lokasi.';

            checkOutStatus.className =
                'text-center text-xs text-gray-400 mt-3 min-h-[18px]';


            navigator.geolocation.getCurrentPosition(

                function (position) {

                    checkOutLatitude.value =
                        position.coords.latitude;

                    checkOutLongitude.value =
                        position.coords.longitude;

                    checkOutStatus.textContent =
                        '✅ Lokasi ditemukan. Memproses check-out...';

                    checkOutStatus.className =
                        'text-center text-xs text-emerald-400 mt-3 min-h-[18px]';

                    checkOutForm.submit();

                },


                function (error) {

                    checkOutButton.disabled = false;

                    checkOutButton.textContent =
                        '🔴 Check-out';

                    checkOutStatus.className =
                        'text-center text-xs text-red-400 mt-3 min-h-[18px]';


                    if (error.code === 1) {

                        checkOutStatus.textContent =
                            '❌ Izin lokasi ditolak. Izinkan lokasi untuk check-out.';

                    } else if (error.code === 2) {

                        checkOutStatus.textContent =
                            '❌ Lokasi tidak tersedia. Pastikan GPS aktif.';

                    } else if (error.code === 3) {

                        checkOutStatus.textContent =
                            '❌ Pengambilan lokasi terlalu lama. Coba lagi.';

                    } else {

                        checkOutStatus.textContent =
                            '❌ Lokasi tidak dapat diperoleh. Silakan coba lagi.';

                    }

                },


                {
                    enableHighAccuracy: true,
                    timeout: 15000,
                    maximumAge: 0
                }

            );

        });

    }

});

</script>

@endsection