<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-900">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Daftar - Smart POS</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-full flex items-center justify-center px-4 py-8">

    <div class="max-w-md w-full bg-gray-800 p-8 rounded-2xl border border-white/10 shadow-xl">

        <!-- HEADER -->
        <div class="text-center mb-6">

            <h2 class="text-2xl font-bold text-white">
                Daftar Smart POS
            </h2>

            <p class="text-sm text-gray-400 mt-1">
                Buat akun dan toko pertama Anda
            </p>

        </div>


        <!-- PESAN ERROR -->
        @if(session('error'))

            <div class="mb-4 bg-red-500/10 border border-red-500/20
                        text-red-400 text-sm rounded-lg px-4 py-3">
                {{ session('error') }}
            </div>

        @endif
        @if(session('success'))

    <div class="mb-4 bg-emerald-500/10 border border-emerald-500/20
                text-emerald-400 text-sm rounded-lg px-4 py-3">
        {{ session('success') }}
    </div>

@endif


        <!-- FORM -->
        <form action="/proses-register" method="POST" class="space-y-5">

            @csrf


            <!-- DATA AKUN -->
            <div>

                <h3 class="text-sm font-semibold text-indigo-400 mb-3">
                    Data Akun
                </h3>

                <div class="space-y-3">

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">
                            Nama
                        </label>

                        <input
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            placeholder="Nama Anda"
                            required
                            class="w-full bg-gray-900 border border-white/10
                                   rounded-lg px-4 py-2.5 text-white
                                   focus:outline-none focus:border-indigo-500 text-sm"
                        >
                    </div>


                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">
                            Email
                        </label>

                        <input
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="email@contoh.com"
                            required
                            class="w-full bg-gray-900 border border-white/10
                                   rounded-lg px-4 py-2.5 text-white
                                   focus:outline-none focus:border-indigo-500 text-sm"
                        >
                    </div>


                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">
                            Password
                        </label>

                        <input
                            type="password"
                            name="password"
                            placeholder="Minimal 8 karakter"
                            required
                            class="w-full bg-gray-900 border border-white/10
                                   rounded-lg px-4 py-2.5 text-white
                                   focus:outline-none focus:border-indigo-500 text-sm"
                        >
                    </div>


                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">
                            Konfirmasi Password
                        </label>

                        <input
                            type="password"
                            name="password_confirmation"
                            placeholder="Ulangi password"
                            required
                            class="w-full bg-gray-900 border border-white/10
                                   rounded-lg px-4 py-2.5 text-white
                                   focus:outline-none focus:border-indigo-500 text-sm"
                        >
                    </div>

                </div>

            </div>


            <!-- DATA TOKO -->
            <div class="border-t border-white/10 pt-5">

                <h3 class="text-sm font-semibold text-emerald-400 mb-3">
                    Toko Pertama
                </h3>

                <div class="space-y-3">

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">
                            Nama Toko
                        </label>

                        <input
                            type="text"
                            name="store_name"
                            value="{{ old('store_name') }}"
                            placeholder="Contoh: Toko Sejahtera"
                            required
                            class="w-full bg-gray-900 border border-white/10
                                   rounded-lg px-4 py-2.5 text-white
                                   focus:outline-none focus:border-emerald-500 text-sm"
                        >
                    </div>


                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">
                            Alamat Toko
                        </label>

                        <textarea
                            name="store_address"
                            rows="2"
                            placeholder="Alamat toko"
                            class="w-full bg-gray-900 border border-white/10
                                   rounded-lg px-4 py-2.5 text-white
                                   focus:outline-none focus:border-emerald-500 text-sm"
                        >{{ old('store_address') }}</textarea>
                    </div>


                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">
                            Nomor Telepon
                        </label>

                        <input
                            type="text"
                            name="store_phone"
                            value="{{ old('store_phone') }}"
                            placeholder="08xxxxxxxxxx"
                            class="w-full bg-gray-900 border border-white/10
                                   rounded-lg px-4 py-2.5 text-white
                                   focus:outline-none focus:border-emerald-500 text-sm"
                        >
                    </div>

                </div>

            </div>


            <!-- BUTTON -->
            <button
                type="submit"
                class="w-full bg-indigo-600 hover:bg-indigo-500
                       text-white font-medium py-2.5 rounded-lg
                       transition shadow-md text-sm"
            >
                Buat Akun & Toko
            </button>

        </form>


        <!-- LOGIN -->
        <div class="text-center mt-5">

            <span class="text-sm text-gray-500">
                Sudah punya akun?
            </span>

            <a
                href="{{ route('login') }}"
                class="text-sm text-indigo-400 hover:text-indigo-300 font-medium"
            >
                Masuk di sini
            </a>

        </div>

    </div>

</body>
</html>
