@extends('layouts.app')

@section('title', 'Toko KasirKU')
@section('header', 'Toko')

@section('content')

<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

        <div>
            <h1 class="text-2xl font-bold text-white">
                Toko KasirKU
            </h1>

            <p class="text-sm text-gray-400 mt-1">
                Pantau seluruh toko yang terdaftar di platform KasirKU.
            </p>
        </div>

        <a
            href="{{ route('admin-kasirku.dashboard') }}"
            class="inline-flex items-center justify-center gap-2
                   px-4 py-2 rounded-xl
                   bg-gray-700 hover:bg-gray-600
                   text-white text-sm font-medium transition"
        >
            ← Dashboard
        </a>

    </div>


    {{-- TABEL TOKO --}}
    <div class="bg-gray-800/80 border border-white/10 rounded-2xl shadow-xl overflow-hidden">

        <div class="overflow-x-auto">

            <table class="w-full text-sm text-left">

                <thead class="bg-gray-900/70 border-b border-white/10">
                    <tr>

                        <th class="px-5 py-4 text-gray-400 font-medium">
                            Toko
                        </th>

                        <th class="px-5 py-4 text-gray-400 font-medium">
                            Pemilik
                        </th>

                        <th class="px-5 py-4 text-gray-400 font-medium">
                            Pengguna
                        </th>

                        <th class="px-5 py-4 text-gray-400 font-medium">
                            Paket
                        </th>

                        <th class="px-5 py-4 text-gray-400 font-medium">
                            Status
                        </th>

                        <th class="px-5 py-4 text-gray-400 font-medium">
                            Terdaftar
                        </th>

                    </tr>
                </thead>

                <tbody class="divide-y divide-white/5">

                    @forelse($stores as $store)

                        <tr class="hover:bg-white/5 transition">

                            {{-- TOKO --}}
                            <td class="px-5 py-4">

                                <div class="flex items-center gap-3">

                                    <div class="w-9 h-9 rounded-xl
                                                bg-blue-500/10
                                                border border-blue-500/20
                                                flex items-center justify-center
                                                text-blue-400">
                                        🏪
                                    </div>

                                    <div>
                                        <p class="text-white font-medium">
                                            {{ $store->name }}
                                        </p>

                                        <p class="text-xs text-gray-500">
                                            ID #{{ $store->id }}
                                        </p>
                                    </div>

                                </div>

                            </td>


                            {{-- PEMILIK --}}
                            <td class="px-5 py-4">

                                @if($store->owner)

                                    <div>
                                        <p class="text-gray-200">
                                            {{ $store->owner->name }}
                                        </p>

                                        <p class="text-xs text-gray-500">
                                            {{ $store->owner->email }}
                                        </p>
                                    </div>

                                @else

                                    <span class="text-gray-500">
                                        Tidak ditemukan
                                    </span>

                                @endif

                            </td>


                            {{-- JUMLAH PENGGUNA --}}
                            <td class="px-5 py-4 text-gray-300">

                                {{ $store->users->count() }}

                            </td>


                          {{-- PAKET --}}
                          <td class="px-5 py-4">
                          
                              @if($store->owner?->subscription?->plan)

                              <span class="inline-flex px-2.5 py-1 rounded-lg
                                           bg-indigo-500/10
                                           text-indigo-400 text-xs font-medium">
                                  {{ $store->owner->subscription->plan->name }}
                              </span>
                          
                          @else
                          
                              <span class="text-gray-500">
                                  Belum ada paket
                              </span>
                          
                          @endif
                          
                          </td>
                          
                          
                          {{-- STATUS --}}
                          <td class="px-5 py-4">
                          
                          @if($store->owner?->subscription?->status === 'active')
                          
                              <span class="inline-flex px-2.5 py-1 rounded-lg
                                           bg-emerald-500/10
                                           text-emerald-400 text-xs font-medium">
                                  Aktif
                              </span>
                          
                          @else
                          
                              <span class="text-gray-500">
                                  Tidak aktif
                              </span>
                          
                          @endif
                          
                          </td>
                          
                          
                          {{-- TANGGAL --}}
                          <td class="px-5 py-4 text-gray-400 whitespace-nowrap">
                          
                              {{ $store->created_at?->format('d M Y') }}
                          
                          </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="6"
                                class="px-5 py-10 text-center text-gray-500"
                            >
                                Belum ada toko.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- PAGINATION --}}
        @if($stores->hasPages())

            <div class="px-5 py-4 border-t border-white/10">
                {{ $stores->links() }}
            </div>

        @endif

    </div>

</div>

@endsection