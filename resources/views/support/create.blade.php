@extends('layouts.app')

@section('title', 'Buat Tiket Bantuan')

@section('content')

<div class="max-w-3xl mx-auto px-4 py-6">{{-- Header --}}
<div class="flex items-center gap-3 mb-6">
    <a
        href="{{ route('support.index') }}"
        class="w-9 h-9 shrink-0 flex items-center justify-center
               rounded-xl bg-gray-800 border border-gray-700
               text-gray-300 hover:text-white hover:border-gray-600 transition"
    >
        ←
    </a>

    <div>
        <h1 class="text-xl sm:text-2xl font-bold text-white">
            Buat Tiket Bantuan
        </h1>
        <p class="text-sm text-gray-400 mt-1">
            Jelaskan masalah atau kebutuhan Anda kepada tim KasirKU.
        </p>
    </div>
</div>

{{-- Validation Error --}}
@if($errors->any())
    <div class="mb-5 p-4 rounded-xl
                bg-red-500/10 border border-red-500/20">
        <p class="text-sm font-semibold text-red-400 mb-2">
            Periksa kembali data berikut:
        </p>

        <ul class="space-y-1 text-sm text-red-300">
            @foreach($errors->all() as $error)
                <li>• {{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- Form --}}
<form
    action="{{ route('support.store') }}"
    method="POST"
    class="bg-gray-800/80 border border-gray-700 rounded-2xl p-4 sm:p-6"
>
    @csrf

    <div class="space-y-5">

        {{-- Toko --}}
        <div>
            <label for="store_id" class="block text-sm font-medium text-gray-200 mb-2">
                Toko
            </label>

            <select
                id="store_id"
                name="store_id"
                required
                class="w-full rounded-xl bg-gray-900 border border-gray-700
                       text-white text-sm px-4 py-3
                       focus:outline-none focus:ring-2 focus:ring-emerald-500
                       focus:border-transparent"
            >
                <option value="">Pilih toko</option>

                @foreach($stores as $store)
                    <option
                        value="{{ $store->id }}"
                        {{ old('store_id') == $store->id ? 'selected' : '' }}
                    >
                        {{ $store->name }}
                    </option>
                @endforeach
            </select>

            <p class="text-xs text-gray-500 mt-2">
                Pilih toko yang terkait dengan masalah Anda.
            </p>
        </div>

        {{-- Kategori --}}
        <div>
            <label for="category" class="block text-sm font-medium text-gray-200 mb-2">
                Kategori
            </label>

            <select
                id="category"
                name="category"
                required
                class="w-full rounded-xl bg-gray-900 border border-gray-700
                       text-white text-sm px-4 py-3
                       focus:outline-none focus:ring-2 focus:ring-emerald-500
                       focus:border-transparent"
            >
                <option value="">Pilih kategori</option>
                <option value="akun" {{ old('category') === 'akun' ? 'selected' : '' }}>
                    Akun
                </option>
                <option value="teknis" {{ old('category') === 'teknis' ? 'selected' : '' }}>
                    Teknis
                </option>
                <option value="pembayaran" {{ old('category') === 'pembayaran' ? 'selected' : '' }}>
                    Pembayaran
                </option>
                <option value="langganan" {{ old('category') === 'langganan' ? 'selected' : '' }}>
                    Langganan
                </option>
                <option value="fitur" {{ old('category') === 'fitur' ? 'selected' : '' }}>
                    Fitur
                </option>
                <option value="lainnya" {{ old('category') === 'lainnya' ? 'selected' : '' }}>
                    Lainnya
                </option>
            </select>
        </div>

        {{-- Prioritas --}}
        <div>
            <label for="priority" class="block text-sm font-medium text-gray-200 mb-2">
                Prioritas
            </label>

            <select
                id="priority"
                name="priority"
                required
                class="w-full rounded-xl bg-gray-900 border border-gray-700
                       text-white text-sm px-4 py-3
                       focus:outline-none focus:ring-2 focus:ring-emerald-500
                       focus:border-transparent"
            >
                <option value="low" {{ old('priority', 'normal') === 'low' ? 'selected' : '' }}>
                    Rendah
                </option>

                <option value="normal" {{ old('priority', 'normal') === 'normal' ? 'selected' : '' }}>
                    Normal
                </option>

                <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>
                    Tinggi
                </option>

                <option value="urgent" {{ old('priority') === 'urgent' ? 'selected' : '' }}>
                    Mendesak
                </option>
            </select>

            <p class="text-xs text-gray-500 mt-2">
                Gunakan prioritas Mendesak hanya untuk masalah yang benar-benar menghambat operasional.
            </p>
        </div>

        {{-- Subjek --}}
        <div>
            <label for="subject" class="block text-sm font-medium text-gray-200 mb-2">
                Subjek
            </label>

            <input
                type="text"
                id="subject"
                name="subject"
                value="{{ old('subject') }}"
                required
                maxlength="255"
                placeholder="Contoh: Tidak bisa mencetak struk"
                class="w-full rounded-xl bg-gray-900 border border-gray-700
                       text-white placeholder-gray-600 text-sm px-4 py-3
                       focus:outline-none focus:ring-2 focus:ring-emerald-500
                       focus:border-transparent"
            >
        </div>

        {{-- Deskripsi --}}
        <div>
            <label for="description" class="block text-sm font-medium text-gray-200 mb-2">
                Jelaskan masalah
            </label>

            <textarea
                id="description"
                name="description"
                rows="6"
                required
                placeholder="Ceritakan masalah yang Anda alami secara detail..."
                class="w-full rounded-xl bg-gray-900 border border-gray-700
                       text-white placeholder-gray-600 text-sm px-4 py-3
                       resize-y
                       focus:outline-none focus:ring-2 focus:ring-emerald-500
                       focus:border-transparent"
            >{{ old('description') }}</textarea>

            <p class="text-xs text-gray-500 mt-2">
                Semakin jelas informasi yang diberikan, semakin mudah tim kami membantu.
            </p>
        </div>

    </div>

    {{-- Actions --}}
    <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 mt-6 pt-5 border-t border-gray-700">

        <a
            href="{{ route('support.index') }}"
            class="w-full sm:w-auto text-center px-5 py-3 rounded-xl
                   bg-gray-700 hover:bg-gray-600
                   text-gray-200 text-sm font-semibold transition"
        >
            Batal
        </a>

        <button
            type="submit"
            class="w-full sm:w-auto px-5 py-3 rounded-xl
                   bg-emerald-600 hover:bg-emerald-500
                   text-white text-sm font-semibold transition"
        >
            Kirim Tiket
        </button>

    </div>

</form>

</div>@endsection