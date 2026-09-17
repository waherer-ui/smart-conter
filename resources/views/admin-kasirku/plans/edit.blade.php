@extends('layouts.app')

@section('title', 'Edit Paket')
@section('header', '📦')

@section('content')

<div class="max-w-4xl mx-auto space-y-6">

    {{-- =====================================================
         HEADER
    ====================================================== --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-white">
                Edit Paket
            </h1>

            <p class="mt-1 text-sm text-gray-400">
                Kelola informasi, batas penggunaan, dan fitur paket.
            </p>
        </div>

        <a
            href="{{ route('admin-kasirku.plans.index') }}"
            class="inline-flex items-center justify-center gap-2
                   px-4 py-2 rounded-xl
                   bg-gray-700 hover:bg-gray-600
                   text-white text-sm font-medium transition"
        >
            ← Kembali
        </a>

    </div>


    {{-- =====================================================
         FORM UTAMA
    ====================================================== --}}
    <form
        action="{{ route('admin-kasirku.plans.update', $plan) }}"
        method="POST"
        class="space-y-6"
    >

        @csrf
        @method('PUT')


        {{-- =================================================
             INFORMASI PAKET
        ================================================== --}}
        <div class="bg-gray-800/80 border border-white/10
                    rounded-2xl p-5 shadow-xl">

            <h2 class="text-base font-semibold text-white mb-4">
                Informasi Paket
            </h2>


            {{-- VALIDATION ERROR --}}
            @if ($errors->any())

                <div class="mb-5 rounded-xl
                            border border-red-500/20
                            bg-red-500/10 p-4">

                    <div class="flex items-start gap-3">

                        <div class="text-red-400 text-lg">
                            ⚠️
                        </div>

                        <div>

                            <p class="text-sm font-semibold text-red-300">
                                Periksa kembali data yang dimasukkan.
                            </p>

                            <ul class="mt-2 space-y-1
                                       text-xs text-red-300/90">

                                @foreach ($errors->all() as $error)

                                    <li>
                                        • {{ $error }}
                                    </li>

                                @endforeach

                            </ul>

                        </div>

                    </div>

                </div>

            @endif


            {{-- SUCCESS --}}
            @if (session('success'))

                <div class="mb-5 rounded-xl
                            border border-emerald-500/20
                            bg-emerald-500/10 p-4">

                    <p class="text-sm text-emerald-300">
                        ✓ {{ session('success') }}
                    </p>

                </div>

            @endif


            <div class="space-y-5">


                {{-- NAMA --}}
                <div>

                    <label class="block text-sm font-medium
                                  text-gray-300 mb-2">
                        Nama Paket
                    </label>

                    <input
                        type="text"
                        name="name"
                        value="{{ old('name', $plan->name) }}"
                        required
                        class="w-full rounded-xl
                               bg-gray-900/70
                               border border-white/10
                               px-4 py-3
                               text-sm text-white
                               focus:outline-none
                               focus:border-emerald-400/50"
                    >

                </div>


                {{-- SLUG --}}
                <div>

                    <label class="block text-sm font-medium
                                  text-gray-300 mb-2">
                        Slug
                    </label>

                    <input
                        type="text"
                        name="slug"
                        value="{{ old('slug', $plan->slug) }}"
                        required
                        class="w-full rounded-xl
                               bg-gray-900/70
                               border border-white/10
                               px-4 py-3
                               text-sm text-white
                               focus:outline-none
                               focus:border-emerald-400/50"
                    >

                    <p class="mt-1 text-xs text-gray-500">
                        Gunakan huruf kecil, angka, tanda strip,
                        atau underscore.
                    </p>

                </div>


                {{-- HARGA --}}
                <div>

                    <label class="block text-sm font-medium
                                  text-gray-300 mb-2">
                        Harga
                    </label>

                    <div class="relative">

                        <span
                            class="absolute left-4 top-1/2
                                   -translate-y-1/2
                                   text-gray-500 text-sm"
                        >
                            Rp
                        </span>

                        <input
                            type="number"
                            name="price"
                            value="{{ old('price', $plan->price) }}"
                            min="0"
                            step="0.01"
                            required
                            class="w-full rounded-xl
                                   bg-gray-900/70
                                   border border-white/10
                                   pl-11 pr-4 py-3
                                   text-sm text-white
                                   focus:outline-none
                                   focus:border-emerald-400/50"
                        >

                    </div>

                </div>


                {{-- DESKRIPSI --}}
                <div>

                    <label class="block text-sm font-medium
                                  text-gray-300 mb-2">
                        Deskripsi
                    </label>

                    <textarea
                        name="description"
                        rows="3"
                        class="w-full rounded-xl
                               bg-gray-900/70
                               border border-white/10
                               px-4 py-3
                               text-sm text-white
                               focus:outline-none
                               focus:border-emerald-400/50"
                    >{{ old('description', $plan->description) }}</textarea>

                </div>


                {{-- STATUS --}}
                <div>

                    <label class="block text-sm font-medium
                                  text-gray-300 mb-2">
                        Status Paket
                    </label>

                    <select
                        name="is_active"
                        required
                        class="w-full rounded-xl
                               bg-gray-900/70
                               border border-white/10
                               px-4 py-3
                               text-sm text-white
                               focus:outline-none
                               focus:border-emerald-400/50"
                    >

                        <option
                            value="1"
                            {{ old('is_active', $plan->is_active) == 1
                                ? 'selected'
                                : '' }}
                        >
                            Aktif
                        </option>

                        <option
                            value="0"
                            {{ old('is_active', $plan->is_active) == 0
                                ? 'selected'
                                : '' }}
                        >
                            Nonaktif
                        </option>

                    </select>

                </div>

            </div>

        </div>


        {{-- =================================================
             BATAS PENGGUNAAN
        ================================================== --}}
        <div class="bg-gray-800/80 border border-white/10
                    rounded-2xl p-5 shadow-xl">

            <h2 class="text-base font-semibold text-white mb-1">
                Batas Penggunaan
            </h2>

            <p class="text-xs text-gray-500 mb-5">
                Kosongkan jika ingin memberikan akses tanpa batas.
            </p>


            <div class="space-y-4">

                @foreach($plan->limits as $limit)

                    @php

                        $limitLabel = match ($limit->key) {

                            'history_days'
                                => 'Riwayat data',

                            'max_customers'
                                => 'Pelanggan tersimpan',

                            'max_products'
                                => 'Jenis produk',

                            'max_staff'
                                => 'Akun staff/kasir',

                            'max_stores'
                                => 'Toko/cabang',

                            default
                                => $limit->key,

                        };

                    @endphp


                    <div>

                        <label
                            class="block text-sm font-medium
                                   text-gray-300 mb-2"
                        >
                            {{ $limitLabel }}
                        </label>


                        <input
                            type="number"
                            name="limits[{{ $limit->key }}]"
                            value="{{ old(
                                'limits.' . $limit->key,
                                $limit->value
                            ) }}"
                            min="0"
                            placeholder="Tanpa batas"
                            class="w-full rounded-xl
                                   bg-gray-900/70
                                   border border-white/10
                                   px-4 py-3
                                   text-sm text-white
                                   focus:outline-none
                                   focus:border-emerald-400/50"
                        >


                        <p class="mt-1 text-xs text-gray-500">

                            @if($limit->key === 'history_days')

                                Jumlah hari riwayat data
                                yang dapat diakses.

                            @elseif($limit->key === 'max_customers')

                                Maksimal pelanggan
                                yang dapat disimpan.

                            @elseif($limit->key === 'max_products')

                                Maksimal jenis produk
                                yang dapat disimpan.

                            @elseif($limit->key === 'max_staff')

                                Maksimal akun staff/kasir.

                            @elseif($limit->key === 'max_stores')

                                Maksimal toko atau cabang.

                            @endif

                        </p>

                    </div>

                @endforeach

            </div>

        </div>


        {{-- =================================================
             FITUR PAKET
        ================================================== --}}
        <div class="bg-gray-800/80 border border-white/10
                    rounded-2xl p-5 shadow-xl">

            <h2 class="text-base font-semibold text-white mb-4">
                Fitur Paket
            </h2>


            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">

                @forelse($plan->features as $feature)

                    <div
                        class="flex items-start gap-2
                               rounded-xl
                               bg-gray-900/60
                               p-3"
                    >

                        <span class="text-emerald-400 font-bold">
                            ✓
                        </span>

                        <span class="text-sm text-gray-300">
                            {{ $feature->name }}
                        </span>

                    </div>

                @empty

                    <p class="text-sm text-gray-500">
                        Belum ada fitur untuk paket ini.
                    </p>

                @endforelse

            </div>

        </div>


        {{-- =================================================
             TOMBOL
        ================================================== --}}
        <div class="flex flex-col sm:flex-row gap-2 pt-1">

            <button
                type="submit"
                class="inline-flex items-center
                       justify-center gap-2
                       px-5 py-2.5 rounded-xl
                       bg-emerald-500
                       hover:bg-emerald-400
                       text-gray-950
                       text-sm font-semibold
                       transition"
            >
                💾 Simpan Perubahan
            </button>


            <a
                href="{{ route('admin-kasirku.plans.index') }}"
                class="inline-flex items-center
                       justify-center
                       px-5 py-2.5 rounded-xl
                       bg-gray-700
                       hover:bg-gray-600
                       text-white
                       text-sm font-medium
                       transition"
            >
                Batal
            </a>

        </div>


    </form>

</div>

@endsection