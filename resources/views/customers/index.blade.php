@extends('layouts.app')

@section('title', 'Pelanggan')
@section('header', '👥')

@section('content')

<div class="space-y-4">

    {{-- HEADER --}}
    <div class="flex items-center justify-between gap-3">

        <div>
            <h2 class="text-lg font-bold text-white">
                Pelanggan
            </h2>

            <p class="text-xs text-gray-400">
                Daftar pelanggan toko
            </p>
        </div>

        <a
    href="{{ route('pelanggan.create') }}"="#"
            class="bg-emerald-500
                   hover:bg-emerald-400
                   text-gray-950
                   px-4 py-2
                   rounded-xl
                   text-xs
                   font-semibold
                   transition"
        >
            + Tambah
        </a>

    </div>


    {{-- INFO PAKET --}}
    @if($store)

        @php
            $customerLimit = $store->getLimit('max_customers');
            $customerCount = $customers->total();
        @endphp

        <div class="bg-gray-800/80
                    border border-white/10
                    rounded-2xl
                    px-4 py-3">

            <div class="flex items-center justify-between">

                <span class="text-xs text-gray-400">
                    Pelanggan tersimpan
                </span>

                <span class="text-xs font-semibold text-white">
                    {{ $customerCount }}
                    /
                    {{ $customerLimit === null ? '∞' : number_format($customerLimit, 0, ',', '.') }}
                </span>

            </div>

            @if($customerLimit !== null)

                <div class="mt-2 h-1.5 bg-gray-700 rounded-full overflow-hidden">

                    <div
                        class="h-full bg-emerald-500"
                        style="width: {{ min(100, ($customerCount / max(1, $customerLimit)) * 100) }}%"
                    ></div>

                </div>

            @endif

        </div>

    @endif


    {{-- DAFTAR PELANGGAN --}}
    <div class="bg-gray-800/80
                border border-white/10
                rounded-2xl
                overflow-hidden">

        @forelse($customers as $customer)

            <a
    href="{{ route('pelanggan.show', $customer->id) }}"
    class="block px-4 py-3
           border-b border-white/5
           last:border-b-0
           hover:bg-white/5
           transition"
>

                <div class="flex items-center justify-between gap-3">

                    <div class="min-w-0">

                        <div class="text-sm font-semibold text-white truncate">
                            {{ $customer->name }}
                        </div>

                        @if($customer->phone)

                            <div class="text-xs text-gray-400 mt-1">
                                📱 {{ $customer->phone }}
                            </div>

                        @endif

                    </div>

                    <span class="text-gray-500 text-sm">
                        ›
                    </span>

                </div>

            </a>

        @empty

            <div class="px-5 py-12 text-center">

                <div class="text-3xl mb-3">
                    👥
                </div>

                <div class="text-sm font-semibold text-white">
                    Belum ada pelanggan
                </div>

                <p class="text-xs text-gray-500 mt-1">
                    Tambahkan pelanggan untuk menyimpan data mereka.
                </p>

            </div>

        @endforelse

    </div>


    {{-- PAGINATION --}}
    @if($customers->hasPages())

        <div>
            {{ $customers->links() }}
        </div>

    @endif

</div>

@endsection