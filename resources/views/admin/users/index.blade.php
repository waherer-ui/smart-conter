@extends('layouts.app')

@section('title', 'Manajemen Pengguna')

@section('header', 'Manajemen Pengguna')

@section('content')

<div class="space-y-6">

    {{-- ========================================================= --}}
    {{-- PESAN SUCCESS --}}
    {{-- ========================================================= --}}

    @if(session('success'))
        <div class="rounded-xl border border-green-500/30 bg-green-500/10 px-4 py-3 text-green-300">
            <div class="flex items-center gap-2">
                <span>✅</span>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif


    {{-- ========================================================= --}}
    {{-- PESAN ERROR --}}
    {{-- ========================================================= --}}

    @if(session('error'))
        <div class="rounded-xl border border-red-500/30 bg-red-500/10 px-4 py-3 text-red-300">
            <div class="flex items-center gap-2">
                <span>⚠️</span>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif


    {{-- ========================================================= --}}
    {{-- ERROR VALIDASI --}}
    {{-- ========================================================= --}}

    @if($errors->any())

        <div class="rounded-xl border border-yellow-500/30 bg-yellow-500/10 px-4 py-3 text-yellow-300">

            <div class="font-semibold mb-2">
                ⚠️ Periksa data berikut:
            </div>

            <ul class="list-disc list-inside text-sm space-y-1">

                @foreach($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif



    {{-- ========================================================= --}}
    {{-- FORM TAMBAH AKUN --}}
    {{-- ========================================================= --}}

    <div class="rounded-2xl border border-white/10 bg-gray-800 shadow-xl">

        <div class="border-b border-white/10 px-5 py-4">

            <h2 class="text-lg font-semibold text-white">
                Tambah Akun Baru
            </h2>

            <p class="mt-1 text-sm text-gray-400">
                Buat akun Administrator atau Kasir untuk mengakses Smart POS.
            </p>

        </div>


        <div class="p-5">

            <form
                action="{{ route('admin.users.store') }}"
                method="POST"
                class="space-y-5"
            >

                @csrf


                {{-- ================================================= --}}
                {{-- NAMA --}}
                {{-- ================================================= --}}

                <div>

                    <label
                        for="name"
                        class="mb-2 block text-sm font-medium text-gray-300"
                    >
                        Nama Lengkap
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name') }}"
                        required
                        maxlength="255"
                        autocomplete="name"
                        placeholder="Contoh: Budi Santoso"
                        class="w-full rounded-xl border border-white/10 bg-gray-900 px-4 py-3 text-white placeholder-gray-500 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20"
                    >

                </div>



                {{-- ================================================= --}}
                {{-- EMAIL --}}
                {{-- ================================================= --}}

                <div>

                    <label
                        for="email"
                        class="mb-2 block text-sm font-medium text-gray-300"
                    >
                        Email
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        maxlength="255"
                        autocomplete="email"
                        placeholder="contoh@email.com"
                        class="w-full rounded-xl border border-white/10 bg-gray-900 px-4 py-3 text-white placeholder-gray-500 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20"
                    >

                </div>



                {{-- ================================================= --}}
                {{-- PASSWORD --}}
                {{-- ================================================= --}}

                <div>

                    <label
                        for="password"
                        class="mb-2 block text-sm font-medium text-gray-300"
                    >
                        Password
                    </label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        minlength="6"
                        autocomplete="new-password"
                        placeholder="Minimal 6 karakter"
                        class="w-full rounded-xl border border-white/10 bg-gray-900 px-4 py-3 text-white placeholder-gray-500 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20"
                    >

                </div>



                {{-- ================================================= --}}
                {{-- KONFIRMASI PASSWORD --}}
                {{-- ================================================= --}}

                <div>

                    <label
                        for="password_confirmation"
                        class="mb-2 block text-sm font-medium text-gray-300"
                    >
                        Konfirmasi Password
                    </label>

                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        required
                        minlength="6"
                        autocomplete="new-password"
                        placeholder="Ulangi password"
                        class="w-full rounded-xl border border-white/10 bg-gray-900 px-4 py-3 text-white placeholder-gray-500 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20"
                    >

                </div>



                {{-- ================================================= --}}
                {{-- ROLE --}}
                {{-- ================================================= --}}

                <div>

                    <label
                        for="role"
                        class="mb-2 block text-sm font-medium text-gray-300"
                    >
                        Hak Akses
                    </label>

                    <select
                        id="role"
                        name="role"
                        required
                        class="w-full rounded-xl border border-white/10 bg-gray-900 px-4 py-3 text-white outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20"
                    >

                        <option
                            value="kasir"
                            {{ old('role', 'kasir') === 'kasir' ? 'selected' : '' }}
                        >
                            Kasir
                        </option>

                        <option
                            value="admin"
                            {{ old('role') === 'admin' ? 'selected' : '' }}
                        >
                            Administrator
                        </option>

                    </select>

                    <p class="mt-2 text-xs text-gray-500">
                        Kasir dapat melakukan transaksi dan mengelola produk.
                        Administrator memiliki akses penuh ke sistem.
                    </p>

                </div>



                {{-- ================================================= --}}
                {{-- BUTTON --}}
                {{-- ================================================= --}}

                <div class="pt-2">

                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-600/20 transition hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-gray-800"
                    >

                        <span>➕</span>

                        <span>
                            Simpan Akun
                        </span>

                    </button>

                </div>

            </form>

        </div>

    </div>



    {{-- ========================================================= --}}
    {{-- DAFTAR USER --}}
    {{-- ========================================================= --}}

    <div class="rounded-2xl border border-white/10 bg-gray-800 shadow-xl">

        <div class="border-b border-white/10 px-5 py-4">

            <h2 class="text-lg font-semibold text-white">
                Daftar Pengguna
            </h2>

            <p class="mt-1 text-sm text-gray-400">
                Daftar akun yang memiliki akses ke Smart POS.
            </p>

        </div>


        <div class="overflow-x-auto">

            <table class="min-w-full divide-y divide-white/10">

                <thead class="bg-gray-900/50">

                    <tr>

                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-400">
                            Nama
                        </th>

                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-400">
                            Email
                        </th>

                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-400">
                            Role
                        </th>

                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-400">
                            Dibuat
                        </th>

                        <th class="px-5 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-400">
                            Aksi
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-white/10">

                    @forelse($users as $u)

                        <tr class="transition hover:bg-white/[0.03]">

                            {{-- NAMA --}}

                            <td class="whitespace-nowrap px-5 py-4">

                                <div class="flex items-center gap-3">

                                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-indigo-600/20 text-sm font-bold text-indigo-300">

                                        {{ strtoupper(substr($u->name, 0, 1)) }}

                                    </div>

                                    <div>

                                        <div class="font-medium text-white">
                                            {{ $u->name }}
                                        </div>

                                        @if((int) $u->id === (int) session('user_id'))

                                            <div class="text-xs text-green-400">
                                                ● Sedang login
                                            </div>

                                        @endif

                                    </div>

                                </div>

                            </td>


                            {{-- EMAIL --}}

                            <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-300">

                                {{ $u->email }}

                            </td>


                            {{-- ROLE --}}

                            <td class="whitespace-nowrap px-5 py-4">

                                @if($u->role === 'admin')

                                    <span class="inline-flex items-center rounded-full border border-red-400/20 bg-red-500/10 px-3 py-1 text-xs font-semibold text-red-300">

                                        🔐 Administrator

                                    </span>

                                @else

                                    <span class="inline-flex items-center rounded-full border border-blue-400/20 bg-blue-500/10 px-3 py-1 text-xs font-semibold text-blue-300">

                                        🛒 Kasir

                                    </span>

                                @endif

                            </td>


                            {{-- CREATED AT --}}

                            <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-400">

                                {{ $u->created_at ? $u->created_at->format('d/m/Y H:i') : '-' }}

                            </td>


                            {{-- AKSI --}}

                            <td class="whitespace-nowrap px-5 py-4 text-right">

                                @if((int) $u->id === (int) session('user_id'))

                                    <span class="text-xs text-gray-500">
                                        Akun aktif
                                    </span>

                                @else

                                    <form
                                        action="{{ route('admin.users.destroy', $u->id) }}"
                                        method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus akun {{ addslashes($u->name) }}?')"
                                        class="inline"
                                    >

                                        @csrf

                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="rounded-lg bg-red-600/10 px-3 py-2 text-xs font-semibold text-red-400 transition hover:bg-red-600 hover:text-white"
                                        >

                                            🗑️ Hapus

                                        </button>

                                    </form>

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="5"
                                class="px-5 py-10 text-center text-sm text-gray-500"
                            >

                                Belum ada akun pengguna.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection