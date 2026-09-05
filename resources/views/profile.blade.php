@extends('layouts.app')

@section('header', 'Pengaturan Profil')

@section('content')

<div class="max-w-xl mx-auto space-y-6 pb-12">

    @if(session('success'))
        <div class="bg-emerald-500/10 border border-emerald-500/35 text-emerald-400 px-4 py-3 rounded-xl text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-gray-800/80 border border-white/10 rounded-2xl p-6 shadow-xl backdrop-blur-md">
        
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')

            {{-- PREVIEW FOTO --}}
            <div class="flex flex-col items-center mb-6">
                <div class="w-20 h-20 rounded-full overflow-hidden bg-gray-700 border border-white/10 mb-3 flex items-center justify-center">
                    @if($user->avatar ?? false)
                        <img src="{{ asset('avatars/' . $user->avatar) }}" alt="Avatar" class="w-full h-full object-cover">
                    @else
                        <span class="text-2xl text-gray-400">👤</span>
                    @endif
                </div>

                <label class="block text-xs font-medium text-gray-300 mb-1">
                    Ganti Foto Profil
                </label>
                <input type="file" name="avatar" accept="image/*" class="w-full text-xs text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-500 cursor-pointer">
            </div>

            {{-- NAMA --}}
            <div>
                <label class="block text-xs font-medium text-gray-300 mb-1">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full bg-gray-900 border border-white/10 rounded-xl px-4 py-2.5 text-white text-sm outline-none focus:ring-2 focus:ring-indigo-500" required>
            </div>

            {{-- EMAIL --}}
            <div>
                <label class="block text-xs font-medium text-gray-300 mb-1">Email / Username</label>
                <input type="text" value="{{ $user->email }}" class="w-full bg-gray-900/50 border border-white/10 rounded-xl px-4 py-2.5 text-gray-400 text-sm outline-none cursor-not-allowed" disabled>
            </div>

            <hr class="border-white/10 my-4">

            {{-- PASSWORD BARU (Opsional) --}}
            <div>
                <label class="block text-xs font-medium text-gray-300 mb-1">Password Baru (Kosongkan jika tidak ingin diubah)</label>
                <input type="password" name="password" placeholder="••••••••" class="w-full bg-gray-900 border border-white/10 rounded-xl px-4 py-2.5 text-white text-sm outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 text-white px-5 py-2.5 rounded-xl text-xs font-medium transition shadow">
                    Simpan Perubahan
                </button>
            </div>

        </form>

    </div>

</div>

@endsection
