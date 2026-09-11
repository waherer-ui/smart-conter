<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-900">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - Smart POS</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="h-full flex items-center justify-center px-4">

    <div class="max-w-md w-full bg-gray-800 p-8 rounded-2xl border border-white/10 shadow-xl space-y-6">

        <div class="text-center">
            <h2 class="text-2xl font-bold text-white">
                Email & Password
            </h2>

            <p class="text-sm text-gray-400 mt-1">
                Silakan masuk sesuai hak akses Anda
            </p>
        </div>


        <form action="/proses-login" method="POST" class="space-y-4">

            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">
                    Username / Email
                </label>

                <input
                    type="text"
                    name="username"
                    placeholder="Masukkan username..."
                    required
                    class="w-full bg-gray-900 border border-white/10 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-indigo-500 text-sm"
                >
            </div>


            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">
                    Password
                </label>

                <input
                    type="password"
                    name="password"
                    placeholder="••••••••"
                    required
                    class="w-full bg-gray-900 border border-white/10 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-indigo-500 text-sm"
                >
            </div>


            <button
                type="submit"
                class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-medium py-2.5 rounded-lg transition shadow-md text-sm"
            >
                Masuk
            </button>

        </form>


        {{-- Registrasi --}}
        <div class="text-center pt-2 border-t border-white/10">

            <p class="text-sm text-gray-400">
                Belum punya akun?
            </p>

            <a
                href="{{ route('register') }}"
                class="inline-block mt-2 text-indigo-400 hover:text-indigo-300 font-medium text-sm transition"
            >
                Daftar akun baru →
            </a>

        </div>

    </div>

</body>

</html>