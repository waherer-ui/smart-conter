@extends('layouts.app')

@section('header', 'Kontrol Manajemen & Kasir')

@section('content')
<div class="space-y-6">
    
    {{-- Notifikasi Sukses --}}
    @if(session('success'))
        <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 px-4 py-3 rounded-xl text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Notifikasi Error --}}
    @if(session('error'))
        <div class="bg-rose-500/10 border border-rose-500/30 text-rose-400 px-4 py-3 rounded-xl text-sm">
            {{ session('error') }}
        </div>
    @endif

    {{-- Form Tambah Akun --}}
    <div class="bg-gray-800/80 border border-white/10 rounded-2xl p-6 shadow-xl backdrop-blur-md">
        <h3 class="text-lg font-semibold text-white mb-4">Tambah Akun Baru (Admin / Kasir)</h3>
        
        <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Nama Lengkap</label>
                <input type="text" name="name" class="w-full bg-gray-900 border border-white/10 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Email</label>
                <input type="email" name="email" class="w-full bg-gray-900 border border-white/10 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Password</label>
                <input type="password" name="password" class="w-full bg-gray-900 border border-white/10 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Hak Akses (Role)</label>
                <select name="role" class="w-full bg-gray-900 border border-white/10 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                    <option value="kasir">Kasir</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 text-white font-medium px-5 py-2.5 rounded-xl text-sm transition shadow-lg w-full sm:w-auto">
                Simpan Akun
            </button>
        </form>
    </div>

    {{-- Daftar Akun --}}
    <div class="bg-gray-800/80 border border-white/10 rounded-2xl p-6 shadow-xl backdrop-blur-md">
        <h3 class="text-lg font-semibold text-white mb-1">Daftar Akun Pengguna / Hak Akses</h3>
        <p class="text-sm text-gray-400 mb-4">Kelola akun yang bisa mengakses sistem Kasir dan Admin.</p>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-white/10 text-xs text-gray-400 uppercase tracking-wider">
                        <th class="py-3 px-4">Nama</th>
                        <th class="py-3 px-4">Email</th>
                        <th class="py-3 px-4">Role</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5 text-sm">
                    @isset($users)
                        @foreach($users as $u)
                        <tr class="hover:bg-white/5 transition">
                            <td class="py-3.5 px-4 font-medium text-white">{{ $u->name }}</td>
                            <td class="py-3.5 px-4 text-gray-300">{{ $u->email }}</td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $u->role == 'admin' ? 'bg-rose-500/20 text-rose-400 border border-rose-500/30' : 'bg-sky-500/20 text-sky-400 border border-sky-500/30' }}">
                                    {{ ucfirst($u->role) }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <form action="{{ route('admin.users.destroy', $u->id) }}" method="POST" onsubmit="return confirm('Yakin ingin hapus akun ini?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-rose-600/20 hover:bg-rose-600 text-rose-400 hover:text-white px-3 py-1.5 rounded-lg text-xs font-medium transition border border-rose-500/30">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    @endisset
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
