@extends('layouts.app')

@section('title', 'Tambah Supplier')
@section('header', '🚚')

@section('content')

<div class="max-w-2xl mx-auto">

    <div
        class="bg-gray-800/80
               border border-white/10
               rounded-2xl
               shadow-xl
               overflow-hidden"
    >

        {{-- HEADER --}}
        <div class="px-5 py-4 border-b border-white/10">

            <h1 class="text-lg font-semibold text-white">
                Tambah Supplier
            </h1>

            <p class="text-xs text-gray-500 mt-1">
                Tambahkan pemasok untuk toko Anda.
            </p>

        </div>


        {{-- FORM --}}
        <form
            action="{{ route('supplier.store') }}"
            method="POST"
            class="p-5 space-y-4"
        >

            @csrf


            {{-- NAMA --}}
            <div>
                <label
                    for="name"
                    class="block text-xs
                           font-medium
                           text-gray-300
                           mb-1.5"
                >
                    Nama Supplier
                    <span class="text-red-400">*</span>
                </label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name') }}"
                    required
                    maxlength="255"
                    placeholder="Contoh: PT Sumber Jaya"
                    class="w-full
                           bg-gray-900
                           border border-white/10
                           rounded-xl
                           px-3 py-2.5
                           text-sm text-white
                           placeholder-gray-600
                           focus:outline-none
                           focus:border-emerald-500"
                >

                @error('name')
                    <p class="text-xs text-red-400 mt-1">
                        {{ $message }}
                    </p>
                @enderror
            </div>


            {{-- TELEPON --}}
            <div>
                <label
                    for="phone"
                    class="block text-xs
                           font-medium
                           text-gray-300
                           mb-1.5"
                >
                    Nomor Telepon
                </label>

                <input
                    type="text"
                    id="phone"
                    name="phone"
                    value="{{ old('phone') }}"
                    maxlength="30"
                    placeholder="Contoh: 081234567890"
                    class="w-full
                           bg-gray-900
                           border border-white/10
                           rounded-xl
                           px-3 py-2.5
                           text-sm text-white
                           placeholder-gray-600
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
                <label
                    for="address"
                    class="block text-xs
                           font-medium
                           text-gray-300
                           mb-1.5"
                >
                    Alamat
                </label>

                <textarea
                    id="address"
                    name="address"
                    rows="3"
                    maxlength="1000"
                    placeholder="Alamat supplier..."
                    class="w-full
                           bg-gray-900
                           border border-white/10
                           rounded-xl
                           px-3 py-2.5
                           text-sm text-white
                           placeholder-gray-600
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


            {{-- CATATAN --}}
            <div>
                <label
                    for="notes"
                    class="block text-xs
                           font-medium
                           text-gray-300
                           mb-1.5"
                >
                    Catatan
                </label>

                <textarea
                    id="notes"
                    name="notes"
                    rows="3"
                    maxlength="2000"
                    placeholder="Catatan tambahan tentang supplier..."
                    class="w-full
                           bg-gray-900
                           border border-white/10
                           rounded-xl
                           px-3 py-2.5
                           text-sm text-white
                           placeholder-gray-600
                           focus:outline-none
                           focus:border-emerald-500
                           resize-none"
                >{{ old('notes') }}</textarea>

                @error('notes')
                    <p class="text-xs text-red-400 mt-1">
                        {{ $message }}
                    </p>
                @enderror
            </div>


            {{-- BUTTON --}}
            <div class="flex items-center justify-end gap-2 pt-2">

                <a
                    href="{{ route('supplier.index') }}"
                    class="px-4 py-2.5
                           rounded-xl
                           text-xs
                           font-semibold
                           text-gray-400
                           hover:text-white
                           hover:bg-white/5
                           transition"
                >
                    Batal
                </a>

                <button
                    type="submit"
                    class="bg-emerald-500
                           hover:bg-emerald-400
                           text-gray-950
                           px-4 py-2.5
                           rounded-xl
                           text-xs
                           font-semibold
                           transition"
                >
                    Simpan Supplier
                </button>

            </div>

        </form>

    </div>

</div>

@endsection