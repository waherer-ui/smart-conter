@extends('layouts.app')

@section('title', 'Edit Paket')
@section('header', '📦')

@section('content')

<div class="max-w-4xl mx-auto space-y-6">{{-- =====================================================
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

            @forelse($plan->limits as $limit)

                @php

                    $limitLabel = match ($limit->key) {

                        'history_days'
                            => 'Riwayat Data',

                        'max_customers'
                            => 'Pelanggan Tersimpan',

                        'max_products'
                            => 'Jenis Produk',

                        'max_staff'
                            => 'Akun Staff/Kasir',

                        'max_stores'
                            => 'Toko/Cabang',

                        default
                            => ucwords(
                                str_replace(
                                    '_',
                                    ' ',
                                    $limit->key
                                )
                            ),

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

                        @switch($limit->key)

                            @case('history_days')
                                Jumlah hari riwayat data
                                yang dapat diakses.
                                @break

                            @case('max_customers')
                                Maksimal pelanggan
                                yang dapat disimpan.
                                @break

                            @case('max_products')
                                Maksimal jenis produk
                                yang dapat disimpan.
                                @break

                            @case('max_staff')
                                Maksimal akun staff/kasir.
                                @break

                            @case('max_stores')
                                Maksimal toko atau cabang.
                                @break

                        @endswitch

                    </p>

                </div>

            @empty

                <div class="rounded-xl
                            border border-white/10
                            bg-gray-900/40
                            p-4">

                    <p class="text-sm text-gray-500">
                        Belum ada konfigurasi limit untuk paket ini.
                    </p>

                </div>

            @endforelse

        </div>

    </div>


    {{-- =================================================
         FITUR PAKET
    ================================================== --}}

    @php

        $selectedFeatures = old(
            'features',
            $plan->features
                ->pluck('id')
                ->toArray()
        );

        $selectedFeatureCount = count($selectedFeatures);

    @endphp


    <div class="bg-gray-800/80 border border-white/10
                rounded-2xl p-5 shadow-xl">

        <div class="flex flex-col sm:flex-row
                    sm:items-center sm:justify-between
                    gap-2 mb-4">

            <div>

                <h2 class="text-base font-semibold text-white">
                    Fitur Paket
                </h2>

                <p class="mt-1 text-xs text-gray-500">
                    Pilih fitur yang tersedia pada paket ini.
                </p>

            </div>

            <span class="text-xs text-gray-500">
                {{ $selectedFeatureCount }} fitur aktif
            </span>

        </div>


        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">

            @forelse($features as $feature)

                <label
                    class="flex items-start gap-3
                           rounded-xl
                           bg-gray-900/60
                           border border-white/5
                           p-3
                           cursor-pointer
                           hover:bg-gray-900
                           transition"
                >

                    <input
                        type="checkbox"
                        name="features[]"
                        value="{{ $feature->id }}"
                        {{ in_array(
                            $feature->id,
                            $selectedFeatures
                        ) ? 'checked' : '' }}
                        class="mt-1 h-4 w-4
                               rounded
                               border-gray-600
                               bg-gray-800
                               text-emerald-500
                               focus:ring-emerald-500"
                    >


                    <div class="min-w-0">

                        <p class="text-sm font-medium text-gray-200">
                            {{ $feature->name }}
                        </p>

                        @if($feature->description)

                            <p class="mt-1 text-xs text-gray-500">
                                {{ $feature->description }}
                            </p>

                        @endif

                    </div>

                </label>

            @empty

                <div class="sm:col-span-2
                            rounded-xl
                            border border-white/10
                            bg-gray-900/40
                            p-4">

                    <p class="text-sm text-gray-500">
                        Belum ada fitur aktif.
                    </p>

                </div>

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

</div>@endsection