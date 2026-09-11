@extends('layouts.app')

@section('title', 'Tambah Toko')
@section('header', 'Tambah Toko / Cabang')

@section('content')

<div class="max-w-xl mx-auto">

    <div class="bg-gray-800 rounded-2xl border border-white/10 p-5">

        <form
            action="{{ route('store.store') }}"
            method="POST"
            class="space-y-5"
        >

            @csrf

            {{-- Nama Toko --}}
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">
                    Nama Toko
                </label>

                <input
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    required
                    class="w-full rounded-xl bg-gray-900 border border-white/10
                           px-4 py-3 text-white
                           focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    placeholder="Contoh: Cabang Rajabasa"
                >

                @error('name')
                    <p class="mt-1 text-sm text-red-400">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Alamat --}}
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">
                    Alamat
                </label>

                <textarea
                    name="address"
                    rows="3"
                    class="w-full rounded-xl bg-gray-900 border border-white/10
                           px-4 py-3 text-white
                           focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    placeholder="Alamat toko"
                >{{ old('address') }}</textarea>

                @error('address')
                    <p class="mt-1 text-sm text-red-400">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Nomor HP --}}
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">
                    Nomor HP
                </label>

                <input
                    type="text"
                    name="phone"
                    value="{{ old('phone') }}"
                    class="w-full rounded-xl bg-gray-900 border border-white/10
                           px-4 py-3 text-white
                           focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    placeholder="08xxxxxxxxxx"
                >

                @error('phone')
                    <p class="mt-1 text-sm text-red-400">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Tombol --}}
            <div class="flex gap-3 pt-2">

                <a
                    href="{{ route('dashboard') }}"
                    class="flex-1 text-center rounded-xl bg-gray-700
                           px-4 py-3 font-semibold text-white
                           hover:bg-gray-600 transition"
                >
                    Batal
                </a>

                <button
                    type="submit"
                    class="flex-1 rounded-xl bg-emerald-500
                           px-4 py-3 font-semibold text-white
                           hover:bg-emerald-600 transition"
                >
                    Buat Toko
                </button>

            </div>

        </form>

    </div>

</div>

@endsection