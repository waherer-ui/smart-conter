@extends('layouts.app')

@section('title', 'Edit Supplier')
@section('header', '🚚')

@section('content')

<div class="max-w-2xl mx-auto space-y-4">

    <div>
        <h1 class="text-lg font-semibold text-white">
            Edit Supplier
        </h1>

        <p class="text-xs text-gray-500 mt-1">
            Perbarui informasi supplier toko Anda.
        </p>
    </div>

    @if($errors->any())
        <div
            class="bg-red-500/10
                   border border-red-500/20
                   rounded-xl
                   px-4 py-3"
        >
            <ul class="space-y-1">
                @foreach($errors->all() as $error)
                    <li class="text-xs text-red-400">
                        • {{ $error }}
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <form
        action="{{ route('supplier.update', $supplier->id) }}"
        method="POST"
        class="bg-gray-800/80
               border border-white/10
               rounded-2xl
               shadow-xl
               overflow-hidden"
    >

        @csrf
        @method('PUT')

        <div class="p-5 space-y-4">

            {{-- NAMA --}}
            <div>
                <label class="block text-xs font-medium text-gray-400 mb-2">
                    Nama Supplier
                </label>

                <input
                    type="text"
                    name="name"
                    value="{{ old('name', $supplier->name) }}"
                    required
                    class="w-full
                           bg-gray-900
                           border border-white/10
                           rounded-xl
                           px-4 py-3
                           text-sm text-white
                           placeholder-gray-600
                           focus:outline-none
                           focus:border-emerald-500"
                    placeholder="Contoh: PT Sumber Jaya"
                >
            </div>

            {{-- TELEPON --}}
            <div>
                <label class="block text-xs font-medium text-gray-400 mb-2">
                    Nomor Telepon
                </label>

                <input
                    type="text"
                    name="phone"
                    value="{{ old('phone', $supplier->phone) }}"
                    class="w-full
                           bg-gray-900
                           border border-white/10
                           rounded-xl
                           px-4 py-3
                           text-sm text-white
                           placeholder-gray-600
                           focus:outline-none
                           focus:border-emerald-500"
                    placeholder="Contoh: 082xxxxxxxxx"
                >
            </div>

            {{-- ALAMAT --}}
            <div>
                <label class="block text-xs font-medium text-gray-400 mb-2">
                    Alamat
                </label>

                <textarea
                    name="address"
                    rows="3"
                    class="w-full
                           bg-gray-900
                           border border-white/10
                           rounded-xl
                           px-4 py-3
                           text-sm text-white
                           placeholder-gray-600
                           focus:outline-none
                           focus:border-emerald-500"
                    placeholder="Alamat supplier"
                >{{ old('address', $supplier->address) }}</textarea>
            </div>

            {{-- CATATAN --}}
            <div>
                <label class="block text-xs font-medium text-gray-400 mb-2">
                    Catatan
                </label>

                <textarea
                    name="notes"
                    rows="3"
                    class="w-full
                           bg-gray-900
                           border border-white/10
                           rounded-xl
                           px-4 py-3
                           text-sm text-white
                           placeholder-gray-600
                           focus:outline-none
                           focus:border-emerald-500"
                    placeholder="Catatan tambahan"
                >{{ old('notes', $supplier->notes) }}</textarea>
            </div>

        </div>

        <div
            class="px-5 py-4
                   border-t border-white/10
                   flex items-center
                   justify-between gap-2"
        >

            <a
                href="{{ route('supplier.show', $supplier->id) }}"
                class="px-4 py-2.5
                       rounded-xl
                       text-xs
                       font-semibold
                       text-gray-400
                       hover:text-white
                       hover:bg-white/5
                       transition"
            >
                ← Batal
            </a>

            <button
                type="submit"
                class="px-4 py-2.5
                       rounded-xl
                       text-xs
                       font-semibold
                       bg-emerald-500
                       hover:bg-emerald-400
                       text-gray-950
                       transition"
            >
                💾 Simpan Perubahan
            </button>

        </div>

    </form>

</div>

@endsection