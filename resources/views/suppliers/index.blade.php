@extends('layouts.app')

@section('title', 'Supplier')
@section('header', '🚚')

@section('content')

<div class="space-y-4">

    {{-- HEADER --}}
    <div class="flex items-center justify-between gap-3">

        <div>
            <h1 class="text-lg font-semibold text-white">
                Supplier
            </h1>

            <p class="text-xs text-gray-500 mt-1">
                Kelola data supplier toko Anda.
            </p>
        </div>

        <a
            href="{{ route('supplier.create') }}"
            class="inline-flex items-center gap-2
                   bg-emerald-500
                   hover:bg-emerald-400
                   text-gray-950
                   px-3 py-2
                   rounded-xl
                   text-xs
                   font-semibold
                   transition
                   whitespace-nowrap"
        >
            <span>＋</span>
            <span>Tambah Supplier</span>
        </a>

    </div>


    {{-- DAFTAR SUPPLIER --}}
    <div
        class="bg-gray-800/80
               border border-white/10
               rounded-2xl
               shadow-xl
               overflow-hidden"
    >

        {{-- HEADER LIST --}}
        <div
            class="px-4 py-3
                   border-b border-white/10"
        >
            <p class="text-xs text-gray-400">
                {{ $suppliers->total() }} supplier terdaftar
            </p>
        </div>


        {{-- LIST --}}
        @forelse($suppliers as $supplier)

          <a
    href="{{ route('supplier.show', $supplier->id) }}"
    class="block
           px-4 py-4
           border-b border-white/5
           last:border-b-0
           hover:bg-white/[0.03]
           transition"
>

    <div class="flex items-center justify-between gap-3">

        {{-- INFO SUPPLIER --}}
        <div class="min-w-0">

            <h3 class="text-sm font-semibold text-white truncate">
                {{ $supplier->name }}
            </h3>

            @if($supplier->phone)
                <p class="text-xs text-gray-500 mt-1">
                    📱 {{ $supplier->phone }}
                </p>
            @endif

            @if($supplier->address)
                <p class="text-xs text-gray-500 mt-1 truncate">
                    📍 {{ $supplier->address }}
                </p>
            @endif

        </div>

    </div>

</a>

        @empty

            <div class="px-4 py-12 text-center">

                <div class="text-3xl mb-3">
                    🚚
                </div>

                <h3
                    class="text-sm
                           font-semibold
                           text-white"
                >
                    Belum ada supplier
                </h3>

                <p
                    class="text-xs
                           text-gray-500
                           mt-2"
                >
                    Tambahkan supplier untuk mulai mencatat pemasok toko Anda.
                </p>

            </div>

        @endforelse

    </div>


    {{-- PAGINATION --}}
    @if($suppliers->hasPages())

        <div>
            {{ $suppliers->links() }}
        </div>

    @endif

</div>

@endsection