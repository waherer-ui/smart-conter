@extends('layouts.app')

@section('content')

<div class="max-w-3xl mx-auto px-4 py-6"><div class="mb-6">
    <h1 class="text-xl font-bold text-white">
        Periksa Backup
    </h1>

    <p class="text-sm text-gray-400 mt-1">
        File berhasil dibaca dan lolos pemeriksaan dasar.
    </p>
</div>


{{-- Metadata --}}
<div class="bg-gray-800/80 border border-gray-700 rounded-2xl p-5 mb-5">

    <h2 class="font-semibold text-white mb-4">
        Informasi Backup
    </h2>

    <div class="space-y-3 text-sm">

        <div class="flex justify-between gap-4">
            <span class="text-gray-400">Aplikasi</span>
            <span class="text-white">
                {{ $backup['application'] }}
            </span>
        </div>

        <div class="flex justify-between gap-4">
            <span class="text-gray-400">Versi</span>
            <span class="text-white">
                {{ $backup['format_version'] }}
            </span>
        </div>

        <div class="flex justify-between gap-4">
            <span class="text-gray-400">Tanggal Backup</span>
            <span class="text-white">
                {{ $backup['backup_date'] }}
            </span>
        </div>

        <div class="flex justify-between gap-4">
            <span class="text-gray-400">Jumlah Toko</span>
            <span class="text-emerald-400 font-medium">
                {{ count($backupStores) }}
            </span>
        </div>

    </div>

</div>


{{-- Toko --}}
<div class="bg-gray-800/80 border border-gray-700 rounded-2xl overflow-hidden mb-5">

    <div class="px-5 py-4 border-b border-gray-700">
        <h2 class="font-semibold text-white">
            Toko dalam Backup
        </h2>
    </div>

    <div class="divide-y divide-gray-700">

        @foreach($backupStores as $store)

            <div class="px-5 py-4">

                <p class="text-sm font-medium text-white">
                    {{ $store['name'] ?? 'Tanpa Nama' }}
                </p>

                @if(!empty($store['address']))
                    <p class="text-xs text-gray-500 mt-1">
                        {{ $store['address'] }}
                    </p>
                @endif

            </div>

        @endforeach

    </div>

</div>


{{-- Data --}}
<div class="bg-gray-800/80 border border-gray-700 rounded-2xl p-5 mb-5">

    <h2 class="font-semibold text-white mb-4">
        Isi Backup
    </h2>

    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">

        @foreach($counts as $table => $count)

            <div class="bg-gray-900/50 rounded-lg px-3 py-2">

                <p class="text-xs text-gray-500">
                    {{ ucwords(str_replace('_', ' ', $table)) }}
                </p>

                <p class="text-sm text-white font-semibold mt-1">
                    {{ number_format($count) }}
                </p>

            </div>

        @endforeach

    </div>

</div>


{{-- Status --}}
<div class="bg-emerald-500/10 border border-emerald-500/20 rounded-2xl p-4 mb-5">

    <div class="flex items-start gap-3">

        <svg class="w-5 h-5 text-emerald-400 flex-shrink-0"
             fill="none"
             stroke="currentColor"
             viewBox="0 0 24 24">

            <path stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M5 13l4 4L19 7"/>

        </svg>

        <div>

            <p class="text-sm font-medium text-emerald-300">
                Backup valid
            </p>

            <p class="text-xs text-gray-400 mt-1 leading-relaxed">
                Belum ada perubahan database yang dilakukan.
                Data baru akan diproses setelah Anda mengonfirmasi restore.
            </p>

        </div>

    </div>

</div>


{{-- Restore --}}
<div class="bg-gray-800/80 border border-gray-700 rounded-2xl p-5">

    <div class="mb-4">

        <h2 class="font-semibold text-white">
            Pulihkan Data
        </h2>

        <p class="text-xs text-gray-400 mt-1 leading-relaxed">
            Data dari backup akan ditambahkan ke akun Anda.
            Data yang sudah ada tidak akan dihapus.
        </p>

    </div>


    <div class="bg-amber-500/10 border border-amber-500/20 rounded-xl p-3 mb-4">

        <div class="flex items-start gap-2">

            <svg class="w-5 h-5 text-amber-400 flex-shrink-0"
                 fill="none"
                 stroke="currentColor"
                 viewBox="0 0 24 24">

                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M12 9v2m0 4h.01M10.29 3.86l-8.82 15A2 2 0 003.2 22h17.6a2 2 0 001.73-3.14l-8.82-15a2 2 0 00-3.42 0z"/>

            </svg>

            <p class="text-xs text-gray-400 leading-relaxed">
                Pastikan file backup ini memang berasal dari akun
                KasirKU Anda sebelum melanjutkan.
            </p>

        </div>

    </div>


    <form
        action="{{ route('backup.restore.execute') }}"
        method="POST"
        onsubmit="return confirm('Yakin ingin memulihkan data dari backup ini? Proses ini akan menambahkan data ke akun Anda.');"
    >

        @csrf

        <button
            type="submit"
            class="w-full inline-flex items-center justify-center gap-2
                   px-5 py-3
                   rounded-xl
                   bg-indigo-600 hover:bg-indigo-500
                   text-white text-sm font-semibold
                   transition"
        >

            <svg class="w-5 h-5"
                 fill="none"
                 stroke="currentColor"
                 viewBox="0 0 24 24">

                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1M12 3v12m0 0l-4-4m4 4l4-4"/>

            </svg>

            Restore Sekarang

        </button>

    </form>

</div>

</div>@endsection