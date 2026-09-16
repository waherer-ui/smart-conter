@extends('layouts.app')

@section('title', 'Tambah Utang')
@section('header', '💰')

@section('content')

<div class="max-w-xl mx-auto">

    {{-- HEADER --}}
    <div class="flex items-center gap-3 mb-5">

        <a
            href="{{ route('pelanggan.show', $customer->id) }}"
            class="w-9 h-9
                   flex items-center justify-center
                   bg-gray-800
                   border border-white/10
                   rounded-xl
                   text-gray-300
                   hover:text-white
                   transition"
        >
            ←
        </a>

        <div>
            <h2 class="text-lg font-bold text-white">
                Tambah Utang
            </h2>

            <p class="text-xs text-gray-400">
                Catat utang pelanggan
            </p>
        </div>

    </div>

    {{-- PELANGGAN --}}
    <div class="bg-gray-800/80
                border border-white/10
                rounded-2xl
                p-4 mb-4">

        <div class="text-[11px] text-gray-500 uppercase">
            Pelanggan
        </div>

        <div class="text-sm font-semibold text-white mt-1">
            {{ $customer->name }}
        </div>

        @if($customer->phone)

            <div class="text-xs text-gray-400 mt-1">
                📱 {{ $customer->phone }}
            </div>

        @endif

    </div>

    {{-- FORM --}}
    <div class="bg-gray-800/80
                border border-white/10
                rounded-2xl
                p-5">

        <form
            action="{{ route('pelanggan.debt.store', $customer->id) }}"
            method="POST"
            class="space-y-4"
        >

            @csrf

            {{-- JUMLAH --}}
            <div>

                <label class="block text-xs font-medium text-gray-300 mb-1.5">
                    Jumlah Utang
                </label>

                <input
                    type="number"
                    name="amount"
                    min="1"
                    step="1"
                    inputmode="numeric"
                    required
                    placeholder="Contoh: 500000"
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

                @error('amount')
                    <p class="text-xs text-red-400 mt-1">
                        {{ $message }}
                    </p>
                @enderror

            </div>

            {{-- KETERANGAN --}}
            <div>

                <label class="block text-xs font-medium text-gray-300 mb-1.5">
                    Keterangan
                    <span class="text-gray-500">(opsional)</span>
                </label>

                <input
                    type="text"
                    name="description"
                    maxlength="255"
                    placeholder="Contoh: Servis LCD / Beli aksesoris"
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

            </div>

            {{-- TANGGAL UTANG --}}
            <div>

                <label class="block text-xs font-medium text-gray-300 mb-1.5">
                    Tanggal Utang
                </label>

                <input
                    type="date"
                    name="debt_date"
                    value="{{ old('debt_date', now()->format('Y-m-d')) }}"
                    required
                    class="w-full
                           bg-gray-900
                           border border-white/10
                           rounded-xl
                           px-4 py-3
                           text-sm text-white
                           focus:outline-none
                           focus:border-emerald-500"
                >

            </div>

            {{-- JATUH TEMPO --}}
            <div>

                <label class="block text-xs font-medium text-gray-300 mb-1.5">
                    Jatuh Tempo
                    <span class="text-gray-500">(opsional)</span>
                </label>

                <input
                    type="date"
                    name="due_date"
                    value="{{ old('due_date') }}"
                    class="w-full
                           bg-gray-900
                           border border-white/10
                           rounded-xl
                           px-4 py-3
                           text-sm text-white
                           focus:outline-none
                           focus:border-emerald-500"
                >

            </div>

            {{-- TOMBOL --}}
            <div class="flex gap-2 pt-2">

                <a
                    href="{{ route('pelanggan.show', $customer->id) }}"
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
                    Simpan Utang
                </button>

            </div>

        </form>

    </div>

</div>

@endsection