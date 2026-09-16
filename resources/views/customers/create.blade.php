@extends('layouts.app')

@section('title', 'Tambah Pelanggan')
@section('header', '👥')

@section('content')

<div class="max-w-xl mx-auto">

    {{-- HEADER --}}
    <div class="mb-5">

        <h2 class="text-lg font-bold text-white">
            Tambah Pelanggan
        </h2>

        <p class="text-xs text-gray-400 mt-1">
            Simpan data pelanggan untuk catatan toko.
        </p>

    </div>

    {{-- FORM --}}
    <div class="bg-gray-800/80
                border border-white/10
                rounded-2xl
                p-5">

        <form
            action="{{ route('pelanggan.store') }}"
            method="POST"
            class="space-y-4"
        >

            @csrf

            {{-- NAMA --}}
            <div>

                <label class="block text-xs font-medium text-gray-300 mb-1.5">
                    Nama Pelanggan
                </label>

                <input
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    required
                    autofocus
                    placeholder="Masukkan nama pelanggan"
                    class="w-full
                           bg-gray-900
                           border border-white/10
                           rounded-xl
                           px-4 py-3
                           text-sm text-white
                           placeholder-gray-500
                           focus:outline-none
                           focus:border-emerald-500"
                >

                @error('name')
                    <p class="text-xs text-red-400 mt-1">
                        {{ $message }}
                    </p>
                @enderror

            </div>

            {{-- NOMOR HP --}}
            <div>

                <label class="block text-xs font-medium text-gray-300 mb-1.5">
                    Nomor HP
                    <span class="text-gray-500">(opsional)</span>
                </label>

                <input
                    type="text"
                    name="phone"
                    value="{{ old('phone') }}"
                    inputmode="tel"
                    placeholder="08xxxxxxxxxx"
                    class="w-full
                           bg-gray-900
                           border border-white/10
                           rounded-xl
                           px-4 py-3
                           text-sm text-white
                           placeholder-gray-500
                           focus:outline-none
                           focus:border-emerald-500"
                >

                @error('phone')
                    <p class="text-xs text-red-400 mt-1">
                        {{ $message }}
                    </p>
                @enderror

            </div>

            {{-- ALAMAT --}}
            <div>

                <label class="block text-xs font-medium text-gray-300 mb-1.5">
                    Alamat
                    <span class="text-gray-500">(opsional)</span>
                </label>

                <textarea
                    name="address"
                    rows="3"
                    placeholder="Masukkan alamat pelanggan"
                    class="w-full
                           bg-gray-900
                           border border-white/10
                           rounded-xl
                           px-4 py-3
                           text-sm text-white
                           placeholder-gray-500
                           focus:outline-none
                           focus:border-emerald-500
                           resize-none"
                >{{ old('address') }}</textarea>

                @error('address')
                    <p class="text-xs text-red-400 mt-1">
                        {{ $message }}
                    </p>
                @enderror

            </div>

            {{-- TOMBOL --}}
            <div class="flex items-center gap-2 pt-2">

                <a
                    href="{{ route('pelanggan.index') }}"
                    class="flex-1
                           text-center
                           bg-gray-700
                           hover:bg-gray-600
                           text-gray-200
                           px-4 py-3
                           rounded-xl
                           text-xs
                           font-semibold
                           transition"
                >
                    Batal
                </a>

                <button
                    type="submit"
                    class="flex-1
                           bg-emerald-500
                           hover:bg-emerald-400
                           text-gray-950
                           px-4 py-3
                           rounded-xl
                           text-xs
                           font-semibold
                           transition"
                >
                    Simpan Pelanggan
                </button>

            </div>

        </form>

    </div>

</div>

@endsection