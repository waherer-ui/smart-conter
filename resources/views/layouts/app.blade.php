<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-900">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Smart POS - @yield('title', 'Dashboard')
    </title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>


<body class="min-h-screen bg-gray-900 text-gray-200 flex flex-col">

    {{-- Ambil data user aktif secara dinamis untuk seluruh layout --}}
    @php
        $layoutUser = session('logged_in') ? \App\Models\User::find(session('user_id')) : null;
        $avatarUrl = ($layoutUser && $layoutUser->avatar) 
            ? asset('avatars/' . $layoutUser->avatar) 
            : 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=100';
    @endphp


    {{-- ========================================================= --}}
    {{-- NAVBAR GLOBAL --}}
    {{-- ========================================================= --}}

    <nav class="bg-gray-800 border-b border-white/10 sticky top-0 z-50">

        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <div class="flex h-16 items-center justify-between">


                {{-- ================================================= --}}
                {{-- LOGO --}}
                {{-- ================================================= --}}

                <div class="flex items-center">
                  
                    <a
                      href="{{ session('logged_in')
                          ? route('dashboard')
                          : route('login') }}"
                        class="text-white font-bold text-lg tracking-wide"
                    >
                        Toko Ku
                    </a>

                </div>


                {{-- ================================================= --}}
                {{-- DESKTOP --}}
                {{-- ================================================= --}}

                <div class="hidden md:flex items-center">

                        <div class="relative">


                           {{-- ===================================== --}}
{{-- PROFILE / GUEST BUTTON --}}
{{-- ===================================== --}}

@if(session('logged_in'))

    {{-- USER LOGIN --}}

    <button
        onclick="toggleUserDropdown()"
        type="button"
        class="flex items-center gap-3 rounded-xl px-3 py-2
               hover:bg-gray-700 transition
               focus:outline-none focus:ring-2
               focus:ring-indigo-500"
    >

        {{-- User Info --}}
        <div class="text-right">

            <div class="text-sm font-medium text-white">
                {{ session('username') }}
            </div>

            <div class="text-xs text-gray-400 uppercase">
                {{ session('user_role') }}
            </div>

        </div>

        {{-- Avatar --}}
        <img
            class="w-9 h-9 rounded-full border border-white/20 object-cover"
            src="{{ $avatarUrl }}"
            alt="Profile"
        >

        {{-- Arrow --}}
        <svg
            class="w-4 h-4 text-gray-400"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M19 9l-7 7-7-7"
            />
        </svg>

    </button>

@else

    {{-- GUEST / PENGUNJUNG --}}

    <button
        onclick="toggleUserDropdown()"
        type="button"
        class="flex items-center justify-center
               w-11 h-11 rounded-xl
               hover:bg-gray-700 transition
               focus:outline-none focus:ring-2
               focus:ring-indigo-500"
        aria-label="Menu"
    >

        {{-- Hamburger --}}
        <svg
            class="w-6 h-6 text-gray-300"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M4 6h16M4 12h16M4 18h16"
            />
        </svg>

    </button>

@endif
                            {{-- ===================================== --}}
                            {{-- PROFILE DROPDOWN --}}
                            {{-- ===================================== --}}

                            <div
                                id="user-dropdown"
                                class="hidden absolute right-0 mt-2 w-64
                                       rounded-2xl bg-gray-800
                                       border border-white/10
                                       shadow-2xl overflow-hidden z-50"
                            >


                                {{-- ================================= --}}
                                {{-- PROFILE HEADER --}}
                                {{-- ================================= --}}
                                @if(session('logged_in'))
                                <div class="px-4 py-4 bg-gray-800/80">

                                    <div class="flex items-center gap-3">

                                        {{-- Avatar Desktop Dropdown Header --}}
                                        <img
                                            class="w-11 h-11 rounded-full border border-white/20 object-cover"
                                            src="{{ $avatarUrl }}"
                                            alt="Profile"
                                        >

                                        <div class="min-w-0">

                                            <div class="font-semibold text-white truncate">
                                                {{ session('username') }}
                                            </div>

                                            <div class="text-xs text-gray-400 uppercase">
                                                {{ session('user_role') }}
                                            </div>

                                        </div>

                                    </div>

                                </div>
                                @endif


                                {{-- ================================= --}}
                                {{-- NAVIGASI --}}
                                {{-- ================================= --}}

                                <div class="border-t border-white/10 py-2">

                                    <div class="px-4 py-1.5 text-xs
                                                font-semibold text-gray-500 uppercase">
                                        Navigasi
                                    </div>

                                    {{-- Dashboard --}}
                                        <a
                                            href="{{ route('dashboard') }}"
                                            class="dropdown-link"
                                        >
                                            <span>🏠</span>
                                            <span>Dashboard</span>
                                        </a>

                                    {{-- Kasir --}}
                                    <a
                                        href="{{ route('kasir.index') }}"
                                        class="dropdown-link"
                                    >
                                        <span>🛒</span>
                                        <span>Kasir</span>
                                    </a>


                                    {{-- Produk --}}
                                    <a
                                        href="{{ route('produk.index') }}"
                                        class="dropdown-link"
                                    >
                                        <span>📦</span>
                                        <span>Produk</span>
                                    </a>


                                    {{-- Pengeluaran --}}
                                    <a
                                        href="{{ route('pengeluaran') }}"
                                        class="dropdown-link"
                                    >
                                        <span>💸</span>
                                        <span>Pengeluaran</span>
                                    </a>
                                    
                                    {{-- Laporan --}}
                                    <a
                                        href="{{ route('laporan') }}"
                                        class="dropdown-link"
                                    >
                                        <span>📊</span>
                                        <span>Laporan</span>
                                    </a>

                                    {{-- Riwayat --}}
                                    <a
                                        href="{{ route('riwayat') }}"
                                        class="dropdown-link"
                                    >
                                        <span>🧾</span>
                                        <span>Riwayat Transaksi</span>
                                    </a>

                                   @if(session('logged_in'))

                                  {{-- Profil Saya --}}
                                  <a
                                      href="{{ route('profil') }}"
                                      class="dropdown-link"
                                  >
                                      <span>👤</span>
                                      <span>Profil Saya</span>
                                  </a>
                              
                              @else
                              
                                  {{-- Masuk / Daftar --}}
                                  <a
                                      href="{{ route('login') }}"
                                      class="dropdown-link"
                                  >
                                      <span>🔐</span>
                                      <span>Masuk / Daftar</span>
                                  </a>
                              
                              @endif

                                </div>


                                {{-- ================================= --}}
                                {{-- MENU ADMIN --}}
                                {{-- ================================= --}}

                                @if(session('user_role') === 'admin')

                                    <div class="border-t border-white/10 py-2">

                                        <div class="px-4 py-1.5 text-xs
                                                    font-semibold text-gray-500 uppercase">
                                            Administrator
                                        </div>


                                        {{-- Manajemen Admin --}}
                                        <a
                                            href="{{ route('admin.index') }}"
                                            class="dropdown-link"
                                        >
                                            <span>👥</span>
                                            <span>Manajemen Admin</span>
                                        </a>

                                        {{-- Setting --}}
                                        <a
                                            href="{{ route('setting') }}"
                                            class="dropdown-link"
                                        >
                                            <span>⚙️</span>
                                            <span>Pengaturan</span>
                                        </a>

                                    </div>

                                @endif


                                {{-- ================================= --}}
                                {{-- LOGOUT --}}
                                {{-- ================================= --}}
                            @if(session('logged_in'))
                                <div class="border-t border-white/10 py-2">

                                    <a
                                        href="{{ route('logout') }}"
                                        class="dropdown-link text-red-400
                                               hover:bg-red-500/10
                                               hover:text-red-300"
                                    >
                                        <span>🚪</span>
                                        <span>Keluar</span>
                                    </a>

                                </div> @endif

                            </div>

                        </div>
                </div>


                {{-- ================================================= --}}
                {{-- MOBILE --}}
                {{-- ================================================= --}}

                <div class="md:hidden">

                   @if(session('logged_in'))

    {{-- USER LOGIN --}}

    <button
        onclick="toggleUserDropdown()"
        type="button"
        class="flex items-center gap-3 rounded-xl px-3 py-2
               hover:bg-gray-700 transition
               focus:outline-none focus:ring-2
               focus:ring-indigo-500"
    >

        {{-- User Info --}}
        <div class="text-right">

            <div class="text-sm font-medium text-white">
                {{ session('username') }}
            </div>

            <div class="text-xs text-gray-400 uppercase">
                {{ session('user_role') }}
            </div>

        </div>

        {{-- Avatar --}}
        <img
            class="w-9 h-9 rounded-full border border-white/20 object-cover"
            src="{{ $avatarUrl }}"
            alt="Profile"
        >

        {{-- Arrow --}}
        <svg
            class="w-4 h-4 text-gray-400"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M19 9l-7 7-7-7"
            />
        </svg>

    </button>

@else

    {{-- GUEST / PENGUNJUNG --}}

    <button
        onclick="toggleMobileMenu()"
        type="button"
        class="flex items-center justify-center
               w-11 h-11 rounded-xl
               hover:bg-gray-700 transition
               focus:outline-none focus:ring-2
               focus:ring-indigo-500"
        aria-label="Menu"
    >

        {{-- Hamburger --}}
        <svg
            class="w-6 h-6 text-gray-300"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M4 6h16M4 12h16M4 18h16"
            />
        </svg>

    </button>

@endif

                </div>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- MOBILE PROFILE MENU --}}
        {{-- ========================================================= --}}


            <div
                id="mobile-menu"
                class="hidden md:hidden absolute
                       top-16 left-0 right-0 px-4"
            >

                <div
                    class="bg-gray-800
                           border border-white/10
                           rounded-2xl
                           shadow-2xl
                           overflow-hidden"
                >


                    {{-- ============================================= --}}
                    {{-- PROFILE --}}
                    {{-- ============================================= --}}
                   @if(session('logged_in'))   
                    <div class="p-4">

                        <div class="flex items-center gap-3">

                            {{-- Avatar Mobile Dropdown Header --}}
                            <img
                                class="w-11 h-11 rounded-full border border-white/20 object-cover"
                                src="{{ $avatarUrl }}"
                                alt="Profile"
                            >

                            <div>

                                <div class="font-semibold text-white">
                                    {{ session('username') }}
                                </div>

                                <div class="text-xs text-gray-400 uppercase">
                                    {{ session('user_role') }}
                                </div>

                            </div>

                        </div>

                    </div> @endif


                    {{-- ============================================= --}}
                    {{-- NAVIGASI --}}
                    {{-- ============================================= --}}

                    <div class="border-t border-white/10 py-2">

                        <div class="px-4 py-1.5 text-xs
                                    font-semibold text-gray-500 uppercase">
                            Navigasi
                        </div>

                        {{-- Dashboard --}}
                            <a
                                href="{{ route('dashboard') }}"
                                class="dropdown-link"
                            >
                                <span>🏠</span>
                                <span>Dashboard</span>
                            </a>

                        {{-- Kasir --}}
                        <a
                            href="{{ route('kasir.index') }}"
                            class="dropdown-link"
                        >
                            <span>🛒</span>
                            <span>Kasir</span>
                        </a>


                        {{-- Produk --}}
                        <a
                            href="{{ route('produk.index') }}"
                            class="dropdown-link"
                        >
                            <span>📦</span>
                            <span>Produk</span>
                        </a>


                        {{-- Pengeluaran --}}
                        <a
                            href="{{ route('pengeluaran') }}"
                            class="dropdown-link"
                        >
                            <span>💸</span>
                            <span>Pengeluaran</span>
                        </a>
                        
                                                    {{-- Laporan --}}
                            <a
                                href="{{ route('laporan') }}"
                                class="dropdown-link"
                            >
                                <span>📊</span>
                                <span>Laporan</span>
                            </a>

                        {{-- Riwayat --}}
                        <a
                            href="{{ route('riwayat') }}"
                            class="dropdown-link"
                        >
                            <span>🧾</span>
                            <span>Riwayat Transaksi</span>
                        </a>
                          
                           @if(session('logged_in'))

                                  {{-- Profil Saya --}}
                                  <a
                                      href="{{ route('profil') }}"
                                      class="dropdown-link"
                                  >
                                      <span>👤</span>
                                      <span>Profil Saya</span>
                                  </a>
                              
                              @else
                              
                                  {{-- Masuk / Daftar --}}
                                  <a
                                      href="{{ route('login') }}"
                                      class="dropdown-link"
                                  >
                                      <span>🔐</span>
                                      <span>Masuk / Daftar</span>
                                  </a>
                              
                              @endif

                    </div>


                    {{-- ============================================= --}}
                    {{-- ADMIN --}}
                    {{-- ============================================= --}}

                    @if(session('user_role') === 'admin')

                        <div class="border-t border-white/10 py-2">

                            <div class="px-4 py-1.5 text-xs
                                        font-semibold text-gray-500 uppercase">
                                Administrator
                            </div>


                            {{-- Manajemen Admin --}}
                            <a
                                href="{{ route('admin.index') }}"
                                class="dropdown-link"
                            >
                                <span>👥</span>
                                <span>Manajemen Admin</span>
                            </a>





                            {{-- Setting --}}
                            <a
                                href="{{ route('setting') }}"
                                class="dropdown-link"
                            >
                                <span>⚙️</span>
                                <span>Pengaturan</span>
                            </a>

                        </div>

                    @endif


                    {{-- ============================================= --}}
                    {{-- LOGOUT --}}
                    {{-- ============================================= --}}
                  @if(session('logged_in'))
                    <div class="border-t border-white/10 py-2">

                        <a
                            href="{{ route('logout') }}"
                            onclick="localStorage.removeItem('smart_pos_cart')"
                            class="dropdown-link text-red-400
                                   hover:bg-red-500/10
                                   hover:text-red-300"
                        >
                            <span>🚪</span>
                            <span>Keluar</span>
                        </a>

                    </div> @endif

                </div>

            </div>

    </nav>


    {{-- ========================================================= --}}
    {{-- HEADER HALAMAN --}}
    {{-- ========================================================= --}}

        <header class="relative bg-gray-800/50 border-b border-white/10">

            <div
                class="mx-auto max-w-7xl
                       px-4 py-4
                       sm:px-6
                       lg:px-8"
            >

                <h1
                    class="text-2xl
                           sm:text-3xl
                           font-bold
                           tracking-tight
                           text-white"
                >
                    @yield('header', 'Dashboard')
                </h1>

            </div>

        </header>


    {{-- ========================================================= --}}
    {{-- MAIN CONTENT --}}
    {{-- ========================================================= --}}

    <main class="flex-grow">

        <div
            class="mx-auto max-w-7xl
                   px-4 py-6
                   sm:px-6
                   lg:px-8"
        >

            {{-- Error --}}
            @if(session('error'))

                <div
                    class="mb-5
                           rounded-xl
                           border border-red-500/20
                           bg-red-500/10
                           px-4 py-3
                           text-sm
                           text-red-300"
                >
                    {{ session('error') }}
                </div>

            @endif


            {{-- Success --}}
            @if(session('success'))

                <div
                    class="mb-5
                           rounded-xl
                           border border-green-500/20
                           bg-green-500/10
                           px-4 py-3
                           text-sm
                           text-green-300"
                >
                    {{ session('success') }}
                </div>

            @endif


            @yield('content')

        </div>

    </main>


    {{-- ========================================================= --}}
    {{-- FOOTER --}}
    {{-- ========================================================= --}}

    <footer
        class="bg-gray-800
               border-t border-white/10
               py-4 mt-auto"
    >

        <div
            class="mx-auto max-w-7xl
                   px-4
                   sm:px-6
                   lg:px-8
                   text-center
                   text-xs
                   text-gray-400"
        >
            &copy; 2026 Smart POS
        </div>

    </footer>


    {{-- ========================================================= --}}
    {{-- JAVASCRIPT --}}
    {{-- ========================================================= --}}

    <script>

        /*
        |--------------------------------------------------------------------------
        | DESKTOP PROFILE DROPDOWN
        |--------------------------------------------------------------------------
        */

        function toggleUserDropdown() {

            const dropdown =
                document.getElementById('user-dropdown');

            if (!dropdown) return;

            dropdown.classList.toggle('hidden');

        }


        /*
        |--------------------------------------------------------------------------
        | MOBILE PROFILE MENU
        |--------------------------------------------------------------------------
        */

        function toggleMobileMenu() {

            const menu =
                document.getElementById('mobile-menu');

            if (!menu) return;

            menu.classList.toggle('hidden');

        }


        /*
        |--------------------------------------------------------------------------
        | CLOSE DESKTOP DROPDOWN KETIKA KLIK DI LUAR
        |--------------------------------------------------------------------------
        */

        window.addEventListener('click', function (event) {

            const dropdown =
                document.getElementById('user-dropdown');

            if (dropdown) {

                const button =
                    dropdown.previousElementSibling;

                if (
                    button &&
                    !button.contains(event.target) &&
                    !dropdown.contains(event.target)
                ) {

                    dropdown.classList.add('hidden');

                }

            }

        });


        /*
        |--------------------------------------------------------------------------
        | TUTUP MOBILE MENU SETELAH MEMILIH LINK
        |--------------------------------------------------------------------------
        */

        document.querySelectorAll('#mobile-menu a')
            .forEach(function (link) {

                link.addEventListener('click', function () {

                    const menu =
                        document.getElementById('mobile-menu');

                    if (menu) {

                        menu.classList.add('hidden');

                    }

                });

            });

    </script>


    {{-- ========================================================= --}}
    {{-- STYLE --}}
    {{-- ========================================================= --}}

    <style>

        .dropdown-link {

            display: flex;

            align-items: center;

            gap: 0.75rem;

            width: 100%;

            padding: 0.65rem 1rem;

            font-size: 0.875rem;

            font-weight: 500;

            color: rgb(209 213 219);

            transition: all 0.2s ease;

        }


        .dropdown-link:hover {

            background: rgb(55 65 81);

            color: white;

        }

    </style>

</body>

</html>
