<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-950">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - Kasir½M</title>

    <link
        rel="icon"
        type="image/png"
        href="{{ asset('images/Favicon.png') }}"
    >

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-full flex items-center justify-center px-4 py-8">

    <div class="w-full max-w-md">

        {{-- Card --}}
        <div class="bg-gray-900/90 p-6 sm:p-8 rounded-2xl border border-white/10 shadow-2xl">

            {{-- Logo --}}
            <header class="flex justify-center mb-6">
                <a href="/" class="inline-flex items-center">
                    <img
                        src="{{ asset('images/Icon-navbar.png') }}"
                        alt="Kasir½M"
                        class="h-11 w-auto object-contain"
                    >
                </a>
            </header>


{{-- Heading --}}
@if (!isset($resetToken))
    <div class="text-center mb-6">

        <h2 class="text-xl sm:text-2xl font-bold text-white">
            Selamat Datang Kembali
        </h2>

        <p class="text-xs sm:text-sm text-gray-400 mt-1">
            Masuk untuk melanjutkan ke Kasir½M
        </p>

    </div>
@endif


{{-- Flash Success --}}
@if (session('success'))
    <div class="mb-4 rounded-lg border border-emerald-500/20 bg-emerald-500/10 px-4 py-3">
        <p class="text-xs text-emerald-400">
            {{ session('success') }}
        </p>
    </div>
@endif


{{-- Flash Error --}}
@if (session('error'))
    <div class="mb-4 rounded-lg border border-red-500/20 bg-red-500/10 px-4 py-3">
        <p class="text-xs text-red-400">
            {{ session('error') }}
        </p>
    </div>
@endif


{{-- Validation Error --}}
@if ($errors->any())
    <div class="mb-4 rounded-lg border border-red-500/20 bg-red-500/10 px-4 py-3">
        @foreach ($errors->all() as $error)
            <p class="text-xs text-red-400">
                {{ $error }}
            </p>
        @endforeach
    </div>
@endif


            {{-- Login Form --}}
            <form
                  id="loginForm"
                  action="/proses-login"
                  method="POST"
                  class="space-y-4 {{ isset($resetToken) ? 'hidden' : '' }}"
              >

                @csrf

                {{-- Username / Email --}}
                <div>
                    <label class="block text-xs font-medium text-gray-300 mb-1.5">
                        Username / Email
                    </label>

                    <input
                        type="text"
                        name="username"
                        value="{{ old('username') }}"
                        placeholder="Masukkan username atau email..."
                        required
                        autocomplete="username"
                        class="w-full h-11 bg-gray-950 border border-white/10 rounded-xl px-4 text-sm text-white placeholder-gray-600 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition"
                    >
                </div>


                {{-- Password --}}
<div>
    <div class="flex items-center justify-between mb-1.5">

        <label class="block text-xs font-medium text-gray-300">
            Password
        </label>

        <button
            type="button"
            onclick="showForgotPassword()"
            class="text-[11px] text-emerald-400 hover:text-emerald-300 transition"
        >
            Lupa password?
        </button>

    </div>

    <div class="relative">

        <input
            type="password"
            name="password"
            id="password"
            placeholder="••••••••"
            required
            autocomplete="current-password"
            class="w-full h-11 bg-gray-950 border border-white/10 rounded-xl px-4 pr-12 text-sm text-white placeholder-gray-600 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition"
        >

        <button
            type="button"
            onclick="togglePassword()"
            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-emerald-400 transition"
            aria-label="Tampilkan password"
        >
            👁
        </button>

    </div>
</div>


                {{-- Login --}}
                <button
                    type="submit"
                    class="w-full h-11 bg-emerald-500 hover:bg-emerald-400 text-gray-950 font-semibold rounded-xl transition shadow-lg shadow-emerald-500/10 text-sm"
                >
                    Masuk
                </button>

            </form>
            
            {{-- Forgot Password Form --}}
<div id="forgotPasswordForm" class="hidden">

    <div class="text-center mb-6">

        <h2 class="text-xl sm:text-2xl font-bold text-white">
            Lupa Password?
        </h2>

        <p class="text-xs sm:text-sm text-gray-400 mt-1">
            Masukkan email akun Anda untuk mendapatkan link reset password.
        </p>

    </div>


    <form
        action="{{ route('password.email') }}"
        method="POST"
        class="space-y-4"
    >

        @csrf

        <div>

            <label class="block text-xs font-medium text-gray-300 mb-1.5">
                Email
            </label>

            <input
                type="email"
                name="email"
                value="{{ old('email') }}"
                placeholder="Masukkan email Anda..."
                required
                autocomplete="email"
                class="w-full h-11 bg-gray-950 border border-white/10 rounded-xl px-4 text-sm text-white placeholder-gray-600 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition"
            >

        </div>


        <button
            type="submit"
            class="w-full h-11 bg-emerald-500 hover:bg-emerald-400 text-gray-950 font-semibold rounded-xl transition shadow-lg shadow-emerald-500/10 text-sm"
        >
            Kirim Link Reset
        </button>

    </form>


    <div class="text-center mt-5">

        <button
            type="button"
            onclick="showLogin()"
            class="text-sm text-emerald-400 hover:text-emerald-300 font-semibold transition"
        >
            ← Kembali ke Login
        </button>

    </div>

</div>

{{-- Reset Password Form --}}
<div
    id="resetPasswordForm"
    class="{{ isset($resetToken) ? '' : 'hidden' }}"
>

    <div class="text-center mb-6">

        <h2 class="text-xl sm:text-2xl font-bold text-white">
            Buat Password Baru
        </h2>

        <p class="text-xs sm:text-sm text-gray-400 mt-1">
            Masukkan password baru untuk akun Kasir½M Anda.
        </p>

    </div>


    <form
        action="{{ route('password.update') }}"
        method="POST"
        class="space-y-4"
    >

        @csrf

        {{-- Token --}}
        <input
            type="hidden"
            name="token"
            value="{{ $resetToken ?? '' }}"
        >


        {{-- Email --}}
        <div>

            <label class="block text-xs font-medium text-gray-300 mb-1.5">
                Email
            </label>

            <input
                type="email"
                name="email"
                value="{{ old('email', $resetEmail ?? '') }}"
                required
                autocomplete="email"
                placeholder="Masukkan email Anda..."
                class="w-full h-11 bg-gray-950 border border-white/10 rounded-xl px-4 text-sm text-white placeholder-gray-600 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition"
            >

        </div>


        {{-- Password Baru --}}
        <div>

            <label class="block text-xs font-medium text-gray-300 mb-1.5">
                Password Baru
            </label>

            <div class="relative">

                <input
                    type="password"
                    name="password"
                    id="resetPassword"
                    required
                    minlength="8"
                    autocomplete="new-password"
                    placeholder="Minimal 8 karakter..."
                    class="w-full h-11 bg-gray-950 border border-white/10 rounded-xl px-4 pr-12 text-sm text-white placeholder-gray-600 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition"
                >

                <button
                    type="button"
                    onclick="toggleResetPassword('resetPassword', this)"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-emerald-400 transition"
                    aria-label="Tampilkan password"
                >
                    👁
                </button>

            </div>

        </div>


        {{-- Konfirmasi Password --}}
        <div>

            <label class="block text-xs font-medium text-gray-300 mb-1.5">
                Konfirmasi Password
            </label>

            <div class="relative">

                <input
                    type="password"
                    name="password_confirmation"
                    id="resetPasswordConfirmation"
                    required
                    minlength="8"
                    autocomplete="new-password"
                    placeholder="Ulangi password baru..."
                    class="w-full h-11 bg-gray-950 border border-white/10 rounded-xl px-4 pr-12 text-sm text-white placeholder-gray-600 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition"
                >

                <button
                    type="button"
                    onclick="toggleResetPassword('resetPasswordConfirmation', this)"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-emerald-400 transition"
                    aria-label="Tampilkan password"
                >
                    👁
                </button>

            </div>

        </div>


        {{-- Submit --}}
        <button
            type="submit"
            class="w-full h-11 bg-emerald-500 hover:bg-emerald-400 text-gray-950 font-semibold rounded-xl transition shadow-lg shadow-emerald-500/10 text-sm"
        >
            Simpan Password Baru
        </button>

    </form>


    <div class="text-center mt-5">

        <a
            href="{{ route('login') }}"
            class="text-sm text-emerald-400 hover:text-emerald-300 font-semibold transition"
        >
            ← Kembali ke Login
        </a>

    </div>

</div>


{{-- Login Extra --}}
<div
    id="loginExtra"
    class="{{ isset($resetToken) ? 'hidden' : '' }}"
>

    {{-- Divider --}}
    <div class="flex items-center gap-3 my-6">

        <div class="flex-1 h-px bg-white/10"></div>

        <span class="text-[10px] text-gray-600 uppercase">
            atau
        </span>

        <div class="flex-1 h-px bg-white/10"></div>

    </div>


    {{-- Google --}}
    <a
        href="{{ route('google.redirect', ['mode' => 'login']) }}"
        class="w-full h-11 flex items-center justify-center gap-3
               bg-gray-800 hover:bg-gray-700
               border border-white/10
               text-white
               rounded-xl
               text-sm
               transition duration-200"
    >

        <svg
            class="w-5 h-5"
            viewBox="0 0 24 24"
            aria-hidden="true"
        >

            <path
                fill="#4285F4"
                d="M21.35 12.23c0-.71-.06-1.4-.18-2.05H12v3.88h5.23a4.47 4.47 0 0 1-1.94 2.93v2.44h3.14c1.84-1.69 2.92-4.18 2.92-7.2z"
            />

            <path
                fill="#34A853"
                d="M12 21.6c2.63 0 4.84-.87 6.45-2.35l-3.14-2.44c-.87.58-1.98.92-3.31.92-2.54 0-4.69-1.72-5.46-4.03H3.3v2.52A9.75 9.75 0 0 0 12 21.6z"
            />

            <path
                fill="#FBBC05"
                d="M6.54 13.7a5.87 5.87 0 0 1 0-3.4V7.78H3.3a9.75 9.75 0 0 0 0 8.44l3.24-2.52z"
            />

            <path
                fill="#EA4335"
                d="M12 6.27c1.43 0 2.72.49 3.73 1.45l2.8-2.8C16.83 3.3 14.63 2.4 12 2.4a9.75 9.75 0 0 0-8.7 5.38l3.24 2.52C7.31 7.99 9.46 6.27 12 6.27z"
            />

        </svg>

        <span>Masuk dengan Google</span>

    </a>


    <p class="text-center text-[9px] text-gray-700 mt-2">
        Google Sign-In akan tersedia setelah konfigurasi akun.
    </p>


    {{-- Register --}}
    <div class="text-center mt-6 pt-5 border-t border-white/10">

        <p class="text-xs text-gray-400">
            Belum punya akun?
        </p>

        <a
            href="{{ route('register') }}"
            class="inline-block mt-2 text-emerald-400 hover:text-emerald-300 font-semibold text-sm transition"
        >
            Daftar akun baru →
        </a>

    </div>

</div>
{{-- End Login Extra --}}


        {{-- Footer --}}
        <footer class="mt-5 text-center">

            <div class="flex items-center justify-center gap-1.5">

                <span class="text-[9px] text-gray-600">
                    © {{ date('Y') }}
                </span>

                <img
                    src="{{ asset('images/Icon-navbar.png') }}"
                    alt="Kasir½M"
                    class="h-5 w-auto object-contain"
                >

            </div>

            <p class="text-[9px] text-gray-700 mt-1">
                Solusi kasir untuk usaha Anda.
            </p>

        </footer>

    </div>


<script>
    function togglePassword() {

        const password = document.getElementById('password');

        password.type =
            password.type === 'password'
                ? 'text'
                : 'password';
    }
    
    function toggleResetPassword(id, button) {

    const input = document.getElementById(id);

    if (input.type === 'password') {

        input.type = 'text';
        button.textContent = '🙈';

    } else {

        input.type = 'password';
        button.textContent = '👁';

    }
}


    function showForgotPassword() {

        document
            .getElementById('loginForm')
            .classList.add('hidden');

        document
            .getElementById('forgotPasswordForm')
            .classList.remove('hidden');

        document
            .getElementById('loginExtra')
            .classList.add('hidden');
    }


    function showLogin() {

        document
            .getElementById('forgotPasswordForm')
            .classList.add('hidden');

        document
            .getElementById('loginForm')
            .classList.remove('hidden');

        document
            .getElementById('loginExtra')
            .classList.remove('hidden');
    }
</script>

</body>

</html>