@extends('layouts.app')

@section('title', 'Pengaturan Admin KasirKU')
@section('header', 'Pengaturan')

@section('content')

<div class="max-w-3xl mx-auto space-y-6">

    {{-- HEADER --}}
    <div>
        <h1 class="text-2xl font-bold text-white">
            Pengaturan Admin KasirKU
        </h1>

        <p class="text-sm text-gray-400 mt-1">
            Kelola informasi akun dan keamanan Admin Platform.
        </p>
    </div>


    {{-- PROFIL --}}
    <div class="bg-gray-800/80 border border-white/10 rounded-2xl p-5 shadow-xl">

        <div class="mb-5">
            <h2 class="text-lg font-semibold text-white">
                Profil Admin Platform
            </h2>

            <p class="text-sm text-gray-400 mt-1">
                Ubah nama dan email akun Admin KasirKU.
            </p>
        </div>

        <form
            action="{{ route('admin-kasirku.settings.profile') }}"
            method="POST"
            class="space-y-4"
        >
            @csrf
            @method('PUT')

            {{-- NAMA --}}
            <div>
                <label class="block text-sm text-gray-300 mb-1">
                    Nama
                </label>

                <input
                    type="text"
                    name="name"
                    value="{{ old('name', $user->name) }}"
                    required
                    class="w-full bg-gray-900 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
                >

                @error('name')
                    <p class="text-red-400 text-xs mt-1">
                        {{ $message }}
                    </p>
                @enderror
            </div>


            {{-- EMAIL --}}
            <div>
                <label class="block text-sm text-gray-300 mb-1">
                    Email
                </label>

                <input
                    type="email"
                    name="email"
                    value="{{ old('email', $user->email) }}"
                    required
                    class="w-full bg-gray-900 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
                >

                @error('email')
                    <p class="text-red-400 text-xs mt-1">
                        {{ $message }}
                    </p>
                @enderror
            </div>


            <div class="flex justify-end pt-2">
                <button
                    type="submit"
                    class="bg-emerald-600 hover:bg-emerald-500 text-white font-semibold px-5 py-3 rounded-xl transition"
                >
                    Simpan Profil
                </button>
            </div>

        </form>

    </div>


    {{-- PASSWORD --}}
    <div class="bg-gray-800/80 border border-white/10 rounded-2xl p-5 shadow-xl">

        <div class="mb-5">
            <h2 class="text-lg font-semibold text-white">
                Keamanan Akun
            </h2>

            <p class="text-sm text-gray-400 mt-1">
                Ganti password Admin KasirKU secara berkala untuk menjaga keamanan akun.
            </p>
        </div>

        <form
            action="{{ route('admin-kasirku.settings.password') }}"
            method="POST"
            class="space-y-4"
        >
            @csrf
            @method('PUT')

            {{-- PASSWORD LAMA --}}
            <div>
                <label class="block text-sm text-gray-300 mb-1">
                    Password Saat Ini
                </label>

                <input
                    type="password"
                    name="current_password"
                    required
                    autocomplete="current-password"
                    class="w-full bg-gray-900 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
                >

                @error('current_password')
                    <p class="text-red-400 text-xs mt-1">
                        {{ $message }}
                    </p>
                @enderror
            </div>


            {{-- PASSWORD BARU --}}
            <div>
                <label class="block text-sm text-gray-300 mb-1">
                    Password Baru
                </label>

                <input
                    type="password"
                    name="password"
                    required
                    minlength="8"
                    autocomplete="new-password"
                    class="w-full bg-gray-900 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
                >

                @error('password')
                    <p class="text-red-400 text-xs mt-1">
                        {{ $message }}
                    </p>
                @enderror

                <p class="text-xs text-gray-500 mt-1">
                    Minimal 8 karakter.
                </p>
            </div>


            {{-- KONFIRMASI --}}
            <div>
                <label class="block text-sm text-gray-300 mb-1">
                    Konfirmasi Password Baru
                </label>

                <input
                    type="password"
                    name="password_confirmation"
                    required
                    minlength="8"
                    autocomplete="new-password"
                    class="w-full bg-gray-900 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
                >
            </div>


            <div class="flex justify-end pt-2">
                <button
                    type="submit"
                    class="bg-emerald-600 hover:bg-emerald-500 text-white font-semibold px-5 py-3 rounded-xl transition"
                >
                    Ganti Password
                </button>
            </div>

        </form>

    </div>

</div>

@endsection