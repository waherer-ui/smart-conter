@extends('layouts.app')

@section('content')

<div class="max-w-2xl mx-auto px-4 py-6">

    {{-- Header --}}
    <div class="mb-6">
        <div class="flex items-center gap-3">

            <div class="w-11 h-11 rounded-xl bg-indigo-500/15 flex items-center justify-center">
                <svg class="w-6 h-6 text-indigo-400"
                     fill="none"
                     stroke="currentColor"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1M12 3v12m0 0l-4-4m4 4l4-4"/>
                </svg>
            </div>

            <div>
                <h1 class="text-xl font-bold text-white">
                    Restore Data
                </h1>

                <p class="text-sm text-gray-400">
                    Pulihkan data bisnis dari file backup KasirKU
                </p>
            </div>

        </div>
    </div>


    {{-- Warning --}}
    <div class="bg-amber-500/10 border border-amber-500/20 rounded-2xl p-4 mb-5">

        <div class="flex items-start gap-3">

            <svg class="w-5 h-5 text-amber-400 flex-shrink-0 mt-0.5"
                 fill="none"
                 stroke="currentColor"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M12 9v2m0 4h.01M10.29 3.86l-7.12 12A2 2 0 004.9 19h14.2a2 2 0 001.73-3.14l-7.12-12a2 2 0 00-3.42 0z"/>
            </svg>

            <div>

                <p class="text-sm font-medium text-amber-300">
                    Gunakan file backup milik akun ini
                </p>

                <p class="text-xs text-gray-400 mt-1 leading-relaxed">
                    KasirKU akan memeriksa file backup sebelum data dipulihkan.
                    Jangan gunakan file dari akun atau sumber yang tidak dikenal.
                </p>

            </div>

        </div>

    </div>


    {{-- Upload --}}
    <div class="bg-gray-800/80 border border-gray-700 rounded-2xl p-5">

        <form
            action="{{ route('backup.restore.preview') }}"
            method="POST"
            enctype="multipart/form-data"
        >

            @csrf

            <label class="block text-sm font-medium text-gray-200 mb-2">
                File Backup
            </label>

            <input
                type="file"
                name="backup_file"
                accept=".zip"
                required
                class="block w-full text-sm text-gray-400
                       file:mr-4
                       file:py-2.5
                       file:px-4
                       file:rounded-xl
                       file:border-0
                       file:bg-indigo-600
                       file:text-white
                       file:font-medium
                       hover:file:bg-indigo-500
                       bg-gray-900/50
                       border border-gray-700
                       rounded-xl
                       p-2"
            >

            @error('backup_file')
                <p class="text-xs text-red-400 mt-2">
                    {{ $message }}
                </p>
            @enderror

            <p class="text-xs text-gray-500 mt-2">
                Format yang didukung: ZIP. Maksimal 100 MB.
            </p>


            <button
                type="submit"
                class="w-full mt-5 inline-flex items-center justify-center gap-2
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
                          d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M12 12v9m0 0l-3-3m3 3l3-3"/>
                </svg>

                Periksa File Backup

            </button>

        </form>

    </div>


    {{-- Back --}}
    <div class="mt-4 text-center">

        <a
            href="{{ route('backup.index') }}"
            class="text-sm text-gray-400 hover:text-white transition"
        >
            ← Kembali ke Backup Data
        </a>

    </div>

</div>

@endsection