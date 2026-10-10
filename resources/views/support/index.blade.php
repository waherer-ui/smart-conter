@extends('layouts.app')

@section('title', 'Pusat Bantuan')

@section('content')

<div class="max-w-5xl mx-auto px-4 py-6">

    {{-- Header --}}
    <div class="flex items-center justify-between gap-3 mb-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-white">
                Pusat Bantuan
            </h1>
            <p class="text-sm text-gray-400 mt-1">
                Hubungi tim Kasir½M jika Anda membutuhkan bantuan.
            </p>
        </div>

        <a
            href="{{ route('support.create') }}"
            class="shrink-0 inline-flex items-center gap-2 px-4 py-2.5
                   bg-emerald-600 hover:bg-emerald-500
                   text-white text-sm font-semibold rounded-xl
                   transition"
        >
            <span class="text-lg leading-none">+</span>
            <span class="hidden sm:inline">Buat Tiket</span>
            <span class="sm:hidden">Tiket</span>
        </a>
    </div>
    
    {{-- Menu Utama Pusat Bantuan --}}

<div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mb-6">
  <a href="#panduan"
   class="group p-4 rounded-2xl bg-gray-800/80 border border-gray-700 hover:border-emerald-500/50 transition">
    <div class="w-11 h-11 rounded-xl bg-blue-500/10 flex items-center justify-center text-2xl mb-3">
        📚
    </div>
    <h3 class="text-sm font-semibold text-white">Panduan</h3>
    <p class="text-xs text-gray-400 mt-1">Pelajari penggunaan Kasir½M.</p>
</a>

<a href="{{ route('support.videos') }}"
   class="group p-4 rounded-2xl bg-gray-800/80 border border-gray-700 hover:border-emerald-500/50 transition">

    <div class="w-11 h-11 rounded-xl bg-emerald-500/10 flex items-center justify-center text-2xl mb-3">
        🎬
    </div>

    <h3 class="text-sm font-semibold text-white">Video Tutorial</h3>

    <p class="text-xs text-gray-400 mt-1">
        Pelajari fitur Kasir½M melalui video.
    </p>
</a>

<a href="#faq"
   class="group p-4 rounded-2xl bg-gray-800/80 border border-gray-700 hover:border-emerald-500/50 transition">
    <div class="w-11 h-11 rounded-xl bg-purple-500/10 flex items-center justify-center text-2xl mb-3">
        ❓
    </div>
    <h3 class="text-sm font-semibold text-white">FAQ</h3>
    <p class="text-xs text-gray-400 mt-1">Jawaban pertanyaan umum.</p>
</a>

<a href="#langganan"
   class="group p-4 rounded-2xl bg-gray-800/80 border border-gray-700 hover:border-emerald-500/50 transition">
    <div class="w-11 h-11 rounded-xl bg-amber-500/10 flex items-center justify-center text-2xl mb-3">
        💳
    </div>
    <h3 class="text-sm font-semibold text-white">Langganan</h3>
    <p class="text-xs text-gray-400 mt-1">Paket dan informasi layanan.</p>
</a>

<a href="#pengumuman"
   class="group p-4 rounded-2xl bg-gray-800/80 border border-gray-700 hover:border-emerald-500/50 transition">
    <div class="w-11 h-11 rounded-xl bg-pink-500/10 flex items-center justify-center text-2xl mb-3">
        🔔
    </div>
    <h3 class="text-sm font-semibold text-white">Pengumuman</h3>
    <p class="text-xs text-gray-400 mt-1">Informasi terbaru dari Kasir½M.</p>
</a>

<a href="{{ route('support.create') }}"
   class="group p-4 rounded-2xl bg-gray-800/80 border border-gray-700 hover:border-emerald-500/50 transition">
    <div class="w-11 h-11 rounded-xl bg-emerald-500/10 flex items-center justify-center text-2xl mb-3">
        🎫
    </div>
    <h3 class="text-sm font-semibold text-white">Tiket Bantuan</h3>
    <p class="text-xs text-gray-400 mt-1">Sampaikan kendala kepada tim kami.</p>
</a>

<a href="#kontak"
   class="group p-4 rounded-2xl bg-gray-800/80 border border-gray-700 hover:border-emerald-500/50 transition">
    <div class="w-11 h-11 rounded-xl bg-cyan-500/10 flex items-center justify-center text-2xl mb-3">
        💬
    </div>
    <h3 class="text-sm font-semibold text-white">Hubungi Kami</h3>
    <p class="text-xs text-gray-400 mt-1">Informasi kontak dukungan.</p>
</a>

</div>

    {{-- Success --}}
    @if(session('success'))
        <div class="mb-5 px-4 py-3 rounded-xl
                    bg-emerald-500/10 border border-emerald-500/20
                    text-emerald-400 text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Error --}}
    @if(session('error'))
        <div class="mb-5 px-4 py-3 rounded-xl
                    bg-red-500/10 border border-red-500/20
                    text-red-400 text-sm">
            {{ session('error') }}
        </div>
    @endif
    
{{-- Panduan Penggunaan --}}

<section id="panduan" class="mb-8 scroll-mt-24">

    <div class="mb-4">
        <h2 class="text-lg font-bold text-white">
            📚 Panduan Penggunaan
        </h2>
        <p class="text-sm text-gray-400 mt-1">
            Pelajari fitur Kasir½M melalui panduan singkat atau video tutorial.
        </p>
    </div>

    @php
        $panduanPenggunaan = [
            [
                'judul' => '🚀 Memulai Kasir½M',
                'kategori' => 'Pengenalan Kasir½M',
                'deskripsi' => 'Panduan awal untuk mulai menggunakan aplikasi Kasir½M.',
                'langkah' => [
                    'Lengkapi profil dan informasi toko.',
                    'Tambahkan produk beserta harga dan stok.',
                    'Kenali menu utama pada Dashboard.',
                    'Mulai transaksi setelah produk tersedia.',
                    'Periksa riwayat dan laporan penjualan.',
                ],
            ],
            [
                'judul' => '🏠 Mengenal Dashboard',
                'kategori' => 'Pengenalan Kasir½M',
                'deskripsi' => 'Kenali ringkasan usaha dan navigasi cepat pada Dashboard.',
                'langkah' => [
                    'Buka Dashboard setelah berhasil masuk.',
                    'Kenali ringkasan informasi usaha yang ditampilkan.',
                    'Gunakan navigasi cepat untuk membuka fitur.',
                    'Pilih menu sesuai aktivitas yang ingin dilakukan.',
                ],
            ],
            [
                'judul' => '👤 Mengatur Profil dan Toko',
                'kategori' => 'Pengaturan Toko',
                'deskripsi' => 'Pelajari pengaturan informasi akun dan toko.',
                'langkah' => [
                    'Buka menu Profil atau Pengaturan.',
                    'Periksa informasi akun dan toko.',
                    'Perbarui informasi yang ingin diubah.',
                    'Simpan perubahan dan periksa kembali hasilnya.',
                ],
            ],
            [
                'judul' => '📦 Mengelola Produk',
                'kategori' => 'Manajemen Produk',
                'deskripsi' => 'Pelajari cara menambah dan mengelola data produk.',
                'langkah' => [
                    'Buka menu Produk.',
                    'Tambahkan nama produk dan kategori.',
                    'Isi SKU atau kode produk jika diperlukan.',
                    'Masukkan harga modal, harga jual, dan stok.',
                    'Simpan dan periksa produk pada daftar.',
                ],
            ],
            [
                'judul' => '🏷️ Mengatur Harga dan Stok',
                'kategori' => 'Manajemen Produk',
                'deskripsi' => 'Pahami harga modal, harga jual, dan persediaan produk.',
                'langkah' => [
                    'Pilih produk yang ingin diperiksa.',
                    'Periksa harga modal dan harga jual.',
                    'Periksa jumlah stok yang tersedia.',
                    'Perbarui data sesuai perubahan persediaan.',
                    'Pastikan informasi produk sudah benar.',
                ],
            ],
            [
                'judul' => '📷 Memindai Barcode Produk',
                'kategori' => 'Manajemen Produk',
                'deskripsi' => 'Pelajari penggunaan pemindaian kode produk jika tersedia.',
                'langkah' => [
                    'Buka fitur pemindaian produk.',
                    'Izinkan akses kamera jika diminta.',
                    'Arahkan kamera ke barcode produk.',
                    'Periksa hasil pemindaian sebelum melanjutkan.',
                ],
            ],
            [
                'judul' => '🛒 Melakukan Transaksi',
                'kategori' => 'Transaksi Kasir',
                'deskripsi' => 'Pelajari proses penjualan dari memilih produk sampai selesai.',
                'langkah' => [
                    'Buka Dashboard atau halaman Kasir.',
                    'Cari produk yang ingin dijual.',
                    'Masukkan produk ke keranjang.',
                    'Periksa jumlah barang, harga, dan diskon.',
                    'Pilih metode pembayaran yang tersedia.',
                    'Periksa hasil transaksi setelah pembayaran.',
                ],
            ],
            [
                'judul' => '💳 Mengatur Metode Pembayaran',
                'kategori' => 'Pembayaran',
                'deskripsi' => 'Kenali metode pembayaran dan pengaturannya.',
                'langkah' => [
                    'Buka menu pengaturan pembayaran.',
                    'Periksa metode pembayaran yang sudah dikonfigurasi.',
                    'Atur informasi QRIS atau rekening jika tersedia.',
                    'Periksa metode pembayaran yang dapat digunakan pada Kasir.',
                ],
            ],
            [
                'judul' => '👥 Mengelola Pelanggan',
                'kategori' => 'Pelanggan & Piutang',
                'deskripsi' => 'Pelajari cara menyimpan dan mengelola data pelanggan.',
                'langkah' => [
                    'Buka menu Pelanggan.',
                    'Tambahkan pelanggan jika diperlukan.',
                    'Lengkapi informasi yang diminta.',
                    'Simpan dan periksa data pelanggan.',
                ],
            ],
            [
                'judul' => '🧾 Mengelola Utang dan Piutang',
                'kategori' => 'Pelanggan & Piutang',
                'deskripsi' => 'Pelajari pencatatan transaksi utang dan pembayaran piutang.',
                'langkah' => [
                    'Pilih pelanggan yang sesuai.',
                    'Gunakan metode Cashbon jika tersedia dan diperlukan.',
                    'Periksa catatan utang pelanggan.',
                    'Catat pembayaran yang diterima.',
                    'Periksa sisa piutang setelah pembayaran.',
                ],
            ],
            [
                'judul' => '💸 Mencatat Pengeluaran',
                'kategori' => 'Keuangan',
                'deskripsi' => 'Catat biaya operasional untuk membantu memantau keuangan.',
                'langkah' => [
                    'Buka menu Pengeluaran.',
                    'Masukkan informasi pengeluaran.',
                    'Isi nominal dan keterangan dengan benar.',
                    'Simpan dan periksa catatan pengeluaran.',
                ],
            ],
            [
                'judul' => '📊 Memahami Laporan Penjualan',
                'kategori' => 'Laporan Penjualan',
                'deskripsi' => 'Pelajari cara membaca informasi penjualan dan keuangan.',
                'langkah' => [
                    'Buka menu Laporan.',
                    'Pilih periode laporan yang diperlukan.',
                    'Periksa data transaksi dan penjualan.',
                    'Bandingkan omzet, keuntungan, dan pengeluaran.',
                    'Periksa laporan piutang jika diperlukan.',
                ],
            ],
            [
                'judul' => '🏭 Mengelola Supplier',
                'kategori' => 'Supplier & Pembelian',
                'deskripsi' => 'Pelajari pengelolaan data pemasok barang.',
                'langkah' => [
                    'Buka menu Supplier.',
                    'Tambahkan informasi pemasok.',
                    'Lengkapi data yang diperlukan.',
                    'Simpan dan periksa daftar supplier.',
                ],
            ],
            [
                'judul' => '📥 Mencatat Pembelian Stok',
                'kategori' => 'Supplier & Pembelian',
                'deskripsi' => 'Pelajari pencatatan pembelian barang untuk persediaan.',
                'langkah' => [
                    'Buka menu Pembelian.',
                    'Pilih supplier jika diperlukan.',
                    'Masukkan produk dan jumlah pembelian.',
                    'Periksa nominal pembelian.',
                    'Simpan dan periksa hasil pencatatan.',
                ],
            ],
            [
                'judul' => '🔄 Transfer Stok Antartoko',
                'kategori' => 'Transfer Stok',
                'deskripsi' => 'Pelajari pemindahan persediaan antartoko jika fitur tersedia.',
                'langkah' => [
                    'Pastikan akun memiliki akses ke toko terkait.',
                    'Buka menu Transfer.',
                    'Pilih toko asal dan toko tujuan.',
                    'Pilih produk serta jumlah yang akan dipindahkan.',
                    'Periksa dan selesaikan proses transfer.',
                ],
            ],
            [
                'judul' => '↩️ Mengelola Retur',
                'kategori' => 'Retur Transaksi',
                'deskripsi' => 'Kenali proses retur pembelian dan transaksi penjualan.',
                'langkah' => [
                    'Buka transaksi atau pembelian yang berkaitan.',
                    'Periksa data dan barang yang akan diretur.',
                    'Masukkan informasi retur sesuai kondisi sebenarnya.',
                    'Periksa kembali sebelum menyimpan.',
                ],
            ],
            [
                'judul' => '👨‍💼 Mengelola Staf',
                'kategori' => 'Pengguna & Staf',
                'deskripsi' => 'Pelajari pengelolaan pengguna dan akses staf toko.',
                'langkah' => [
                    'Buka menu pengelolaan pengguna jika tersedia.',
                    'Periksa akun yang terdaftar pada toko.',
                    'Atur akses sesuai peran pengguna.',
                    'Pastikan staf menggunakan akun masing-masing.',
                ],
            ],
            [
                'judul' => '📍 Menggunakan Absensi',
                'kategori' => 'Absensi',
                'deskripsi' => 'Pelajari check-in, check-out, dan riwayat kehadiran.',
                'langkah' => [
                    'Buka menu Absensi.',
                    'Izinkan akses lokasi jika diminta.',
                    'Lakukan check-in sesuai prosedur.',
                    'Lakukan check-out ketika selesai bekerja.',
                    'Periksa riwayat atau rekap absensi.',
                ],
            ],
            [
                'judul' => '🧾 Mengatur Struk Penjualan',
                'kategori' => 'Pengaturan Toko',
                'deskripsi' => 'Pelajari pengaturan struk sesuai pilihan yang tersedia.',
                'langkah' => [
                    'Buka pengaturan struk.',
                    'Periksa informasi toko pada struk.',
                    'Sesuaikan pengaturan yang tersedia.',
                    'Periksa hasil struk setelah transaksi.',
                ],
            ],
            [
                'judul' => '💾 Backup dan Pemulihan Data',
                'kategori' => 'Backup & Keamanan',
                'deskripsi' => 'Kenali fitur pencadangan dan pemulihan data jika tersedia.',
                'langkah' => [
                    'Buka menu Backup.',
                    'Periksa pilihan pencadangan yang tersedia.',
                    'Simpan cadangan sesuai prosedur aplikasi.',
                    'Sebelum memulihkan data, periksa sumber dan isi cadangan.',
                    'Jalankan pemulihan hanya setelah memahami dampaknya.',
                ],
            ],
        ];
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">

        @foreach($panduanPenggunaan as $index => $panduan)

            <details class="group bg-gray-800/80 border border-gray-700 rounded-2xl p-4">

                <summary class="flex items-center justify-between gap-3 cursor-pointer list-none">
                    <span class="text-sm font-semibold text-white">
                        {{ $panduan['judul'] }}
                    </span>

                    <span class="shrink-0 text-gray-400 transition group-open:rotate-180">
                        ⌄
                    </span>
                </summary>

                <div class="mt-3 border-t border-gray-700 pt-3">

                    <p class="text-sm text-gray-300 leading-6">
                        {{ $panduan['deskripsi'] }}
                    </p>

                    <ol class="list-decimal pl-5 mt-2 space-y-1 text-sm text-gray-300 leading-6">
                        @foreach($panduan['langkah'] as $langkah)
                            <li>{{ $langkah }}</li>
                        @endforeach
                    </ol>

                    <a
                        href="{{ route('support.videos', ['kategori' => $panduan['kategori']]) }}"
                        class="mt-4 inline-flex items-center gap-2 rounded-xl
                               border border-emerald-500/30 bg-emerald-500/10
                               px-4 py-2.5 text-sm font-semibold text-emerald-400
                               transition hover:bg-emerald-500/20"
                    >
                        🎬 Lihat Video Tutorial
                        <span>→</span>
                    </a>

                </div>

            </details>

        @endforeach

    </div>

</section>

{{-- FAQ --}}

<section id="faq" class="mb-8 scroll-mt-24"><div class="mb-4">
    <h2 class="text-lg font-bold text-white">❓ Pertanyaan Umum</h2>
    <p class="text-sm text-gray-400 mt-1">
        Jawaban untuk beberapa pertanyaan yang sering diajukan.
    </p>
</div>

<div class="space-y-3">

    <details class="group bg-gray-800/80 border border-gray-700 rounded-2xl p-4">
        <summary class="flex items-center justify-between gap-3 cursor-pointer list-none">
            <span class="text-sm font-semibold text-white">Bagaimana cara menambahkan produk?</span>
            <span class="text-gray-400 group-open:rotate-180 transition">⌄</span>
        </summary>
        <p class="text-sm text-gray-300 leading-6 mt-3 border-t border-gray-700 pt-3">
            Buka menu Produk, pilih fitur tambah produk, lalu isi informasi barang,
            harga, kategori, dan stok sesuai kebutuhan toko.
        </p>
    </details>

    <details class="group bg-gray-800/80 border border-gray-700 rounded-2xl p-4">
        <summary class="flex items-center justify-between gap-3 cursor-pointer list-none">
            <span class="text-sm font-semibold text-white">Bagaimana cara mengganti paket langganan?</span>
            <span class="text-gray-400 group-open:rotate-180 transition">⌄</span>
        </summary>
        <p class="text-sm text-gray-300 leading-6 mt-3 border-t border-gray-700 pt-3">
            Buka halaman Langganan atau Paket pada aplikasi untuk melihat pilihan paket
            yang tersedia dan mengikuti proses berlangganan.
        </p>
    </details>

    <details class="group bg-gray-800/80 border border-gray-700 rounded-2xl p-4">
        <summary class="flex items-center justify-between gap-3 cursor-pointer list-none">
            <span class="text-sm font-semibold text-white">Mengapa fitur tertentu tidak bisa digunakan?</span>
            <span class="text-gray-400 group-open:rotate-180 transition">⌄</span>
        </summary>
        <p class="text-sm text-gray-300 leading-6 mt-3 border-t border-gray-700 pt-3">
            Fitur mungkin memerlukan paket tertentu, izin akses yang sesuai,
            atau pengaturan tambahan. Periksa informasi paket dan akses akun Anda.
            Jika kendala tetap terjadi, buat tiket bantuan.
        </p>
    </details>

    <details class="group bg-gray-800/80 border border-gray-700 rounded-2xl p-4">
        <summary class="flex items-center justify-between gap-3 cursor-pointer list-none">
            <span class="text-sm font-semibold text-white">Bagaimana jika transaksi bermasalah?</span>
            <span class="text-gray-400 group-open:rotate-180 transition">⌄</span>
        </summary>
        <p class="text-sm text-gray-300 leading-6 mt-3 border-t border-gray-700 pt-3">
            Periksa status transaksi dan riwayat penjualan terlebih dahulu.
            Jangan langsung mengulangi pembayaran sebelum memastikan status transaksi
            sebelumnya untuk menghindari pencatatan ganda. Jika masalah berlanjut,
            hubungi tim dukungan melalui tiket bantuan.
        </p>
    </details>

    <details class="group bg-gray-800/80 border border-gray-700 rounded-2xl p-4">
        <summary class="flex items-center justify-between gap-3 cursor-pointer list-none">
            <span class="text-sm font-semibold text-white">Bagaimana cara melaporkan masalah?</span>
            <span class="text-gray-400 group-open:rotate-180 transition">⌄</span>
        </summary>
        <p class="text-sm text-gray-300 leading-6 mt-3 border-t border-gray-700 pt-3">
            Buka Tiket Bantuan, pilih kategori yang sesuai, tuliskan kendala secara jelas,
            lalu kirim. Anda dapat membuka kembali tiket tersebut untuk melihat
            tanggapan tim Kasir½M.
        </p>
    </details>

</div>

</section>

{{-- Informasi Langganan --}}

<section id="langganan" class="mb-8 scroll-mt-24"><div class="mb-4">
    <h2 class="text-lg font-bold text-white">💳 Paket & Langganan</h2>
    <p class="text-sm text-gray-400 mt-1">
        Kenali pilihan paket dan fitur Kasir½M.
    </p>
</div>

<div class="bg-gray-800/80 border border-gray-700 rounded-2xl p-5">

    <div class="flex items-start gap-4">
        <div class="w-12 h-12 shrink-0 rounded-xl bg-emerald-500/10 flex items-center justify-center text-2xl">
            🚀
        </div>

        <div class="flex-1">
            <h3 class="text-sm font-semibold text-white">
                Pilih paket sesuai kebutuhan usaha
            </h3>

            <p class="text-sm text-gray-400 mt-2 leading-6">
                Pelajari fitur yang tersedia pada paket Free, Pro, dan Premium.
                Pastikan paket pilihanmu sesuai dengan kebutuhan toko.
            </p>

            <p class="text-xs text-gray-500 mt-3">
                Informasi harga, masa aktif, dan fitur mengikuti data paket yang tersedia di aplikasi.
            </p>
        </div>
    </div>

    {{-- Ganti URL ini dengan route paket yang benar jika sudah dipastikan --}}
    <div class="mt-5">
        <a href="{{ url('/paket') }}"
           class="inline-flex items-center justify-center gap-2 w-full sm:w-auto
                  px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500
                  text-white text-sm font-semibold transition">
            Lihat Paket & Langganan
            <span>→</span>
        </a>
    </div>

</div>

</section>

{{-- Pengumuman Dinamis --}}

<section id="pengumuman" class="mb-8 scroll-mt-24">

    <div class="mb-4">
        <h2 class="text-lg font-bold text-white">
            🔔 Pengumuman
        </h2>

        <p class="text-sm text-gray-400 mt-1">
            Informasi dan kabar terbaru dari Kasir½M.
        </p>
    </div>

    @if(isset($announcements) && $announcements->isNotEmpty())

        <div class="space-y-3">

            @foreach($announcements as $announcement)

                <div class="bg-gray-800/80 border border-gray-700
                            rounded-2xl p-5">

                    <div class="flex items-start gap-4">

                        <div class="w-12 h-12 shrink-0 rounded-xl
                                    bg-blue-500/10 flex items-center
                                    justify-center text-2xl">
                            📢
                        </div>

                        <div class="flex-1 min-w-0">

                            <h3 class="text-sm font-semibold text-white">
                                {{ $announcement->title }}
                            </h3>

                            <p class="text-xs text-gray-500 mt-1">
                                Diterbitkan
                                {{ $announcement->published_at?->format('d M Y, H:i') }}
                            </p>

                            <p class="text-sm text-gray-300 mt-3 leading-6">
                                {{ \Illuminate\Support\Str::limit(
                                    strip_tags($announcement->content),
                                    180
                                ) }}
                            </p>

                            <a
                                href="{{ route(
                                    'announcements.show',
                                    $announcement->id
                                ) }}"
                                class="inline-flex items-center gap-2 mt-4
                                       px-4 py-2 rounded-xl
                                       bg-emerald-600 hover:bg-emerald-500
                                       text-white text-sm font-semibold
                                       transition"
                            >
                                Lihat Pengumuman
                                <span>→</span>
                            </a>

                        </div>

                    </div>

                </div>

            @endforeach

        </div>

    @else

        <div class="bg-gray-800/80 border border-gray-700
                    rounded-2xl p-6 text-center">

            <div class="w-14 h-14 mx-auto mb-3 rounded-2xl
                        bg-blue-500/10 flex items-center
                        justify-center text-2xl">
                📢
            </div>

            <h3 class="text-sm font-semibold text-white">
                Belum Ada Pengumuman
            </h3>

            <p class="text-sm text-gray-400 mt-2 leading-6">
                Saat ini belum ada pengumuman yang tersedia
                untuk akun Anda.
            </p>

        </div>

    @endif

</section>

{{-- Hubungi Kami --}}

<section id="kontak" class="mb-8 scroll-mt-24"><div class="mb-4">
    <h2 class="text-lg font-bold text-white">💬 Hubungi Kami</h2>
    <p class="text-sm text-gray-400 mt-1">
        Tim Kasir½M siap membantu jika kamu mengalami kendala.
    </p>
</div>

<div class="bg-gray-800/80 border border-gray-700 rounded-2xl p-5">

    <div class="flex items-start gap-4">
        <div class="w-12 h-12 shrink-0 rounded-xl bg-cyan-500/10 flex items-center justify-center text-2xl">
            🎧
        </div>

        <div class="flex-1">
            <h3 class="text-sm font-semibold text-white">
                Butuh bantuan lebih lanjut?
            </h3>

            <p class="text-sm text-gray-400 mt-2 leading-6">
                Sampaikan pertanyaan atau kendala melalui tiket bantuan.
                Kamu bisa memantau status tiket dan membaca balasan tim
                langsung melalui aplikasi.
            </p>

            <a href="{{ route('support.create') }}"
               class="inline-flex items-center gap-2 mt-4 px-4 py-2.5
                      rounded-xl bg-cyan-600 hover:bg-cyan-500
                      text-white text-sm font-semibold transition">
                Buat Tiket Bantuan
                <span>→</span>
            </a>
        </div>
    </div>

</div>

</section>

{{-- Daftar Tiket --}}

<section id="daftar-tiket" class="mb-6 scroll-mt-24">
    <div class="mb-4">
        <h2 class="text-lg font-bold text-white">
            🎫 Tiket Bantuan Saya
        </h2>

        <p class="text-sm text-gray-400 mt-1">
            Pantau status tiket dan balasan dari tim Kasir½M.
        </p>
    </div>

    {{-- Daftar Tiket --}}
    @if($tickets->count())

        <div class="space-y-3">

            @foreach($tickets as $ticket)

                <a
                    href="{{ route('support.show', $ticket) }}"
                    class="block bg-gray-800/80 border border-gray-700
                           hover:border-gray-600 hover:bg-gray-800
                           rounded-2xl p-4 sm:p-5 transition"
                >

                    <div class="flex items-start justify-between gap-4">

                        <div class="min-w-0 flex-1">

                            {{-- Ticket Number --}}
                            <div class="flex flex-wrap items-center gap-2 mb-2">
                                <span class="text-xs font-mono text-gray-500">
                                    #{{ $ticket->ticket_number }}
                                </span>

                                {{-- Status --}}
                                @php
                                    $status = [
                                        'open' => [
                                            'label' => 'Baru',
                                            'class' => 'bg-blue-500/10 text-blue-400 border-blue-500/20'
                                        ],
                                        'processing' => [
                                            'label' => 'Diproses',
                                            'class' => 'bg-yellow-500/10 text-yellow-400 border-yellow-500/20'
                                        ],
                                        'waiting' => [
                                            'label' => 'Menunggu',
                                            'class' => 'bg-orange-500/10 text-orange-400 border-orange-500/20'
                                        ],
                                        'resolved' => [
                                            'label' => 'Selesai',
                                            'class' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20'
                                        ],
                                        'closed' => [
                                            'label' => 'Ditutup',
                                            'class' => 'bg-gray-500/10 text-gray-400 border-gray-500/20'
                                        ],
                                    ];

                                    $statusData = $status[$ticket->status] ?? [
                                        'label' => ucfirst($ticket->status),
                                        'class' => 'bg-gray-500/10 text-gray-400 border-gray-500/20'
                                    ];
                                @endphp

                                <span class="inline-flex items-center px-2 py-1 rounded-lg
                                             border text-[11px] font-medium
                                             {{ $statusData['class'] }}">
                                    {{ $statusData['label'] }}
                                </span>
                            </div>

                            {{-- Subject --}}
                            <h2 class="text-sm sm:text-base font-semibold text-white truncate">
                                {{ $ticket->subject }}
                            </h2>

                            {{-- Store --}}
                            @if($ticket->store)
                                <p class="text-xs text-gray-400 mt-1">
                                    🏪 {{ $ticket->store->name }}
                                </p>
                            @endif

                            {{-- Category --}}
                            <p class="text-xs text-gray-500 mt-2">
                                {{ ucfirst($ticket->category) }}
                                ·
                                {{ $ticket->created_at?->format('d M Y, H:i') }}
                            </p>

                        </div>

                        {{-- Arrow --}}
                        <div class="text-gray-500 pt-1">
                            →
                        </div>

                    </div>

                </a>

            @endforeach

        </div>

        {{-- Pagination --}}
        @if(method_exists($tickets, 'links'))
            <div class="mt-6">
                {{ $tickets->links() }}
            </div>
        @endif

    @else

        {{-- Empty State --}}
        <div class="bg-gray-800/80 border border-gray-700
                    rounded-2xl px-6 py-12 text-center">

            <div class="w-14 h-14 mx-auto mb-4 rounded-2xl
                        bg-emerald-500/10
                        flex items-center justify-center">
                <span class="text-2xl">💬</span>
            </div>

            <h2 class="text-base sm:text-lg font-semibold text-white">
                Belum ada tiket bantuan
            </h2>

            <p class="text-sm text-gray-400 mt-2 max-w-md mx-auto">
                Jika Anda mengalami masalah atau membutuhkan bantuan,
                silakan buat tiket baru dan tim Kasir½M akan membantu Anda.
            </p>

            <a
                href="{{ route('support.create') }}"
                class="inline-flex items-center gap-2 mt-6
                       px-5 py-2.5 rounded-xl
                       bg-emerald-600 hover:bg-emerald-500
                       text-white text-sm font-semibold transition"
            >
                <span class="text-lg leading-none">+</span>
                Buat Tiket Bantuan
            </a>

        </div>

    @endif
    </section>

</div>

@endsection