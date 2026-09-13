<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>KasirKU — Solusi Kasir untuk UMKM</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-950 text-white">

    {{-- LANDING PAGE KASIRKU --}}
    
    <main>

       {{-- NAVBAR --}}
<nav class="border-b border-white/10 bg-gray-950/90 backdrop-blur-md">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="h-16 flex items-center justify-between">

            {{-- LOGO --}}
            <a href="/" class="flex items-center gap-2">
                <span class="w-9 h-9 rounded-xl bg-emerald-500 flex items-center justify-center font-black text-gray-950 text-xl">
                    K
                </span>

                <span class="text-xl font-bold tracking-tight">
                    <span class="text-emerald-400">asir</span><span class="text-white">KU</span>
                </span>
            </a>

            {{-- MENU DESKTOP --}}
            <div class="hidden md:flex items-center gap-7 text-sm text-gray-300">
                <a href="#fitur" class="hover:text-white transition">
                    Fitur
                </a>

                <a href="#keunggulan" class="hover:text-white transition">
                    Keunggulan
                </a>

                <a href="#cara-kerja" class="hover:text-white transition">
                    Cara Kerja
                </a>

                <a href="#faq" class="hover:text-white transition">
                    FAQ
                </a>
            </div>

            {{-- TOMBOL --}}
            <div class="flex items-center gap-2">
                <a
                    href="{{ route('login') }}"
                    class="hidden sm:inline-flex px-4 py-2 text-sm text-gray-300 hover:text-white transition"
                >
                    Masuk
                </a>

                <a
                    href="{{ route('register') }}"
                    class="inline-flex items-center px-4 py-2 bg-emerald-500 hover:bg-emerald-400 text-gray-950 rounded-xl text-sm font-bold transition active:scale-95"
                >
                    Daftar Gratis
                </a>
            </div>

        </div>
    </div>
</nav>

{{-- HERO SECTION --}}
<section class="relative overflow-hidden">

    {{-- Background Glow --}}
    <div class="absolute -top-32 -left-32 w-80 h-80
                bg-indigo-600/20 rounded-full blur-3xl">
    </div>

    <div class="absolute top-20 -right-32 w-96 h-96
                bg-emerald-500/10 rounded-full blur-3xl">
    </div>

    <div class="relative max-w-7xl mx-auto
                px-4 sm:px-6 lg:px-8
                py-20 sm:py-28 lg:py-32">

        <div class="grid lg:grid-cols-2
                    gap-12 lg:gap-16
                    items-center">

            {{-- KONTEN --}}
            <div class="text-center lg:text-left">

                {{-- Badge --}}
                <div class="inline-flex items-center gap-2
                            px-4 py-2
                            rounded-full
                            bg-white/5
                            border border-white/10
                            text-sm text-gray-300">

                    <span class="w-2 h-2
                                 rounded-full
                                 bg-emerald-400
                                 shadow-lg
                                 shadow-emerald-400/50">
                    </span>

                    Solusi kasir untuk UMKM
                </div>


                {{-- Judul --}}
                <h1 class="mt-6
                           text-4xl sm:text-5xl lg:text-6xl
                           font-black
                           tracking-tight
                           leading-tight">

                    Kelola Usaha
                    <span class="block
                                 text-transparent
                                 bg-clip-text
                                 bg-gradient-to-r
                                 from-indigo-400
                                 via-blue-400
                                 to-emerald-400">
                        Lebih Mudah
                    </span>

                    <span class="block text-white">
                        Bersama KasirKU
                    </span>

                </h1>


                {{-- Deskripsi --}}
                <p class="mt-6
                          max-w-xl
                          mx-auto lg:mx-0
                          text-base sm:text-lg
                          text-gray-400
                          leading-7">

                    KasirKU membantu Anda mengelola
                    produk, stok, transaksi, pengeluaran,
                    dan laporan usaha dalam satu aplikasi
                    yang sederhana dan mudah digunakan.

                </p>


                {{-- Tombol --}}
                <div class="mt-8
                            flex flex-col sm:flex-row
                            items-center
                            justify-center lg:justify-start
                            gap-3">

                    <a
                        href="{{ route('register') }}"
                        class="w-full sm:w-auto
                               inline-flex items-center
                               justify-center gap-2
                               px-6 py-3.5
                               bg-emerald-500
                               hover:bg-emerald-400
                               text-gray-950
                               rounded-xl
                               font-bold
                               shadow-lg
                               shadow-emerald-500/20
                               transition
                               active:scale-95"
                    >
                        🚀 Buat Toko Gratis
                    </a>


                    <a
                        href="#fitur"
                        class="w-full sm:w-auto
                               inline-flex items-center
                               justify-center gap-2
                               px-6 py-3.5
                               bg-white/5
                               hover:bg-white/10
                               border border-white/10
                               text-white
                               rounded-xl
                               font-semibold
                               transition"
                    >
                        Lihat Fitur
                        <span>↓</span>
                    </a>

                </div>


                {{-- Info kecil --}}
                <div class="mt-6
                            flex flex-wrap
                            items-center
                            justify-center lg:justify-start
                            gap-x-5 gap-y-2
                            text-xs text-gray-500">

                    <span>✓ Produk & Stok</span>
                    <span>✓ Kasir & Transaksi</span>
                    <span>✓ Laporan</span>

                </div>

            </div>


            {{-- VISUAL KANAN --}}
            <div class="relative">

                {{-- Glow --}}
                <div class="absolute inset-0
                            bg-indigo-500/20
                            blur-3xl
                            rounded-full">
                </div>


                {{-- Mockup --}}
                <div class="relative
                            max-w-sm
                            mx-auto">

                    <div class="rounded-[2rem]
                                bg-gray-900
                                border border-white/10
                                shadow-2xl
                                shadow-indigo-900/30
                                p-3">

                        {{-- Layar --}}
                        <div class="rounded-[1.5rem]
                                    overflow-hidden
                                    bg-gray-800
                                    border border-white/10">

                            {{-- Header Mockup --}}
                            <div class="px-4 py-4
                                        bg-gray-800
                                        border-b border-white/10">

                                <div class="flex items-center
                                            justify-between">

                                    <div class="flex items-center gap-2">

                                        <div class="w-8 h-8
                                                    rounded-lg
                                                    bg-emerald-500
                                                    flex items-center
                                                    justify-center
                                                    text-gray-950
                                                    font-black">
                                            K
                                        </div>

                                        <div>
                                            <p class="text-sm
                                                      font-bold
                                                      text-white">
                                                KasirKU
                                            </p>

                                            <p class="text-[10px]
                                                      text-gray-500">
                                                Dashboard
                                            </p>
                                        </div>

                                    </div>

                                    <div class="w-8 h-8
                                                rounded-full
                                                bg-white/5
                                                flex items-center
                                                justify-center">
                                        ☰
                                    </div>

                                </div>

                            </div>


                            {{-- Isi Mockup --}}
                            <div class="p-4 space-y-4">

                                {{-- Greeting --}}
                                <div>
                                    <p class="text-xs text-gray-500">
                                        Selamat datang 👋
                                    </p>

                                    <p class="text-lg
                                              font-bold
                                              text-white">
                                        Kelola toko Anda
                                    </p>
                                </div>


                                {{-- Statistik --}}
                                <div class="grid grid-cols-2 gap-3">

                                    <div class="rounded-xl
                                                bg-indigo-500/10
                                                border border-indigo-400/10
                                                p-3">

                                        <p class="text-[10px]
                                                  text-gray-500">
                                            Penjualan
                                        </p>

                                        <p class="mt-1
                                                  text-base
                                                  font-bold
                                                  text-white">
                                            Rp 2.450.000
                                        </p>

                                    </div>


                                    <div class="rounded-xl
                                                bg-emerald-500/10
                                                border border-emerald-400/10
                                                p-3">

                                        <p class="text-[10px]
                                                  text-gray-500">
                                            Produk
                                        </p>

                                        <p class="mt-1
                                                  text-base
                                                  font-bold
                                                  text-white">
                                            128
                                        </p>

                                    </div>

                                </div>


                                {{-- Menu --}}
                                <div class="space-y-2">

                                    <div class="flex items-center gap-3
                                                p-3
                                                rounded-xl
                                                bg-white/5">

                                        <span class="text-xl">📦</span>

                                        <div>
                                            <p class="text-xs
                                                      font-semibold
                                                      text-white">
                                                Produk & Stok
                                            </p>

                                            <p class="text-[10px]
                                                      text-gray-500">
                                                Kelola barang dengan mudah
                                            </p>
                                        </div>

                                    </div>


                                    <div class="flex items-center gap-3
                                                p-3
                                                rounded-xl
                                                bg-white/5">

                                        <span class="text-xl">🛒</span>

                                        <div>
                                            <p class="text-xs
                                                      font-semibold
                                                      text-white">
                                                Kasir & Transaksi
                                            </p>

                                            <p class="text-[10px]
                                                      text-gray-500">
                                                Proses penjualan lebih cepat
                                            </p>
                                        </div>

                                    </div>


                                    <div class="flex items-center gap-3
                                                p-3
                                                rounded-xl
                                                bg-white/5">

                                        <span class="text-xl">📊</span>

                                        <div>
                                            <p class="text-xs
                                                      font-semibold
                                                      text-white">
                                                Laporan
                                            </p>

                                            <p class="text-[10px]
                                                      text-gray-500">
                                                Pantau perkembangan usaha
                                            </p>
                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>


                    {{-- Floating Badge --}}
                    <div class="absolute
                                -bottom-5
                                -left-4
                                sm:-left-8
                                px-4 py-3
                                rounded-2xl
                                bg-gray-800
                                border border-white/10
                                shadow-xl">

                        <div class="flex items-center gap-3">

                            <div class="w-9 h-9
                                        rounded-xl
                                        bg-emerald-500/10
                                        flex items-center
                                        justify-center">
                                📈
                            </div>

                            <div>
                                <p class="text-[10px]
                                          text-gray-500">
                                    Kelola lebih praktis
                                </p>

                                <p class="text-xs
                                          font-bold
                                          text-white">
                                    Semua dalam satu aplikasi
                                </p>
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

{{-- KENAPA KASIRKU --}}
<section class="py-20 sm:py-24 border-t border-white/5">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="max-w-2xl mx-auto text-center">

            <span class="text-sm font-semibold text-emerald-400">
                KENAPA KASIRKU?
            </span>

            <h2 class="mt-3 text-3xl sm:text-4xl font-black tracking-tight">
                Usaha lebih teratur,
                <span class="text-indigo-400">
                    tanpa ribet.
                </span>
            </h2>

            <p class="mt-4 text-gray-400 leading-7">
                KasirKU dirancang untuk membantu pemilik usaha
                mengelola kegiatan sehari-hari dengan lebih sederhana,
                cepat, dan terorganisir.
            </p>

        </div>


        {{-- KARTU --}}
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-12">

            {{-- 1 --}}
            <div class="p-6 rounded-2xl
                        bg-gray-900/70
                        border border-white/10
                        hover:border-indigo-400/30
                        transition">

                <div class="w-12 h-12 rounded-xl
                            bg-indigo-500/10
                            flex items-center justify-center
                            text-2xl">
                    📱
                </div>

                <h3 class="mt-5 text-lg font-bold">
                    Bisa dari HP
                </h3>

                <p class="mt-2 text-sm text-gray-400 leading-6">
                    Kelola usaha dari perangkat yang sudah Anda gunakan
                    setiap hari tanpa harus bergantung pada komputer.
                </p>

            </div>


            {{-- 2 --}}
            <div class="p-6 rounded-2xl
                        bg-gray-900/70
                        border border-white/10
                        hover:border-emerald-400/30
                        transition">

                <div class="w-12 h-12 rounded-xl
                            bg-emerald-500/10
                            flex items-center justify-center
                            text-2xl">
                    ⚡
                </div>

                <h3 class="mt-5 text-lg font-bold">
                    Cepat & sederhana
                </h3>

                <p class="mt-2 text-sm text-gray-400 leading-6">
                    Tampilan dibuat sederhana agar pemilik usaha maupun
                    kasir dapat menggunakannya dengan mudah.
                </p>

            </div>


            {{-- 3 --}}
            <div class="p-6 rounded-2xl
                        bg-gray-900/70
                        border border-white/10
                        hover:border-blue-400/30
                        transition">

                <div class="w-12 h-12 rounded-xl
                            bg-blue-500/10
                            flex items-center justify-center
                            text-2xl">
                    📊
                </div>

                <h3 class="mt-5 text-lg font-bold">
                    Pantau usaha
                </h3>

                <p class="mt-2 text-sm text-gray-400 leading-6">
                    Lihat transaksi, omzet, stok, pengeluaran,
                    dan laporan usaha dengan lebih mudah.
                </p>

            </div>


            {{-- 4 --}}
            <div class="p-6 rounded-2xl
                        bg-gray-900/70
                        border border-white/10
                        hover:border-purple-400/30
                        transition">

                <div class="w-12 h-12 rounded-xl
                            bg-purple-500/10
                            flex items-center justify-center
                            text-2xl">
                    🏪
                </div>

                <h3 class="mt-5 text-lg font-bold">
                    Banyak toko
                </h3>

                <p class="mt-2 text-sm text-gray-400 leading-6">
                    Satu akun dapat digunakan untuk mengelola
                    lebih dari satu toko atau cabang.
                </p>

            </div>


            {{-- 5 --}}
            <div class="p-6 rounded-2xl
                        bg-gray-900/70
                        border border-white/10
                        hover:border-yellow-400/30
                        transition">

                <div class="w-12 h-12 rounded-xl
                            bg-yellow-500/10
                            flex items-center justify-center
                            text-2xl">
                    👥
                </div>

                <h3 class="mt-5 text-lg font-bold">
                    Kelola kasir
                </h3>

                <p class="mt-2 text-sm text-gray-400 leading-6">
                    Pemilik toko dapat mengatur pengguna dan
                    memberikan akses sesuai kebutuhan.
                </p>

            </div>


            {{-- 6 --}}
            <div class="p-6 rounded-2xl
                        bg-gray-900/70
                        border border-white/10
                        hover:border-pink-400/30
                        transition">

                <div class="w-12 h-12 rounded-xl
                            bg-pink-500/10
                            flex items-center justify-center
                            text-2xl">
                    🧾
                </div>

                <h3 class="mt-5 text-lg font-bold">
                    Riwayat tersimpan
                </h3>

                <p class="mt-2 text-sm text-gray-400 leading-6">
                    Transaksi dan aktivitas usaha tersimpan sehingga
                    lebih mudah ditinjau kembali.
                </p>

            </div>

        </div>

    </div>

</section>

{{-- FITUR UTAMA --}}
<section id="fitur" class="py-20 sm:py-24 bg-gray-900/40 border-t border-white/5">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- JUDUL --}}
        <div class="max-w-3xl mx-auto text-center">

            <span class="text-sm font-semibold text-indigo-400">
                FITUR KASIRKU
            </span>

            <h2 class="mt-3 text-3xl sm:text-4xl font-black tracking-tight">
                Semua kebutuhan usaha
                <span class="text-emerald-400">
                    dalam satu aplikasi
                </span>
            </h2>

            <p class="mt-4 text-gray-400 leading-7">
                Dari mengelola produk hingga memantau laporan penjualan,
                semua tersedia dalam satu tempat.
            </p>

        </div>


        {{-- DAFTAR FITUR --}}
        <div class="grid md:grid-cols-2 gap-5 mt-14">


            {{-- PRODUK --}}
            <div class="group p-6 sm:p-7 rounded-3xl
                        bg-gray-950/70
                        border border-white/10
                        hover:border-indigo-400/30
                        transition">

                <div class="flex items-start gap-5">

                    <div class="w-14 h-14 shrink-0
                                rounded-2xl
                                bg-indigo-500/10
                                flex items-center justify-center
                                text-3xl">
                        📦
                    </div>

                    <div>

                        <h3 class="text-xl font-bold">
                            Produk & Stok
                        </h3>

                        <p class="mt-2 text-sm text-gray-400 leading-6">
                            Kelola daftar produk, harga, stok, kategori,
                            SKU, dan informasi barang dengan lebih mudah.
                        </p>

                        <div class="mt-4 flex flex-wrap gap-2">

                            <span class="px-2.5 py-1 rounded-lg
                                         bg-white/5 text-xs text-gray-400">
                                Kelola Produk
                            </span>

                            <span class="px-2.5 py-1 rounded-lg
                                         bg-white/5 text-xs text-gray-400">
                                Stok
                            </span>

                            <span class="px-2.5 py-1 rounded-lg
                                         bg-white/5 text-xs text-gray-400">
                                SKU
                            </span>

                        </div>

                    </div>

                </div>

            </div>


            {{-- KASIR --}}
            <div class="group p-6 sm:p-7 rounded-3xl
                        bg-gray-950/70
                        border border-white/10
                        hover:border-emerald-400/30
                        transition">

                <div class="flex items-start gap-5">

                    <div class="w-14 h-14 shrink-0
                                rounded-2xl
                                bg-emerald-500/10
                                flex items-center justify-center
                                text-3xl">
                        🛒
                    </div>

                    <div>

                        <h3 class="text-xl font-bold">
                            Kasir & Transaksi
                        </h3>

                        <p class="mt-2 text-sm text-gray-400 leading-6">
                            Proses transaksi dengan cepat, tambahkan produk
                            ke keranjang, berikan diskon, dan pilih metode
                            pembayaran.
                        </p>

                        <div class="mt-4 flex flex-wrap gap-2">

                            <span class="px-2.5 py-1 rounded-lg
                                         bg-white/5 text-xs text-gray-400">
                                Keranjang
                            </span>

                            <span class="px-2.5 py-1 rounded-lg
                                         bg-white/5 text-xs text-gray-400">
                                Diskon
                            </span>

                            <span class="px-2.5 py-1 rounded-lg
                                         bg-white/5 text-xs text-gray-400">
                                Pembayaran
                            </span>

                        </div>

                    </div>

                </div>

            </div>


            {{-- LAPORAN --}}
            <div class="group p-6 sm:p-7 rounded-3xl
                        bg-gray-950/70
                        border border-white/10
                        hover:border-blue-400/30
                        transition">

                <div class="flex items-start gap-5">

                    <div class="w-14 h-14 shrink-0
                                rounded-2xl
                                bg-blue-500/10
                                flex items-center justify-center
                                text-3xl">
                        📊
                    </div>

                    <div>

                        <h3 class="text-xl font-bold">
                            Laporan Penjualan
                        </h3>

                        <p class="mt-2 text-sm text-gray-400 leading-6">
                            Pantau omzet, jumlah transaksi, barang terjual,
                            diskon, pengeluaran, dan kondisi usaha berdasarkan
                            periode yang dipilih.
                        </p>

                        <div class="mt-4 flex flex-wrap gap-2">

                            <span class="px-2.5 py-1 rounded-lg
                                         bg-white/5 text-xs text-gray-400">
                                Omzet
                            </span>

                            <span class="px-2.5 py-1 rounded-lg
                                         bg-white/5 text-xs text-gray-400">
                                Transaksi
                            </span>

                            <span class="px-2.5 py-1 rounded-lg
                                         bg-white/5 text-xs text-gray-400">
                                Laporan
                            </span>

                        </div>

                    </div>

                </div>

            </div>


            {{-- PENGELUARAN --}}
            <div class="group p-6 sm:p-7 rounded-3xl
                        bg-gray-950/70
                        border border-white/10
                        hover:border-yellow-400/30
                        transition">

                <div class="flex items-start gap-5">

                    <div class="w-14 h-14 shrink-0
                                rounded-2xl
                                bg-yellow-500/10
                                flex items-center justify-center
                                text-3xl">
                        💸
                    </div>

                    <div>

                        <h3 class="text-xl font-bold">
                            Pengeluaran
                        </h3>

                        <p class="mt-2 text-sm text-gray-400 leading-6">
                            Catat pengeluaran usaha dan pantau biaya yang
                            keluar agar kondisi keuangan toko lebih mudah
                            diketahui.
                        </p>

                        <div class="mt-4 flex flex-wrap gap-2">

                            <span class="px-2.5 py-1 rounded-lg
                                         bg-white/5 text-xs text-gray-400">
                                Catatan Biaya
                            </span>

                            <span class="px-2.5 py-1 rounded-lg
                                         bg-white/5 text-xs text-gray-400">
                                Pengeluaran
                            </span>

                        </div>

                    </div>

                </div>

            </div>


            {{-- MULTI TOKO --}}
            <div class="group p-6 sm:p-7 rounded-3xl
                        bg-gray-950/70
                        border border-white/10
                        hover:border-purple-400/30
                        transition">

                <div class="flex items-start gap-5">

                    <div class="w-14 h-14 shrink-0
                                rounded-2xl
                                bg-purple-500/10
                                flex items-center justify-center
                                text-3xl">
                        🏪
                    </div>

                    <div>

                        <h3 class="text-xl font-bold">
                            Banyak Toko & Cabang
                        </h3>

                        <p class="mt-2 text-sm text-gray-400 leading-6">
                            Kelola beberapa toko dari satu akun dan berpindah
                            toko dengan mudah sesuai kebutuhan.
                        </p>

                        <div class="mt-4 flex flex-wrap gap-2">

                            <span class="px-2.5 py-1 rounded-lg
                                         bg-white/5 text-xs text-gray-400">
                                Multi-Toko
                            </span>

                            <span class="px-2.5 py-1 rounded-lg
                                         bg-white/5 text-xs text-gray-400">
                                Cabang
                            </span>

                        </div>

                    </div>

                </div>

            </div>


            {{-- KASIR / USER --}}
            <div class="group p-6 sm:p-7 rounded-3xl
                        bg-gray-950/70
                        border border-white/10
                        hover:border-pink-400/30
                        transition">

                <div class="flex items-start gap-5">

                    <div class="w-14 h-14 shrink-0
                                rounded-2xl
                                bg-pink-500/10
                                flex items-center justify-center
                                text-3xl">
                        👥
                    </div>

                    <div>

                        <h3 class="text-xl font-bold">
                            Kelola Pengguna & Kasir
                        </h3>

                        <p class="mt-2 text-sm text-gray-400 leading-6">
                            Tambahkan pengguna sebagai kasir dan atur
                            penggunaan aplikasi sesuai peran masing-masing.
                        </p>

                        <div class="mt-4 flex flex-wrap gap-2">

                            <span class="px-2.5 py-1 rounded-lg
                                         bg-white/5 text-xs text-gray-400">
                                Admin
                            </span>

                            <span class="px-2.5 py-1 rounded-lg
                                         bg-white/5 text-xs text-gray-400">
                                Kasir
                            </span>

                            <span class="px-2.5 py-1 rounded-lg
                                         bg-white/5 text-xs text-gray-400">
                                Hak Akses
                            </span>

                        </div>

                    </div>

                </div>

            </div>


        </div>

    </div>

</section>

{{-- MASALAH UMKM --}}
<section class="py-20 sm:py-24 border-t border-white/5">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="grid lg:grid-cols-2 gap-12 lg:gap-20 items-center">

            {{-- KIRI --}}
            <div>

                <span class="text-sm font-semibold text-red-400">
                    MASIH MENGELOLA USAHA SECARA MANUAL?
                </span>

                <h2 class="mt-3 text-3xl sm:text-4xl font-black tracking-tight">
                    Jangan biarkan urusan
                    <span class="text-red-400">
                        administrasi
                    </span>
                    menghambat usaha.
                </h2>

                <p class="mt-5 text-gray-400 leading-7">
                    Ketika usaha mulai berkembang, mencatat semuanya secara
                    manual bisa membuat pekerjaan semakin rumit.
                </p>

            </div>


            {{-- KANAN --}}
            <div class="space-y-3">

                {{-- Masalah 1 --}}
                <div class="flex items-start gap-4 p-4 rounded-2xl
                            bg-gray-900/70
                            border border-white/10">

                    <div class="w-10 h-10 shrink-0 rounded-xl
                                bg-red-500/10
                                flex items-center justify-center">
                        ❌
                    </div>

                    <div>
                        <h3 class="font-bold text-white">
                            Catatan transaksi berantakan
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            Sulit mengetahui transaksi yang terjadi
                            dan mencari riwayat penjualan.
                        </p>
                    </div>

                </div>


                {{-- Masalah 2 --}}
                <div class="flex items-start gap-4 p-4 rounded-2xl
                            bg-gray-900/70
                            border border-white/10">

                    <div class="w-10 h-10 shrink-0 rounded-xl
                                bg-red-500/10
                                flex items-center justify-center">
                        ❌
                    </div>

                    <div>
                        <h3 class="font-bold text-white">
                            Sulit mengetahui stok
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            Stok barang sering tidak sesuai dengan
                            kondisi sebenarnya di toko.
                        </p>
                    </div>

                </div>


                {{-- Masalah 3 --}}
                <div class="flex items-start gap-4 p-4 rounded-2xl
                            bg-gray-900/70
                            border border-white/10">

                    <div class="w-10 h-10 shrink-0 rounded-xl
                                bg-red-500/10
                                flex items-center justify-center">
                        ❌
                    </div>

                    <div>
                        <h3 class="font-bold text-white">
                            Sulit menghitung omzet
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            Perlu waktu untuk menghitung penjualan
                            dan melihat hasil usaha.
                        </p>
                    </div>

                </div>


                {{-- Masalah 4 --}}
                <div class="flex items-start gap-4 p-4 rounded-2xl
                            bg-gray-900/70
                            border border-white/10">

                    <div class="w-10 h-10 shrink-0 rounded-xl
                                bg-red-500/10
                                flex items-center justify-center">
                        ❌
                    </div>

                    <div>
                        <h3 class="font-bold text-white">
                            Pengeluaran sulit dipantau
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            Uang keluar sering tercampur sehingga
                            kondisi keuangan usaha sulit dipantau.
                        </p>
                    </div>

                </div>

            </div>

        </div>


        {{-- SOLUSI --}}
        <div class="mt-16 p-7 sm:p-10 rounded-3xl
                    bg-gradient-to-br
                    from-indigo-600/20
                    via-blue-600/10
                    to-emerald-500/10
                    border border-white/10">

            <div class="max-w-3xl">

                <span class="text-sm font-semibold text-emerald-400">
                    SOLUSI DARI KASIRKU
                </span>

                <h3 class="mt-3 text-2xl sm:text-3xl font-black">
                    Satu tempat untuk membantu
                    mengatur kegiatan usaha Anda.
                </h3>

                <p class="mt-4 text-gray-400 leading-7">
                    Dengan KasirKU, berbagai aktivitas penting seperti
                    mengelola produk, mencatat transaksi, memantau stok,
                    mencatat pengeluaran, hingga melihat laporan dapat
                    dilakukan dalam satu aplikasi.
                </p>

            </div>

        </div>

    </div>

</section>

{{-- CARA KERJA --}}
<section id="cara-kerja" class="py-20 sm:py-24 bg-gray-900/40 border-t border-white/5">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- JUDUL --}}
        <div class="max-w-3xl mx-auto text-center">

            <span class="text-sm font-semibold text-emerald-400">
                CARA KERJA KASIRKU
            </span>

            <h2 class="mt-3 text-3xl sm:text-4xl font-black tracking-tight">
                Mulai dari
                <span class="text-indigo-400">satu akun</span>,
                kelola usaha dengan mudah.
            </h2>

            <p class="mt-4 text-gray-400 leading-7">
                Tidak perlu proses yang rumit.
                Buat akun, buat toko, lalu langsung gunakan
                KasirKU untuk membantu mengelola usaha Anda.
            </p>

        </div>

        {{-- LANGKAH --}}
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-5 mt-14">

            {{-- STEP 1 --}}
            <div class="relative p-6 rounded-3xl bg-gray-950/70 border border-white/10">

                <div class="flex items-center justify-between">

                    <div class="w-12 h-12 rounded-2xl bg-indigo-500/10
                                flex items-center justify-center text-2xl">
                        👤
                    </div>

                    <span class="text-4xl font-black text-white/5">
                        01
                    </span>

                </div>

                <h3 class="mt-6 text-lg font-bold">
                    Buat Akun
                </h3>

                <p class="mt-2 text-sm text-gray-400 leading-6">
                    Daftarkan akun KasirKU menggunakan
                    nama dan email Anda.
                </p>

            </div>

            {{-- STEP 2 --}}
            <div class="relative p-6 rounded-3xl bg-gray-950/70 border border-white/10">

                <div class="flex items-center justify-between">

                    <div class="w-12 h-12 rounded-2xl bg-emerald-500/10
                                flex items-center justify-center text-2xl">
                        🏪
                    </div>

                    <span class="text-4xl font-black text-white/5">
                        02
                    </span>

                </div>

                <h3 class="mt-6 text-lg font-bold">
                    Buat Toko
                </h3>

                <p class="mt-2 text-sm text-gray-400 leading-6">
                    Masukkan nama toko dan informasi
                    dasar usaha Anda.
                </p>

            </div>

            {{-- STEP 3 --}}
            <div class="relative p-6 rounded-3xl bg-gray-950/70 border border-white/10">

                <div class="flex items-center justify-between">

                    <div class="w-12 h-12 rounded-2xl bg-blue-500/10
                                flex items-center justify-center text-2xl">
                        📦
                    </div>

                    <span class="text-4xl font-black text-white/5">
                        03
                    </span>

                </div>

                <h3 class="mt-6 text-lg font-bold">
                    Kelola Produk
                </h3>

                <p class="mt-2 text-sm text-gray-400 leading-6">
                    Tambahkan produk, atur harga,
                    stok, kategori, dan SKU.
                </p>

            </div>

            {{-- STEP 4 --}}
            <div class="relative p-6 rounded-3xl bg-gray-950/70 border border-white/10">

                <div class="flex items-center justify-between">

                    <div class="w-12 h-12 rounded-2xl bg-purple-500/10
                                flex items-center justify-center text-2xl">
                        📊
                    </div>

                    <span class="text-4xl font-black text-white/5">
                        04
                    </span>

                </div>

                <h3 class="mt-6 text-lg font-bold">
                    Pantau Usaha
                </h3>

                <p class="mt-2 text-sm text-gray-400 leading-6">
                    Catat transaksi dan lihat laporan
                    untuk mengetahui perkembangan usaha.
                </p>

            </div>

        </div>

        {{-- CTA --}}
        <div class="mt-14 text-center">

            <a
                href="{{ route('register') }}"
                class="inline-flex items-center justify-center gap-2
                       px-6 py-3.5
                       bg-emerald-500
                       hover:bg-emerald-400
                       text-gray-950
                       rounded-xl
                       font-bold
                       shadow-lg
                       shadow-emerald-500/20
                       transition
                       active:scale-95"
            >
                🚀 Mulai Gunakan KasirKU
            </a>

        </div>

    </div>

</section>

{{-- DETAIL FITUR --}}
<section id="keunggulan" class="py-20 sm:py-24 border-t border-white/5">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- JUDUL --}}
        <div class="max-w-3xl mx-auto text-center">

            <span class="text-sm font-semibold text-indigo-400">
                LEBIH DARI SEKADAR KASIR
            </span>

            <h2 class="mt-3 text-3xl sm:text-4xl font-black tracking-tight">
                Semua yang Anda butuhkan
                <span class="text-emerald-400">
                    ada di KasirKU.
                </span>
            </h2>

            <p class="mt-4 text-gray-400 leading-7">
                KasirKU membantu menghubungkan berbagai aktivitas
                usaha dalam satu sistem sehingga pekerjaan sehari-hari
                menjadi lebih teratur.
            </p>

        </div>


        {{-- FITUR 1 --}}
        <div class="grid lg:grid-cols-2 gap-10 lg:gap-16 items-center mt-16">

            {{-- VISUAL --}}
            <div class="order-2 lg:order-1">

                <div class="relative max-w-lg mx-auto">

                    <div class="absolute inset-0
                                bg-indigo-500/10
                                blur-3xl
                                rounded-full">
                    </div>

                    <div class="relative
                                rounded-3xl
                                bg-gray-900
                                border border-white/10
                                p-4
                                shadow-2xl">

                        <div class="rounded-2xl
                                    bg-gray-800
                                    border border-white/10
                                    overflow-hidden">

                            <div class="px-4 py-3
                                        border-b border-white/10
                                        flex items-center justify-between">

                                <div>
                                    <p class="text-xs text-gray-500">
                                        Produk
                                    </p>

                                    <p class="text-sm font-bold">
                                        Daftar Produk
                                    </p>
                                </div>

                                <span class="px-2 py-1 rounded-lg
                                             bg-emerald-500/10
                                             text-emerald-400
                                             text-[10px]">
                                    128 Produk
                                </span>

                            </div>

                            <div class="p-4 space-y-3">

                                <div class="flex items-center gap-3
                                            p-3 rounded-xl bg-white/5">

                                    <div class="w-10 h-10 rounded-xl
                                                bg-indigo-500/10
                                                flex items-center
                                                justify-center">
                                        📱
                                    </div>

                                    <div class="flex-1">

                                        <p class="text-xs font-semibold">
                                            Kabel Data Type-C
                                        </p>

                                        <p class="text-[10px] text-gray-500">
                                            Aksesoris • SKU: KBL001
                                        </p>

                                    </div>

                                    <div class="text-right">

                                        <p class="text-xs font-bold">
                                            Rp25.000
                                        </p>

                                        <p class="text-[10px] text-emerald-400">
                                            Stok 24
                                        </p>

                                    </div>

                                </div>


                                <div class="flex items-center gap-3
                                            p-3 rounded-xl bg-white/5">

                                    <div class="w-10 h-10 rounded-xl
                                                bg-emerald-500/10
                                                flex items-center
                                                justify-center">
                                        🎧
                                    </div>

                                    <div class="flex-1">

                                        <p class="text-xs font-semibold">
                                            Earphone Bluetooth
                                        </p>

                                        <p class="text-[10px] text-gray-500">
                                            Audio • SKU: EAR002
                                        </p>

                                    </div>

                                    <div class="text-right">

                                        <p class="text-xs font-bold">
                                            Rp75.000
                                        </p>

                                        <p class="text-[10px] text-yellow-400">
                                            Stok 5
                                        </p>

                                    </div>

                                </div>


                                <div class="flex items-center gap-3
                                            p-3 rounded-xl bg-white/5">

                                    <div class="w-10 h-10 rounded-xl
                                                bg-purple-500/10
                                                flex items-center
                                                justify-center">
                                        🔌
                                    </div>

                                    <div class="flex-1">

                                        <p class="text-xs font-semibold">
                                            Charger Fast Charging
                                        </p>

                                        <p class="text-[10px] text-gray-500">
                                            Charger • SKU: CHG003
                                        </p>

                                    </div>

                                    <div class="text-right">

                                        <p class="text-xs font-bold">
                                            Rp95.000
                                        </p>

                                        <p class="text-[10px] text-emerald-400">
                                            Stok 18
                                        </p>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- TEKS --}}
            <div class="order-1 lg:order-2">

                <span class="text-sm font-semibold text-indigo-400">
                    01 • PRODUK & STOK
                </span>

                <h3 class="mt-3 text-2xl sm:text-3xl font-black">
                    Ketahui barang Anda
                    tanpa harus mengingat semuanya.
                </h3>

                <p class="mt-4 text-gray-400 leading-7">
                    Kelola produk dengan informasi yang lebih teratur.
                    Nama barang, kategori, SKU, harga, dan stok dapat
                    dikelola dari satu tempat.
                </p>

                <div class="mt-6 space-y-3">

                    <div class="flex items-start gap-3">
                        <span class="text-emerald-400">✓</span>
                        <p class="text-sm text-gray-300">
                            Kelola harga dan stok produk
                        </p>
                    </div>

                    <div class="flex items-start gap-3">
                        <span class="text-emerald-400">✓</span>
                        <p class="text-sm text-gray-300">
                            Gunakan SKU untuk memudahkan pencarian
                        </p>
                    </div>

                    <div class="flex items-start gap-3">
                        <span class="text-emerald-400">✓</span>
                        <p class="text-sm text-gray-300">
                            Pantau stok barang dengan lebih mudah
                        </p>
                    </div>

                </div>

            </div>

        </div>


        {{-- FITUR 2 --}}
        <div class="grid lg:grid-cols-2 gap-10 lg:gap-16 items-center mt-24">

            {{-- TEKS --}}
            <div>

                <span class="text-sm font-semibold text-emerald-400">
                    02 • KASIR & TRANSAKSI
                </span>

                <h3 class="mt-3 text-2xl sm:text-3xl font-black">
                    Transaksi lebih cepat,
                    pekerjaan lebih ringan.
                </h3>

                <p class="mt-4 text-gray-400 leading-7">
                    Tambahkan produk ke keranjang, atur jumlah barang,
                    berikan diskon, lalu pilih metode pembayaran.
                    Semua dilakukan dalam satu halaman kasir.
                </p>

                <div class="mt-6 space-y-3">

                    <div class="flex items-start gap-3">
                        <span class="text-emerald-400">✓</span>
                        <p class="text-sm text-gray-300">
                            Keranjang transaksi
                        </p>
                    </div>

                    <div class="flex items-start gap-3">
                        <span class="text-emerald-400">✓</span>
                        <p class="text-sm text-gray-300">
                            Diskon transaksi
                        </p>
                    </div>

                    <div class="flex items-start gap-3">
                        <span class="text-emerald-400">✓</span>
                        <p class="text-sm text-gray-300">
                            Berbagai metode pembayaran
                        </p>
                    </div>

                </div>

            </div>


            {{-- VISUAL --}}
            <div>

                <div class="relative max-w-lg mx-auto">

                    <div class="absolute inset-0
                                bg-emerald-500/10
                                blur-3xl
                                rounded-full">
                    </div>

                    <div class="relative
                                rounded-3xl
                                bg-gray-900
                                border border-white/10
                                p-4
                                shadow-2xl">

                        <div class="rounded-2xl
                                    bg-gray-800
                                    border border-white/10
                                    p-4">

                            <div class="flex items-center justify-between">

                                <div>
                                    <p class="text-xs text-gray-500">
                                        Kasir
                                    </p>

                                    <p class="text-sm font-bold">
                                        Transaksi Baru
                                    </p>
                                </div>

                                <span class="text-xs text-emerald-400">
                                    3 Item
                                </span>

                            </div>

                            <div class="mt-5 space-y-3">

                                <div class="flex justify-between
                                            p-3 rounded-xl bg-white/5">

                                    <div>
                                        <p class="text-xs font-semibold">
                                            Kabel Data
                                        </p>

                                        <p class="text-[10px] text-gray-500">
                                            2 × Rp25.000
                                        </p>
                                    </div>

                                    <p class="text-xs font-bold">
                                        Rp50.000
                                    </p>

                                </div>

                                <div class="flex justify-between
                                            p-3 rounded-xl bg-white/5">

                                    <div>
                                        <p class="text-xs font-semibold">
                                            Charger
                                        </p>

                                        <p class="text-[10px] text-gray-500">
                                            1 × Rp95.000
                                        </p>
                                    </div>

                                    <p class="text-xs font-bold">
                                        Rp95.000
                                    </p>

                                </div>

                            </div>

                            <div class="mt-5 pt-4
                                        border-t border-white/10">

                                <div class="flex justify-between">

                                    <span class="text-xs text-gray-500">
                                        Total
                                    </span>

                                    <span class="text-lg font-black text-emerald-400">
                                        Rp145.000
                                    </span>

                                </div>

                                <div class="mt-3
                                            grid grid-cols-3 gap-2">

                                    <span class="text-center py-2 rounded-lg
                                                 bg-white/5 text-[10px]">
                                        Tunai
                                    </span>

                                    <span class="text-center py-2 rounded-lg
                                                 bg-emerald-500/10
                                                 text-emerald-400
                                                 text-[10px]">
                                        QRIS
                                    </span>

                                    <span class="text-center py-2 rounded-lg
                                                 bg-white/5 text-[10px]">
                                        Debit
                                    </span>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

{{-- FITUR 3 & 4 --}}
<section class="py-20 sm:py-24 bg-gray-900/40 border-t border-white/5">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="grid lg:grid-cols-2 gap-10 lg:gap-16">

            {{-- PENGELUARAN --}}
            <div class="p-7 sm:p-8 rounded-3xl
                        bg-gray-950/70
                        border border-white/10">

                <div class="w-14 h-14 rounded-2xl
                            bg-yellow-500/10
                            flex items-center justify-center
                            text-3xl">
                    💸
                </div>

                <span class="block mt-6 text-sm font-semibold text-yellow-400">
                    03 • PENGELUARAN
                </span>

                <h3 class="mt-3 text-2xl sm:text-3xl font-black">
                    Uang keluar juga
                    perlu dicatat.
                </h3>

                <p class="mt-4 text-gray-400 leading-7">
                    Tidak hanya mencatat penjualan.
                    KasirKU membantu Anda mencatat pengeluaran
                    usaha sehingga arus uang lebih mudah dipantau.
                </p>

                {{-- MOCKUP --}}
                <div class="mt-7 rounded-2xl
                            bg-gray-900
                            border border-white/10
                            p-4">

                    <div class="flex items-center justify-between">

                        <div>
                            <p class="text-xs text-gray-500">
                                Pengeluaran
                            </p>

                            <p class="text-sm font-bold">
                                Catatan Pengeluaran
                            </p>
                        </div>

                        <span class="text-xs text-yellow-400">
                            Hari ini
                        </span>

                    </div>

                    <div class="mt-4 space-y-2">

                        <div class="flex items-center justify-between
                                    p-3 rounded-xl bg-white/5">

                            <div class="flex items-center gap-3">

                                <span class="text-lg">
                                    🏠
                                </span>

                                <div>
                                    <p class="text-xs font-semibold">
                                        Listrik
                                    </p>

                                    <p class="text-[10px] text-gray-500">
                                        Operasional
                                    </p>
                                </div>

                            </div>

                            <p class="text-xs font-bold text-red-400">
                                - Rp150.000
                            </p>

                        </div>


                        <div class="flex items-center justify-between
                                    p-3 rounded-xl bg-white/5">

                            <div class="flex items-center gap-3">

                                <span class="text-lg">
                                    🚚
                                </span>

                                <div>
                                    <p class="text-xs font-semibold">
                                        Ongkos Kirim
                                    </p>

                                    <p class="text-[10px] text-gray-500">
                                        Operasional
                                    </p>
                                </div>

                            </div>

                            <p class="text-xs font-bold text-red-400">
                                - Rp50.000
                            </p>

                        </div>

                    </div>

                    <div class="mt-4 pt-3
                                border-t border-white/10
                                flex justify-between">

                        <span class="text-xs text-gray-500">
                            Total Pengeluaran
                        </span>

                        <span class="text-sm font-black text-yellow-400">
                            Rp200.000
                        </span>

                    </div>

                </div>

                <div class="mt-6 space-y-3">

                    <div class="flex gap-3">
                        <span class="text-emerald-400">✓</span>
                        <span class="text-sm text-gray-300">
                            Catat biaya operasional
                        </span>
                    </div>

                    <div class="flex gap-3">
                        <span class="text-emerald-400">✓</span>
                        <span class="text-sm text-gray-300">
                            Simpan riwayat pengeluaran
                        </span>
                    </div>

                    <div class="flex gap-3">
                        <span class="text-emerald-400">✓</span>
                        <span class="text-sm text-gray-300">
                            Pantau total uang yang keluar
                        </span>
                    </div>

                </div>

            </div>


            {{-- LAPORAN --}}
            <div class="p-7 sm:p-8 rounded-3xl
                        bg-gray-950/70
                        border border-white/10">

                <div class="w-14 h-14 rounded-2xl
                            bg-blue-500/10
                            flex items-center justify-center
                            text-3xl">
                    📊
                </div>

                <span class="block mt-6 text-sm font-semibold text-blue-400">
                    04 • LAPORAN
                </span>

                <h3 class="mt-3 text-2xl sm:text-3xl font-black">
                    Lihat kondisi usaha
                    dengan lebih jelas.
                </h3>

                <p class="mt-4 text-gray-400 leading-7">
                    Tidak perlu menghitung semuanya secara manual.
                    Gunakan laporan untuk melihat penjualan,
                    transaksi, stok, dan pengeluaran berdasarkan periode.
                </p>

                {{-- MOCKUP --}}
                <div class="mt-7 rounded-2xl
                            bg-gray-900
                            border border-white/10
                            p-4">

                    <div class="flex items-center justify-between">

                        <div>
                            <p class="text-xs text-gray-500">
                                Laporan
                            </p>

                            <p class="text-sm font-bold">
                                Ringkasan Usaha
                            </p>
                        </div>

                        <span class="text-[10px]
                                     px-2 py-1 rounded-lg
                                     bg-blue-500/10
                                     text-blue-400">
                            Bulan Ini
                        </span>

                    </div>

                    <div class="grid grid-cols-2 gap-2 mt-4">

                        <div class="p-3 rounded-xl bg-white/5">

                            <p class="text-[10px] text-gray-500">
                                Omzet
                            </p>

                            <p class="mt-1 text-sm font-black text-emerald-400">
                                Rp12,4 Jt
                            </p>

                        </div>

                        <div class="p-3 rounded-xl bg-white/5">

                            <p class="text-[10px] text-gray-500">
                                Transaksi
                            </p>

                            <p class="mt-1 text-sm font-black">
                                186
                            </p>

                        </div>

                        <div class="p-3 rounded-xl bg-white/5">

                            <p class="text-[10px] text-gray-500">
                                Barang Terjual
                            </p>

                            <p class="mt-1 text-sm font-black">
                                427
                            </p>

                        </div>

                        <div class="p-3 rounded-xl bg-white/5">

                            <p class="text-[10px] text-gray-500">
                                Pengeluaran
                            </p>

                            <p class="mt-1 text-sm font-black text-yellow-400">
                                Rp2,1 Jt
                            </p>

                        </div>

                    </div>

                    <div class="mt-4 p-3 rounded-xl
                                bg-gradient-to-r
                                from-indigo-500/10
                                to-emerald-500/10
                                border border-white/5">

                        <div class="flex justify-between">

                            <span class="text-xs text-gray-500">
                                Kas Bersih
                            </span>

                            <span class="text-sm font-black text-emerald-400">
                                Rp10,3 Jt
                            </span>

                        </div>

                    </div>

                </div>

                <div class="mt-6 space-y-3">

                    <div class="flex gap-3">
                        <span class="text-emerald-400">✓</span>
                        <span class="text-sm text-gray-300">
                            Lihat omzet dan transaksi
                        </span>
                    </div>

                    <div class="flex gap-3">
                        <span class="text-emerald-400">✓</span>
                        <span class="text-sm text-gray-300">
                            Pantau barang yang terjual
                        </span>
                    </div>

                    <div class="flex gap-3">
                        <span class="text-emerald-400">✓</span>
                        <span class="text-sm text-gray-300">
                            Bandingkan penjualan dan pengeluaran
                        </span>
                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

{{-- MULTI TOKO --}}
<section class="py-20 sm:py-24 border-t border-white/5">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="grid lg:grid-cols-2 gap-12 lg:gap-20 items-center">

            {{-- KIRI --}}
            <div>

                <span class="text-sm font-semibold text-purple-400">
                    MULTI-TOKO & CABANG
                </span>

                <h2 class="mt-3 text-3xl sm:text-4xl font-black tracking-tight">
                    Satu akun untuk
                    <span class="text-emerald-400">
                        beberapa toko.
                    </span>
                </h2>

                <p class="mt-5 text-gray-400 leading-7">
                    Punya lebih dari satu toko atau cabang?
                    Anda tidak perlu membuat akun berbeda untuk setiap toko.
                    KasirKU memungkinkan satu akun mengelola beberapa toko
                    dan berpindah toko sesuai kebutuhan.
                </p>

                {{-- POINT --}}
                <div class="mt-7 space-y-4">

                    <div class="flex items-start gap-4">

                        <div class="w-10 h-10 shrink-0 rounded-xl
                                    bg-purple-500/10
                                    flex items-center justify-center">
                            🏪
                        </div>

                        <div>
                            <h3 class="font-bold">
                                Tambahkan toko baru
                            </h3>

                            <p class="mt-1 text-sm text-gray-500 leading-6">
                                Buat toko atau cabang tambahan
                                dari akun yang sama.
                            </p>
                        </div>

                    </div>


                    <div class="flex items-start gap-4">

                        <div class="w-10 h-10 shrink-0 rounded-xl
                                    bg-indigo-500/10
                                    flex items-center justify-center">
                            🔄
                        </div>

                        <div>
                            <h3 class="font-bold">
                                Berpindah toko dengan mudah
                            </h3>

                            <p class="mt-1 text-sm text-gray-500 leading-6">
                                Pilih toko yang ingin dikelola
                                tanpa harus logout dan login kembali.
                            </p>
                        </div>

                    </div>


                    <div class="flex items-start gap-4">

                        <div class="w-10 h-10 shrink-0 rounded-xl
                                    bg-emerald-500/10
                                    flex items-center justify-center">
                            👥
                        </div>

                        <div>
                            <h3 class="font-bold">
                                Pengguna sesuai toko
                            </h3>

                            <p class="mt-1 text-sm text-gray-500 leading-6">
                                Kelola pengguna dan kasir
                                sesuai kebutuhan toko.
                            </p>
                        </div>

                    </div>

                </div>

            </div>


            {{-- KANAN : MOCKUP --}}
            <div class="relative">

                <div class="absolute inset-0
                            bg-purple-500/10
                            blur-3xl
                            rounded-full">
                </div>

                <div class="relative
                            max-w-md
                            mx-auto
                            rounded-3xl
                            bg-gray-900
                            border border-white/10
                            shadow-2xl
                            p-4">

                    {{-- HEADER --}}
                    <div class="rounded-2xl
                                bg-gray-800
                                border border-white/10
                                overflow-hidden">

                        <div class="px-4 py-4
                                    border-b border-white/10">

                            <p class="text-[10px] text-gray-500">
                                TOKO AKTIF
                            </p>

                            <div class="mt-2 flex items-center justify-between">

                                <div class="flex items-center gap-3">

                                    <div class="w-10 h-10 rounded-xl
                                                bg-emerald-500
                                                flex items-center
                                                justify-center
                                                text-gray-950
                                                font-black">
                                        K
                                    </div>

                                    <div>

                                        <p class="text-sm font-bold">
                                            Toko Utama
                                        </p>

                                        <p class="text-[10px] text-gray-500">
                                            Toko aktif
                                        </p>

                                    </div>

                                </div>

                                <span class="text-lg">
                                    ▾
                                </span>

                            </div>

                        </div>


                        {{-- DAFTAR TOKO --}}
                        <div class="p-4">

                            <p class="text-xs text-gray-500 mb-3">
                                Toko Anda
                            </p>

                            <div class="space-y-2">

                                <div class="flex items-center gap-3
                                            p-3 rounded-xl
                                            bg-emerald-500/10
                                            border border-emerald-400/20">

                                    <div class="w-9 h-9 rounded-lg
                                                bg-emerald-500/10
                                                flex items-center
                                                justify-center">
                                        🏪
                                    </div>

                                    <div class="flex-1">

                                        <p class="text-xs font-semibold">
                                            Toko Utama
                                        </p>

                                        <p class="text-[10px] text-gray-500">
                                            Toko aktif
                                        </p>

                                    </div>

                                    <span class="text-emerald-400 text-sm">
                                        ✓
                                    </span>

                                </div>


                                <div class="flex items-center gap-3
                                            p-3 rounded-xl
                                            bg-white/5">

                                    <div class="w-9 h-9 rounded-lg
                                                bg-indigo-500/10
                                                flex items-center
                                                justify-center">
                                        🏬
                                    </div>

                                    <div class="flex-1">

                                        <p class="text-xs font-semibold">
                                            Toko Cabang
                                        </p>

                                        <p class="text-[10px] text-gray-500">
                                            Cabang
                                        </p>

                                    </div>

                                    <span class="text-gray-600 text-sm">
                                        →
                                    </span>

                                </div>


                                <div class="flex items-center gap-3
                                            p-3 rounded-xl
                                            bg-white/5">

                                    <div class="w-9 h-9 rounded-lg
                                                bg-purple-500/10
                                                flex items-center
                                                justify-center">
                                        🏪
                                    </div>

                                    <div class="flex-1">

                                        <p class="text-xs font-semibold">
                                            Toko Yeyer
                                        </p>

                                        <p class="text-[10px] text-gray-500">
                                            Cabang
                                        </p>

                                    </div>

                                    <span class="text-gray-600 text-sm">
                                        →
                                    </span>

                                </div>

                            </div>


                            {{-- TAMBAH TOKO --}}
                            <div class="mt-4">

                                <div class="flex items-center justify-center
                                            gap-2
                                            p-3
                                            rounded-xl
                                            border border-dashed
                                            border-white/10
                                            text-xs
                                            text-gray-500">

                                    <span>＋</span>
                                    Tambahkan toko baru

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- INFO BAWAH --}}
        <div class="mt-12
                    p-5 sm:p-6
                    rounded-2xl
                    bg-purple-500/5
                    border border-purple-400/10">

            <div class="flex items-start gap-4">

                <span class="text-2xl">
                    💡
                </span>

                <div>

                    <h3 class="font-bold">
                        Cocok untuk usaha yang sedang berkembang
                    </h3>

                    <p class="mt-1 text-sm text-gray-500 leading-6">
                        Mulai dari satu toko hari ini,
                        lalu tambahkan cabang ketika bisnis Anda berkembang.
                    </p>

                </div>

            </div>

        </div>

    </div>

</section>

{{-- ADMIN & KASIR --}}
<section class="py-20 sm:py-24 bg-gray-900/40 border-t border-white/5">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="grid lg:grid-cols-2 gap-12 lg:gap-20 items-center">

            {{-- VISUAL --}}
            <div class="order-2 lg:order-1">

                <div class="relative max-w-md mx-auto">

                    <div class="absolute inset-0
                                bg-pink-500/10
                                blur-3xl
                                rounded-full">
                    </div>

                    <div class="relative rounded-3xl
                                bg-gray-900
                                border border-white/10
                                shadow-2xl
                                p-4">

                        <div class="rounded-2xl
                                    bg-gray-800
                                    border border-white/10
                                    overflow-hidden">

                            {{-- HEADER --}}
                            <div class="px-4 py-4
                                        border-b border-white/10">

                                <p class="text-[10px] text-gray-500">
                                    PENGGUNA TOKO
                                </p>

                                <p class="mt-1 text-sm font-bold">
                                    Kelola Pengguna
                                </p>

                            </div>

                            {{-- USER 1 --}}
                            <div class="p-4 space-y-3">

                                <div class="flex items-center gap-3
                                            p-3 rounded-xl bg-white/5">

                                    <div class="w-10 h-10 rounded-full
                                                bg-emerald-500/10
                                                flex items-center
                                                justify-center">
                                        👤
                                    </div>

                                    <div class="flex-1">

                                        <p class="text-xs font-semibold">
                                            Pemilik Toko
                                        </p>

                                        <p class="text-[10px] text-gray-500">
                                            Akses penuh
                                        </p>

                                    </div>

                                    <span class="px-2 py-1 rounded-lg
                                                 bg-emerald-500/10
                                                 text-emerald-400
                                                 text-[10px]">
                                        Admin
                                    </span>

                                </div>


                                {{-- USER 2 --}}
                                <div class="flex items-center gap-3
                                            p-3 rounded-xl bg-white/5">

                                    <div class="w-10 h-10 rounded-full
                                                bg-indigo-500/10
                                                flex items-center
                                                justify-center">
                                        🧑‍💼
                                    </div>

                                    <div class="flex-1">

                                        <p class="text-xs font-semibold">
                                            Kasir Toko
                                        </p>

                                        <p class="text-[10px] text-gray-500">
                                            Melayani transaksi
                                        </p>

                                    </div>

                                    <span class="px-2 py-1 rounded-lg
                                                 bg-indigo-500/10
                                                 text-indigo-400
                                                 text-[10px]">
                                        Kasir
                                    </span>

                                </div>


                                {{-- USER 3 --}}
                                <div class="flex items-center gap-3
                                            p-3 rounded-xl bg-white/5">

                                    <div class="w-10 h-10 rounded-full
                                                bg-purple-500/10
                                                flex items-center
                                                justify-center">
                                        🧑‍💼
                                    </div>

                                    <div class="flex-1">

                                        <p class="text-xs font-semibold">
                                            Kasir Cabang
                                        </p>

                                        <p class="text-[10px] text-gray-500">
                                            Melayani transaksi
                                        </p>

                                    </div>

                                    <span class="px-2 py-1 rounded-lg
                                                 bg-purple-500/10
                                                 text-purple-400
                                                 text-[10px]">
                                        Kasir
                                    </span>

                                </div>

                            </div>

                            {{-- TAMBAH --}}
                            <div class="px-4 pb-4">

                                <div class="flex items-center justify-center
                                            gap-2
                                            p-3
                                            rounded-xl
                                            border border-dashed
                                            border-white/10
                                            text-xs
                                            text-gray-500">

                                    <span>＋</span>
                                    Tambahkan pengguna

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- TEKS --}}
            <div class="order-1 lg:order-2">

                <span class="text-sm font-semibold text-pink-400">
                    ADMIN & KASIR
                </span>

                <h2 class="mt-3 text-3xl sm:text-4xl font-black tracking-tight">
                    Bagi pekerjaan,
                    <span class="text-emerald-400">
                        tetap kendalikan usaha.
                    </span>
                </h2>

                <p class="mt-5 text-gray-400 leading-7">
                    Pemilik toko tidak harus mengerjakan semuanya sendiri.
                    Tambahkan pengguna sebagai kasir dan biarkan mereka
                    membantu melayani transaksi.
                </p>

                <div class="mt-7 space-y-4">

                    <div class="flex items-start gap-4">

                        <div class="w-10 h-10 shrink-0 rounded-xl
                                    bg-emerald-500/10
                                    flex items-center justify-center">
                            👑
                        </div>

                        <div>
                            <h3 class="font-bold">
                                Admin / Pemilik
                            </h3>

                            <p class="mt-1 text-sm text-gray-500 leading-6">
                                Mengelola toko, produk, pengguna,
                                laporan, dan aktivitas usaha.
                            </p>
                        </div>

                    </div>


                    <div class="flex items-start gap-4">

                        <div class="w-10 h-10 shrink-0 rounded-xl
                                    bg-indigo-500/10
                                    flex items-center justify-center">
                            🧾
                        </div>

                        <div>
                            <h3 class="font-bold">
                                Kasir
                            </h3>

                            <p class="mt-1 text-sm text-gray-500 leading-6">
                                Fokus melayani pelanggan dan
                                memproses transaksi penjualan.
                            </p>
                        </div>

                    </div>


                    <div class="flex items-start gap-4">

                        <div class="w-10 h-10 shrink-0 rounded-xl
                                    bg-purple-500/10
                                    flex items-center justify-center">
                            🔐
                        </div>

                        <div>
                            <h3 class="font-bold">
                                Akses berdasarkan peran
                            </h3>

                            <p class="mt-1 text-sm text-gray-500 leading-6">
                                Setiap pengguna menggunakan akun
                                sesuai dengan peran yang diberikan.
                            </p>
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

{{-- COCOK UNTUK BERBAGAI USAHA --}}
<section class="py-20 sm:py-24 border-t border-white/5">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- JUDUL --}}
        <div class="max-w-3xl mx-auto text-center">

            <span class="text-sm font-semibold text-emerald-400">
                UNTUK BERBAGAI JENIS USAHA
            </span>

            <h2 class="mt-3 text-3xl sm:text-4xl font-black tracking-tight">
                Apa pun usaha Anda,
                <span class="text-indigo-400">
                    KasirKU siap membantu.
                </span>
            </h2>

            <p class="mt-4 text-gray-400 leading-7">
                KasirKU dirancang agar dapat digunakan oleh berbagai
                jenis usaha yang membutuhkan pencatatan produk,
                transaksi, stok, dan laporan.
            </p>

        </div>


        {{-- JENIS USAHA --}}
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-14">

            {{-- KONTER HP --}}
            <div class="group p-6 rounded-3xl
                        bg-gray-900/70
                        border border-white/10
                        hover:border-indigo-400/30
                        hover:-translate-y-1
                        transition">

                <div class="w-14 h-14 rounded-2xl
                            bg-indigo-500/10
                            flex items-center justify-center
                            text-3xl">
                    📱
                </div>

                <h3 class="mt-5 text-lg font-bold">
                    Konter HP
                </h3>

                <p class="mt-2 text-sm text-gray-500 leading-6">
                    Cocok untuk mengelola HP, sparepart,
                    aksesoris, dan berbagai kebutuhan konter.
                </p>

            </div>


            {{-- TOKO AKSESORIS --}}
            <div class="group p-6 rounded-3xl
                        bg-gray-900/70
                        border border-white/10
                        hover:border-emerald-400/30
                        hover:-translate-y-1
                        transition">

                <div class="w-14 h-14 rounded-2xl
                            bg-emerald-500/10
                            flex items-center justify-center
                            text-3xl">
                    🎧
                </div>

                <h3 class="mt-5 text-lg font-bold">
                    Toko Aksesoris
                </h3>

                <p class="mt-2 text-sm text-gray-500 leading-6">
                    Kelola banyak jenis aksesoris dengan
                    pencatatan produk dan stok yang lebih rapi.
                </p>

            </div>


            {{-- TOKO RETAIL --}}
            <div class="group p-6 rounded-3xl
                        bg-gray-900/70
                        border border-white/10
                        hover:border-blue-400/30
                        hover:-translate-y-1
                        transition">

                <div class="w-14 h-14 rounded-2xl
                            bg-blue-500/10
                            flex items-center justify-center
                            text-3xl">
                    🛍️
                </div>

                <h3 class="mt-5 text-lg font-bold">
                    Toko Retail
                </h3>

                <p class="mt-2 text-sm text-gray-500 leading-6">
                    Bantu mencatat penjualan dan mengelola
                    barang untuk kebutuhan toko sehari-hari.
                </p>

            </div>


            {{-- USAHA BERKEMBANG --}}
            <div class="group p-6 rounded-3xl
                        bg-gray-900/70
                        border border-white/10
                        hover:border-purple-400/30
                        hover:-translate-y-1
                        transition">

                <div class="w-14 h-14 rounded-2xl
                            bg-purple-500/10
                            flex items-center justify-center
                            text-3xl">
                    🏪
                </div>

                <h3 class="mt-5 text-lg font-bold">
                    Usaha Berkembang
                </h3>

                <p class="mt-2 text-sm text-gray-500 leading-6">
                    Mulai dari satu toko dan berkembang
                    menjadi beberapa toko atau cabang.
                </p>

            </div>

        </div>


        {{-- PESAN --}}
        <div class="mt-12 text-center">

            <p class="text-sm text-gray-500">
                Dan masih banyak jenis usaha lainnya.
            </p>

        </div>

    </div>

</section>

{{-- KENAPA MEMILIH KASIRKU --}}
<section class="py-20 sm:py-24 bg-gray-900/40 border-t border-white/5">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- JUDUL --}}
        <div class="max-w-3xl mx-auto text-center">

            <span class="text-sm font-semibold text-indigo-400">
                KENAPA KASIRKU?
            </span>

            <h2 class="mt-3 text-3xl sm:text-4xl font-black tracking-tight">
                Dibuat untuk membantu
                <span class="text-emerald-400">
                    usaha berkembang.
                </span>
            </h2>

            <p class="mt-4 text-gray-400 leading-7">
                Bukan sekadar mencatat transaksi.
                KasirKU dirancang untuk membantu pemilik usaha
                mengelola kegiatan toko dengan lebih praktis.
            </p>

        </div>


        {{-- KEUNGGULAN --}}
        <div class="grid md:grid-cols-2 gap-5 mt-14">

            {{-- 1 --}}
            <div class="flex gap-5 p-6 sm:p-7 rounded-3xl
                        bg-gray-950/70
                        border border-white/10">

                <div class="w-14 h-14 shrink-0 rounded-2xl
                            bg-emerald-500/10
                            flex items-center justify-center
                            text-3xl">
                    📱
                </div>

                <div>

                    <h3 class="text-lg font-bold">
                        Nyaman digunakan dari HP
                    </h3>

                    <p class="mt-2 text-sm text-gray-500 leading-6">
                        Kelola toko dari perangkat yang selalu ada
                        di tangan Anda. Tidak harus selalu menggunakan
                        komputer untuk menjalankan usaha.
                    </p>

                </div>

            </div>


            {{-- 2 --}}
            <div class="flex gap-5 p-6 sm:p-7 rounded-3xl
                        bg-gray-950/70
                        border border-white/10">

                <div class="w-14 h-14 shrink-0 rounded-2xl
                            bg-indigo-500/10
                            flex items-center justify-center
                            text-3xl">
                    ⚡
                </div>

                <div>

                    <h3 class="text-lg font-bold">
                        Sederhana dan mudah dipahami
                    </h3>

                    <p class="mt-2 text-sm text-gray-500 leading-6">
                        Tampilan dibuat dengan fokus pada kebutuhan
                        utama toko sehingga pengguna tidak perlu
                        menghadapi sistem yang terlalu rumit.
                    </p>

                </div>

            </div>


            {{-- 3 --}}
            <div class="flex gap-5 p-6 sm:p-7 rounded-3xl
                        bg-gray-950/70
                        border border-white/10">

                <div class="w-14 h-14 shrink-0 rounded-2xl
                            bg-blue-500/10
                            flex items-center justify-center
                            text-3xl">
                    🗂️
                </div>

                <div>

                    <h3 class="text-lg font-bold">
                        Data usaha lebih terorganisir
                    </h3>

                    <p class="mt-2 text-sm text-gray-500 leading-6">
                        Produk, transaksi, pengeluaran, dan laporan
                        tersimpan dalam sistem sehingga lebih mudah
                        untuk ditinjau kembali.
                    </p>

                </div>

            </div>


            {{-- 4 --}}
            <div class="flex gap-5 p-6 sm:p-7 rounded-3xl
                        bg-gray-950/70
                        border border-white/10">

                <div class="w-14 h-14 shrink-0 rounded-2xl
                            bg-purple-500/10
                            flex items-center justify-center
                            text-3xl">
                    📈
                </div>

                <div>

                    <h3 class="text-lg font-bold">
                        Siap mengikuti perkembangan usaha
                    </h3>

                    <p class="mt-2 text-sm text-gray-500 leading-6">
                        Mulai dari satu toko dan gunakan fitur
                        multi-toko ketika usaha Anda mulai berkembang
                        dan memiliki cabang.
                    </p>

                </div>

            </div>

        </div>


        {{-- HIGHLIGHT --}}
        <div class="mt-12 rounded-3xl
                    bg-gradient-to-r
                    from-indigo-600/20
                    via-blue-600/10
                    to-emerald-500/20
                    border border-white/10
                    p-7 sm:p-10">

            <div class="grid sm:grid-cols-3 gap-6 text-center">

                <div>
                    <div class="text-3xl">📦</div>
                    <p class="mt-2 font-bold">
                        Produk lebih rapi
                    </p>
                    <p class="mt-1 text-xs text-gray-500">
                        Kelola barang dan stok
                    </p>
                </div>

                <div>
                    <div class="text-3xl">🧾</div>
                    <p class="mt-2 font-bold">
                        Transaksi tercatat
                    </p>
                    <p class="mt-1 text-xs text-gray-500">
                        Riwayat mudah ditinjau
                    </p>
                </div>

                <div>
                    <div class="text-3xl">📊</div>
                    <p class="mt-2 font-bold">
                        Usaha lebih terpantau
                    </p>
                    <p class="mt-1 text-xs text-gray-500">
                        Lihat laporan usaha
                    </p>
                </div>

            </div>

        </div>

    </div>

</section>

{{-- FAQ --}}
<section id="faq" class="py-20 sm:py-24 border-t border-white/5">

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- JUDUL --}}
        <div class="max-w-2xl mx-auto text-center">

            <span class="text-sm font-semibold text-emerald-400">
                FAQ
            </span>

            <h2 class="mt-3 text-3xl sm:text-4xl font-black tracking-tight">
                Pertanyaan yang
                <span class="text-indigo-400">
                    sering ditanyakan.
                </span>
            </h2>

            <p class="mt-4 text-gray-400 leading-7">
                Beberapa hal yang perlu Anda ketahui
                sebelum mulai menggunakan KasirKU.
            </p>

        </div>


        {{-- DAFTAR FAQ --}}
        <div class="mt-12 space-y-3">


            {{-- FAQ 1 --}}
            <details
                class="group rounded-2xl
                       bg-gray-900/70
                       border border-white/10
                       overflow-hidden"
            >

                <summary
                    class="flex items-center justify-between
                           gap-4
                           p-5
                           cursor-pointer
                           list-none
                           font-semibold
                           text-white"
                >

                    <span>
                        Apa itu KasirKU?
                    </span>

                    <span class="text-gray-500
                                 text-xl
                                 transition
                                 group-open:rotate-45">
                        +
                    </span>

                </summary>

                <div class="px-5 pb-5">

                    <p class="text-sm text-gray-400 leading-7">
                        KasirKU adalah aplikasi untuk membantu pemilik
                        usaha mengelola produk, stok, transaksi,
                        pengeluaran, pengguna, dan laporan dalam
                        satu sistem.
                    </p>

                </div>

            </details>


            {{-- FAQ 2 --}}
            <details
                class="group rounded-2xl
                       bg-gray-900/70
                       border border-white/10
                       overflow-hidden"
            >

                <summary
                    class="flex items-center justify-between
                           gap-4
                           p-5
                           cursor-pointer
                           list-none
                           font-semibold
                           text-white"
                >

                    <span>
                        Apakah KasirKU bisa digunakan dari HP?
                    </span>

                    <span class="text-gray-500
                                 text-xl
                                 transition
                                 group-open:rotate-45">
                        +
                    </span>

                </summary>

                <div class="px-5 pb-5">

                    <p class="text-sm text-gray-400 leading-7">
                        Bisa. KasirKU dirancang agar dapat digunakan
                        melalui browser pada HP maupun perangkat
                        dengan layar yang lebih besar.
                    </p>

                </div>

            </details>


            {{-- FAQ 3 --}}
            <details
                class="group rounded-2xl
                       bg-gray-900/70
                       border border-white/10
                       overflow-hidden"
            >

                <summary
                    class="flex items-center justify-between
                           gap-4
                           p-5
                           cursor-pointer
                           list-none
                           font-semibold
                           text-white"
                >

                    <span>
                        Apakah satu akun bisa memiliki beberapa toko?
                    </span>

                    <span class="text-gray-500
                                 text-xl
                                 transition
                                 group-open:rotate-45">
                        +
                    </span>

                </summary>

                <div class="px-5 pb-5">

                    <p class="text-sm text-gray-400 leading-7">
                        Ya. Satu akun dapat digunakan untuk mengelola
                        beberapa toko atau cabang dan berpindah toko
                        sesuai kebutuhan.
                    </p>

                </div>

            </details>


            {{-- FAQ 4 --}}
            <details
                class="group rounded-2xl
                       bg-gray-900/70
                       border border-white/10
                       overflow-hidden"
            >

                <summary
                    class="flex items-center justify-between
                           gap-4
                           p-5
                           cursor-pointer
                           list-none
                           font-semibold
                           text-white"
                >

                    <span>
                        Apakah saya bisa menambahkan kasir?
                    </span>

                    <span class="text-gray-500
                                 text-xl
                                 transition
                                 group-open:rotate-45">
                        +
                    </span>

                </summary>

                <div class="px-5 pb-5">

                    <p class="text-sm text-gray-400 leading-7">
                        Bisa. Pemilik toko dapat menambahkan pengguna
                        sebagai kasir sehingga pekerjaan transaksi
                        dapat dilakukan oleh lebih dari satu orang.
                    </p>

                </div>

            </details>


            {{-- FAQ 5 --}}
            <details
                class="group rounded-2xl
                       bg-gray-900/70
                       border border-white/10
                       overflow-hidden"
            >

                <summary
                    class="flex items-center justify-between
                           gap-4
                           p-5
                           cursor-pointer
                           list-none
                           font-semibold
                           text-white"
                >

                    <span>
                        Bagaimana cara mulai menggunakan KasirKU?
                    </span>

                    <span class="text-gray-500
                                 text-xl
                                 transition
                                 group-open:rotate-45">
                        +
                    </span>

                </summary>

                <div class="px-5 pb-5">

                    <p class="text-sm text-gray-400 leading-7">
                        Klik tombol Daftar Gratis, buat akun,
                        masukkan informasi toko, kemudian Anda
                        dapat masuk ke aplikasi KasirKU dan mulai
                        mengelola usaha.
                    </p>

                </div>

            </details>


            {{-- FAQ 6 --}}
            <details
                class="group rounded-2xl
                       bg-gray-900/70
                       border border-white/10
                       overflow-hidden"
            >

                <summary
                    class="flex items-center justify-between
                           gap-4
                           p-5
                           cursor-pointer
                           list-none
                           font-semibold
                           text-white"
                >

                    <span>
                        Apakah KasirKU hanya untuk konter HP?
                    </span>

                    <span class="text-gray-500
                                 text-xl
                                 transition
                                 group-open:rotate-45">
                        +
                    </span>

                </summary>

                <div class="px-5 pb-5">

                    <p class="text-sm text-gray-400 leading-7">
                        Tidak. KasirKU dapat digunakan untuk berbagai
                        jenis usaha yang membutuhkan pengelolaan
                        produk, transaksi, stok, pengeluaran,
                        dan laporan.
                    </p>

                </div>

            </details>

        </div>

    </div>

</section>

{{-- CTA PENUTUP --}}
<section class="relative overflow-hidden py-20 sm:py-28">

    {{-- BACKGROUND GLOW --}}
    <div class="absolute -top-32 left-1/2 -translate-x-1/2
                w-96 h-96
                bg-indigo-600/20
                rounded-full
                blur-3xl">
    </div>

    <div class="absolute bottom-0 -left-32
                w-80 h-80
                bg-emerald-500/10
                rounded-full
                blur-3xl">
    </div>

    <div class="relative max-w-4xl mx-auto
                px-4 sm:px-6 lg:px-8
                text-center">

        {{-- BADGE --}}
        <div class="inline-flex items-center gap-2
                    px-4 py-2
                    rounded-full
                    bg-emerald-500/10
                    border border-emerald-400/10
                    text-sm text-emerald-400">

            <span>🚀</span>
            Mulai kelola usaha lebih mudah

        </div>


        {{-- JUDUL --}}
        <h2 class="mt-6
                   text-3xl sm:text-5xl
                   font-black
                   tracking-tight
                   leading-tight">

            Siap membuat usaha Anda
            <span class="block text-transparent
                         bg-clip-text
                         bg-gradient-to-r
                         from-indigo-400
                         via-blue-400
                         to-emerald-400">
                lebih teratur?
            </span>

        </h2>


        {{-- DESKRIPSI --}}
        <p class="mt-5
                  max-w-2xl
                  mx-auto
                  text-base sm:text-lg
                  text-gray-400
                  leading-7">

            Buat akun KasirKU dan mulai kelola produk,
            stok, transaksi, pengeluaran, serta laporan
            usaha Anda dalam satu aplikasi.

        </p>


        {{-- BUTTON --}}
        <div class="mt-8
                    flex flex-col sm:flex-row
                    items-center
                    justify-center
                    gap-3">

            <a
                href="{{ route('register') }}"
                class="w-full sm:w-auto
                       inline-flex items-center
                       justify-center gap-2
                       px-7 py-4
                       bg-emerald-500
                       hover:bg-emerald-400
                       text-gray-950
                       rounded-xl
                       font-bold
                       shadow-xl
                       shadow-emerald-500/20
                       transition
                       active:scale-95"
            >
                🚀 Daftar Gratis
            </a>

            <a
                href="{{ route('login') }}"
                class="w-full sm:w-auto
                       inline-flex items-center
                       justify-center
                       px-7 py-4
                       bg-white/5
                       hover:bg-white/10
                       border border-white/10
                       text-white
                       rounded-xl
                       font-semibold
                       transition"
            >
                Sudah punya akun?
            </a>

        </div>


        {{-- INFO --}}
        <div class="mt-7
                    flex flex-wrap
                    justify-center
                    gap-x-6 gap-y-2
                    text-xs text-gray-500">

            <span>✓ Buat akun</span>
            <span>✓ Buat toko</span>
            <span>✓ Mulai mengelola usaha</span>

        </div>

    </div>

</section>

{{-- FOOTER --}}
<footer class="border-t border-white/10 bg-gray-950">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-10">

            {{-- BRAND --}}
            <div class="lg:col-span-2">

                <a href="{{ route('home') }}"
                   class="inline-flex items-center gap-2">

                    <span class="w-9 h-9 rounded-xl
                                 bg-emerald-500
                                 flex items-center justify-center
                                 font-black text-gray-950 text-xl">
                        K
                    </span>

                    <span class="text-xl font-bold tracking-tight">
                        <span class="text-emerald-400">asir</span><span class="text-white">KU</span>
                    </span>

                </a>

                <p class="mt-4 max-w-md
                          text-sm text-gray-500
                          leading-6">

                    KasirKU membantu pemilik usaha
                    mengelola produk, stok, transaksi,
                    pengeluaran, dan laporan dalam
                    satu aplikasi.

                </p>

            </div>


            {{-- NAVIGASI --}}
            <div>

                <h3 class="text-sm font-bold text-white">
                    Navigasi
                </h3>

                <div class="mt-4 space-y-3">

                    <a href="#fitur"
                       class="block text-sm text-gray-500 hover:text-white transition">
                        Fitur
                    </a>

                    <a href="#keunggulan"
                       class="block text-sm text-gray-500 hover:text-white transition">
                        Keunggulan
                    </a>

                    <a href="#cara-kerja"
                       class="block text-sm text-gray-500 hover:text-white transition">
                        Cara Kerja
                    </a>

                    <a href="#faq"
                       class="block text-sm text-gray-500 hover:text-white transition">
                        FAQ
                    </a>

                </div>

            </div>


            {{-- AKUN --}}
            <div>

                <h3 class="text-sm font-bold text-white">
                    Akun
                </h3>

                <div class="mt-4 space-y-3">

                    <a href="{{ route('login') }}"
                       class="block text-sm text-gray-500 hover:text-white transition">
                        Masuk
                    </a>

                    <a href="{{ route('register') }}"
                       class="block text-sm text-gray-500 hover:text-white transition">
                        Daftar Gratis
                    </a>

                </div>

            </div>

        </div>


             {{-- GARIS BAWAH --}}
      <div class="mt-10 pt-6
                  border-t border-white/5
                  flex flex-col sm:flex-row
                  items-center
                  justify-between
                  gap-3">
      
          <a href="{{ route('register') }}"
             class="text-xs text-gray-600
                    hover:text-emerald-400
                    transition duration-200
                    cursor-pointer">
              © {{ date('Y') }} KasirKU. Semua hak dilindungi.
          </a>
      
          <p class="text-xs text-gray-600">
              Solusi kasir untuk usaha Anda.
          </p>
      
      </div>

    </div>

</footer>

    </main>

</body>
</html>