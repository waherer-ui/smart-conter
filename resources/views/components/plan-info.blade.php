@props(['activeStore'])

@php
    $plan = $activeStore?->currentPlan();

    $productLimit = $activeStore?->getLimit('max_products');
    $staffLimit = $activeStore?->getLimit('max_staff');
    $storeLimit = $activeStore?->getLimit('max_stores');

    $productCount = $activeStore?->products()->count() ?? 0;
    $staffCount = $activeStore?->users()->count() ?? 0;
    $storeCount = $activeStore?->owner?->stores()->count() ?? 0;
@endphp

@if($activeStore && $plan)

    <div class="bg-gray-800/80 border border-white/10
                rounded-2xl p-4 shadow-xl">

        {{-- HEADER PAKET --}}
        <div class="flex items-center justify-between gap-3 mb-4">

            <div>
                <p class="text-xs text-gray-400">
                    📦 Paket Anda
                </p>

                <h3 class="text-lg font-bold text-white">
                    {{ strtoupper($plan->name) }}
                </h3>
            </div>

            <span class="px-3 py-1 rounded-full
                         text-xs font-semibold
                         bg-emerald-500/10
                         text-emerald-400
                         border border-emerald-500/20">
                AKTIF
            </span>

        </div>


        {{-- PRODUK --}}
        <div class="mb-4">

            <div class="flex items-center justify-between
                        text-xs mb-1">

                <span class="text-gray-400">
                    Produk
                </span>

                <span class="text-white font-medium">
                    {{ number_format($productCount) }}
                    /
                    {{ $productLimit === null
                        ? '∞'
                        : number_format($productLimit) }}
                </span>

            </div>

            @if($productLimit !== null)

                @php
                    $productPercent = $productLimit > 0
                        ? min(
                            100,
                            ($productCount / $productLimit) * 100
                        )
                        : 100;
                @endphp

                <div class="h-1.5 bg-gray-700
                            rounded-full overflow-hidden">

                    <div
                        class="h-full bg-emerald-500
                               rounded-full"
                        style="width: {{ $productPercent }}%"
                    ></div>

                </div>

            @endif

        </div>


        {{-- TOKO --}}
        <div class="flex items-center justify-between
                    py-2 border-t border-white/5">

            <span class="text-xs text-gray-400">
                Toko
            </span>

            <span class="text-sm text-white">

                {{ $storeCount }}
                /
                {{ $storeLimit === null
                    ? '∞'
                    : $storeLimit }}

                @if($storeLimit !== null && $storeCount > $storeLimit)
                    <span class="text-amber-400 ml-1">
                        ⚠
                    </span>
                @endif

            </span>

        </div>


        {{-- STAFF --}}
        <div class="flex items-center justify-between
                    py-2 border-t border-white/5">

            <span class="text-xs text-gray-400">
                Staff / Kasir
            </span>

            <span class="text-sm text-white">

                {{ $staffCount }}
                /
                {{ $staffLimit === null
                    ? '∞'
                    : $staffLimit }}

                @if($staffLimit !== null && $staffCount > $staffLimit)
                    <span class="text-amber-400 ml-1">
                        ⚠
                    </span>
                @endif

            </span>

        </div>


        {{-- UPGRADE --}}
        @if($plan->slug !== 'premium')

            <a
                <a
                 href="{{ route('paket') }}"
                class="mt-4 block w-full text-center
                       px-4 py-2.5 rounded-xl
                       bg-emerald-500
                       hover:bg-emerald-400
                       text-gray-950
                       font-semibold text-sm
                       transition"
            >
                Upgrade Paket
            </a>

        @endif

    </div>

@endif