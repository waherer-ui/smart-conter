<!DOCTYPE html>
<html lang="id" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link
        rel="icon"
        type="image/png"
        href="{{ asset('images/Favicon.png') }}"
    >

    <title>Daftar - Kasir½M</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        html,
        body {
            min-height: 100%;
        }

        body {
            background:
                radial-gradient(
                    circle at 15% 20%,
                    rgba(16, 185, 129, 0.14),
                    transparent 28%
                ),
                radial-gradient(
                    circle at 85% 75%,
                    rgba(132, 204, 22, 0.11),
                    transparent 30%
                ),
                radial-gradient(
                    circle at 50% 100%,
                    rgba(16, 185, 129, 0.07),
                    transparent 35%
                ),
                #020807;
        }

        .background-glow {
            position: fixed;
            inset: 0;
            pointer-events: none;
            overflow: hidden;
            z-index: 0;
        }

        .background-glow::before {
            content: "";
            position: absolute;
            width: 420px;
            height: 420px;
            left: -180px;
            top: 20%;
            background: rgba(34, 197, 94, 0.09);
            filter: blur(110px);
            border-radius: 9999px;
        }

        .background-glow::after {
            content: "";
            position: absolute;
            width: 380px;
            height: 380px;
            right: -160px;
            bottom: 5%;
            background: rgba(132, 204, 22, 0.08);
            filter: blur(110px);
            border-radius: 9999px;
        }

        .grid-overlay {
            position: fixed;
            inset: 0;
            pointer-events: none;
            opacity: 0.025;
            background-image:
                linear-gradient(
                    rgba(255, 255, 255, 0.5) 1px,
                    transparent 1px
                ),
                linear-gradient(
                    90deg,
                    rgba(255, 255, 255, 0.5) 1px,
                    transparent 1px
                );
            background-size: 40px 40px;
        }

        .auth-card {
            background: rgba(7, 15, 22, 0.94);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
        }

        .input-field {
            background: rgba(3, 10, 15, 0.82);
        }

        .input-field:focus {
            box-shadow:
                0 0 0 1px rgba(16, 185, 129, 0.45),
                0 0 20px rgba(16, 185, 129, 0.05);
        }

        .cta-button {
            background: linear-gradient(
                135deg,
                #a3e635,
                #84cc16
            );
            color: #071008;
        }

        .cta-button:hover {
            filter: brightness(1.08);
            transform: translateY(-1px);
        }

        .cta-button:active {
            transform: translateY(0);
        }

        .readonly-field {
            background: rgba(16, 185, 129, 0.045);
            color: #d1fae5;
        }
    </style>
</head>

<body class="min-h-screen text-white antialiased">

    @php
        $googleRegister = session('google_register');
    @endphp

    <div class="background-glow"></div>
    <div class="grid-overlay"></div>

    <!-- MAIN -->
    <div class="relative z-10 min-h-screen px-5 py-7 sm:px-8">

        <div class="max-w-5xl mx-auto min-h-[calc(100vh-3.5rem)] flex flex-col">

            <!-- BRAND -->
            <header class="flex items-center">

                <a
                    href="/"
                    class="inline-flex items-center transition-opacity hover:opacity-80"
                    aria-label="Kasir½M"
                >
                    <img
                        src="{{ asset('images/Icon-navbar.png') }}"
                        alt="Kasir½M"
                        class="h-11 sm:h-12 w-auto object-contain"
                    >
                </a>

            </header>


            <!-- CENTER -->
            <main class="flex-1 flex items-center justify-center py-8 sm:py-10">

                <div class="w-full max-w-[430px]">

                    <!-- CARD -->
                    <div
                        class="auth-card
                               border border-white/10
                               rounded-2xl
                               shadow-2xl
                               shadow-black/40
                               overflow-hidden"
                    >

                        <!-- CARD HEADER -->
                        <div class="px-6 sm:px-7 pt-7 pb-5">

                            <div class="text-center">

                                <h1
                                    class="text-xl sm:text-2xl
                                           font-bold
                                           tracking-tight"
                                >
                                    Buat akun
                                    <span class="text-emerald-400">Kasir</span><span class="text-white">½M</span>
                                </h1>

                                <p
                                    class="text-[11px] sm:text-xs
                                           text-gray-500
                                           mt-2
                                           leading-relaxed"
                                >
                                    Mulai kelola penjualan dan toko Anda
                                    dengan lebih mudah.
                                </p>

                            </div>

                        </div>


                        <!-- FORM AREA -->
                        <div class="px-6 sm:px-7 pb-7">

                            <!-- SESSION ERROR -->
                            @if(session('error'))

                                <div
                                    class="mb-4
                                           flex items-start gap-2.5
                                           bg-red-500/10
                                           border border-red-500/20
                                           rounded-lg
                                           px-3 py-2.5"
                                >

                                    <svg
                                        class="w-4 h-4 text-red-400 flex-shrink-0 mt-0.5"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                        aria-hidden="true"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M12 8v4m0 4h.01
                                               M10.29 3.86l-7.82 13
                                               a2 2 0 001.71 3.14h15.64
                                               a2 2 0 001.71-3.14l-7.82-13
                                               a2 2 0 00-3.42 0z"
                                        />
                                    </svg>

                                    <p class="text-xs text-red-300">
                                        {{ session('error') }}
                                    </p>

                                </div>

                            @endif


                            <!-- SESSION SUCCESS -->
                            @if(session('success'))

                                <div
                                    class="mb-4
                                           flex items-start gap-2.5
                                           bg-emerald-500/10
                                           border border-emerald-500/20
                                           rounded-lg
                                           px-3 py-2.5"
                                >

                                    <svg
                                        class="w-4 h-4 text-emerald-400 flex-shrink-0 mt-0.5"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                        aria-hidden="true"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M5 13l4 4L19 7"
                                        />
                                    </svg>

                                    <p class="text-xs text-emerald-300">
                                        {{ session('success') }}
                                    </p>

                                </div>

                            @endif


                            <!-- FORM -->
                            <form
                                action="/proses-register"
                                method="POST"
                                class="space-y-4"
                            >

                                @csrf


                                <!-- NAMA -->
                                <div>

                                    <label
                                        for="name"
                                        class="block
                                               text-[11px]
                                               font-medium
                                               text-gray-300
                                               mb-1.5"
                                    >
                                        Nama Lengkap
                                    </label>

                                    <input
                                        id="name"
                                        type="text"
                                        name="name"
                                        value="{{ old('name', $googleRegister['name'] ?? '') }}"
                                        placeholder="Nama Anda"
                                        required
                                        autocomplete="name"
                                        @if($googleRegister) readonly @endif
                                        class="input-field
                                               @if($googleRegister) readonly-field @endif
                                               w-full
                                               h-10
                                               border border-white/10
                                               rounded-lg
                                               px-3.5
                                               text-xs
                                               text-white
                                               placeholder-gray-600
                                               outline-none
                                               transition
                                               focus:border-emerald-500/60
                                               @if($googleRegister) cursor-default @endif"
                                    >

                                    @error('name')
                                        <p class="mt-1 text-[10px] text-red-400">
                                            {{ $message }}
                                        </p>
                                    @enderror

                                </div>


                                <!-- EMAIL -->
                                <div>

                                    <label
                                        for="email"
                                        class="block
                                               text-[11px]
                                               font-medium
                                               text-gray-300
                                               mb-1.5"
                                    >
                                        Email
                                    </label>

                                    <input
                                        id="email"
                                        type="email"
                                        name="email"
                                        value="{{ old('email', $googleRegister['email'] ?? '') }}"
                                        placeholder="email@contoh.com"
                                        required
                                        autocomplete="email"
                                        @if($googleRegister) readonly @endif
                                        class="input-field
                                               @if($googleRegister) readonly-field @endif
                                               w-full
                                               h-10
                                               border border-white/10
                                               rounded-lg
                                               px-3.5
                                               text-xs
                                               text-white
                                               placeholder-gray-600
                                               outline-none
                                               transition
                                               focus:border-emerald-500/60
                                               @if($googleRegister) cursor-default @endif"
                                    >

                                    @error('email')
                                        <p class="mt-1 text-[10px] text-red-400">
                                            {{ $message }}
                                        </p>
                                    @enderror

                                    @if($googleRegister)

                                        <div class="flex items-center gap-1.5 mt-1.5">

                                            <svg
                                                class="w-3.5 h-3.5 text-emerald-400"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                                aria-hidden="true"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M5 13l4 4L19 7"
                                                />
                                            </svg>

                                            <span class="text-[9px] text-emerald-400">
                                                Akun Google terhubung
                                            </span>

                                        </div>

                                    @endif

                                </div>


                                <!-- NAMA TOKO -->
                                <div>

                                    <label
                                        for="store_name"
                                        class="block
                                               text-[11px]
                                               font-medium
                                               text-gray-300
                                               mb-1.5"
                                    >
                                        Nama Toko
                                    </label>

                                    <input
                                        id="store_name"
                                        type="text"
                                        name="store_name"
                                        value="{{ old('store_name') }}"
                                        placeholder="Contoh: Toko Sejahtera"
                                        required
                                        autocomplete="organization"
                                        class="input-field
                                               w-full
                                               h-10
                                               border border-white/10
                                               rounded-lg
                                               px-3.5
                                               text-xs
                                               text-white
                                               placeholder-gray-600
                                               outline-none
                                               transition
                                               focus:border-emerald-500/60"
                                    >

                                    @error('store_name')
                                        <p class="mt-1 text-[10px] text-red-400">
                                            {{ $message }}
                                        </p>
                                    @enderror

                                </div>


                                <!-- ALAMAT -->
                                <div>

                                    <label
                                        for="store_address"
                                        class="block
                                               text-[11px]
                                               font-medium
                                               text-gray-300
                                               mb-1.5"
                                    >
                                        Alamat Toko
                                    </label>

                                    <textarea
                                        id="store_address"
                                        name="store_address"
                                        rows="2"
                                        placeholder="Alamat toko"
                                        autocomplete="street-address"
                                        class="input-field
                                               w-full
                                               min-h-[58px]
                                               border border-white/10
                                               rounded-lg
                                               px-3.5 py-2.5
                                               text-xs
                                               text-white
                                               placeholder-gray-600
                                               resize-none
                                               outline-none
                                               transition
                                               focus:border-emerald-500/60"
                                    >{{ old('store_address') }}</textarea>

                                    @error('store_address')
                                        <p class="mt-1 text-[10px] text-red-400">
                                            {{ $message }}
                                        </p>
                                    @enderror

                                </div>


                                <!-- TELEPON -->
                                <div>

                                    <label
                                        for="store_phone"
                                        class="block
                                               text-[11px]
                                               font-medium
                                               text-gray-300
                                               mb-1.5"
                                    >
                                        Nomor Telepon
                                    </label>

                                    <input
                                        id="store_phone"
                                        type="tel"
                                        name="store_phone"
                                        value="{{ old('store_phone') }}"
                                        placeholder="08xxxxxxxxxx"
                                        autocomplete="tel"
                                        class="input-field
                                               w-full
                                               h-10
                                               border border-white/10
                                               rounded-lg
                                               px-3.5
                                               text-xs
                                               text-white
                                               placeholder-gray-600
                                               outline-none
                                               transition
                                               focus:border-emerald-500/60"
                                    >

                                    @error('store_phone')
                                        <p class="mt-1 text-[10px] text-red-400">
                                            {{ $message }}
                                        </p>
                                    @enderror

                                </div>

                @if(!$googleRegister)
                                <!-- PASSWORD -->
                                <div>

                                    <label
                                        for="password"
                                        class="block
                                               text-[11px]
                                               font-medium
                                               text-gray-300
                                               mb-1.5"
                                    >
                                        Password
                                    </label>

                                    <div class="relative">

                                        <input
                                            id="password"
                                            type="password"
                                            name="password"
                                            placeholder="Minimal 8 karakter"
                                            required
                                            minlength="8"
                                            autocomplete="new-password"
                                            class="input-field
                                                   w-full
                                                   h-10
                                                   border border-white/10
                                                   rounded-lg
                                                   pl-3.5 pr-11
                                                   text-xs
                                                   text-white
                                                   placeholder-gray-600
                                                   outline-none
                                                   transition
                                                   focus:border-emerald-500/60"
                                        >

                                        <button
                                            type="button"
                                            onclick="togglePassword('password', 'passwordIcon', this)"
                                            class="absolute
                                                   inset-y-0
                                                   right-0
                                                   px-3
                                                   text-gray-600
                                                   hover:text-emerald-400
                                                   transition"
                                            aria-label="Tampilkan password"
                                        >

                                            <svg
                                                id="passwordIcon"
                                                class="w-4 h-4"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                                aria-hidden="true"
                                            >

                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0
                                                       3 3 0 016 0z"
                                                />

                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M2.46 12C3.73 7.94 7.52 5 12 5
                                                       c4.48 0 8.27 2.94 9.54 7
                                                       -1.27 4.06-5.06 7-9.54 7
                                                       -4.48 0-8.27-2.94-9.54-7z"
                                                />

                                            </svg>

                                        </button>

                                    </div>

                                    @error('password')
                                        <p class="mt-1 text-[10px] text-red-400">
                                            {{ $message }}
                                        </p>
                                    @enderror

                                </div>


                                <!-- KONFIRMASI PASSWORD -->
                                <div>

                                    <label
                                        for="password_confirmation"
                                        class="block
                                               text-[11px]
                                               font-medium
                                               text-gray-300
                                               mb-1.5"
                                    >
                                        Konfirmasi Password
                                    </label>

                                    <div class="relative">

                                        <input
                                            id="password_confirmation"
                                            type="password"
                                            name="password_confirmation"
                                            placeholder="Ulangi password"
                                            required
                                            minlength="8"
                                            autocomplete="new-password"
                                            class="input-field
                                                   w-full
                                                   h-10
                                                   border border-white/10
                                                   rounded-lg
                                                   pl-3.5 pr-11
                                                   text-xs
                                                   text-white
                                                   placeholder-gray-600
                                                   outline-none
                                                   transition
                                                   focus:border-emerald-500/60"
                                        >

                                        <button
                                            type="button"
                                            onclick="togglePassword('password_confirmation', 'confirmPasswordIcon', this)"
                                            class="absolute
                                                   inset-y-0
                                                   right-0
                                                   px-3
                                                   text-gray-600
                                                   hover:text-emerald-400
                                                   transition"
                                            aria-label="Tampilkan konfirmasi password"
                                        >

                                            <svg
                                                id="confirmPasswordIcon"
                                                class="w-4 h-4"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                                aria-hidden="true"
                                            >

                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0
                                                       3 3 0 016 0z"
                                                />

                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M2.46 12C3.73 7.94 7.52 5 12 5
                                                       c4.48 0 8.27 2.94 9.54 7
                                                       -1.27 4.06-5.06 7-9.54 7
                                                       -4.48 0-8.27-2.94-9.54-7z"
                                                />

                                            </svg>

                                        </button>

                                    </div>

                                    @error('password_confirmation')
                                        <p class="mt-1 text-[10px] text-red-400">
                                            {{ $message }}
                                        </p>
                                    @enderror

                                </div>
                                
                                @else

    <!-- GOOGLE REGISTER INFO -->
    <div
        class="rounded-lg
               border border-emerald-500/15
               bg-emerald-500/5
               px-3 py-2.5"
    >

        <div class="flex items-start gap-2">

            <svg
                class="w-4 h-4 text-emerald-400 flex-shrink-0 mt-0.5"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
                aria-hidden="true"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M5 13l4 4L19 7"
                />
            </svg>

            <p class="text-[10px] text-gray-400 leading-relaxed">
                Anda mendaftar menggunakan Google.
                Tidak perlu membuat password Kasir½M.
            </p>

        </div>

    </div>

@endif


                                <!-- CTA -->
                                <div class="pt-2">

                                    <button
                                        type="submit"
                                        class="cta-button
                                               w-full
                                               h-10
                                               rounded-lg
                                               font-bold
                                               text-xs
                                               flex
                                               items-center
                                               justify-center
                                               gap-2
                                               transition
                                               shadow-lg
                                               shadow-lime-500/10"
                                    >

<span>
    {{ $googleRegister ? 'Buat Akun & Toko' : 'Buat Akun' }}
</span>

                                        <svg
                                            class="w-4 h-4"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                            aria-hidden="true"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M13 7l5 5m0 0l-5 5m5-5H6"
                                            />
                                        </svg>

                                    </button>

                                    <p
                                        class="text-[9px]
                                               text-gray-600
                                               text-center
                                               mt-2.5
                                               leading-relaxed"
                                    >
                                        Dengan mendaftar, Anda mulai menggunakan
                                        Kasir½M untuk mengelola usaha Anda.
                                    </p>

                                </div>

                            </form>


                            <!-- DIVIDER -->
                            <div class="flex items-center gap-3 my-5">

                                <div class="h-px bg-white/10 flex-1"></div>

                                <span class="text-[10px] text-gray-600">
                                    atau
                                </span>

                                <div class="h-px bg-white/10 flex-1"></div>

                            </div>


<!-- GOOGLE -->
<a
    href="{{ route('google.redirect', ['mode' => 'register']) }}"
    class="w-full
           h-10
           rounded-lg
           border border-white/10
           bg-white/[0.03]
           hover:bg-white/[0.06]
           text-white
           text-xs
           font-medium
           flex
           items-center
           justify-center
           gap-2
           transition"
>

    <svg class="w-4 h-4"
         viewBox="0 0 24 24"
         aria-hidden="true">

        <path
            fill="#4285F4"
            d="M21.35 12.23c0-.71-.06-1.39-.18-2.05H12v3.88h5.24a4.48 4.48 0 01-1.94 2.94v2.45h3.14c1.84-1.69 2.91-4.18 2.91-7.22z"/>

        <path
            fill="#34A853"
            d="M12 21.75c2.63 0 4.84-.87 6.45-2.35l-3.14-2.45c-.87.58-1.98.92-3.31.92-2.54 0-4.7-1.72-5.47-4.03H3.29v2.53A9.75 9.75 0 0012 21.75z"/>

        <path
            fill="#FBBC05"
            d="M6.53 13.84A5.86 5.86 0 016.22 12c0-.64.11-1.26.31-1.84V7.63H3.29A9.75 9.75 0 002.25 12c0 1.57.38 3.05 1.04 4.37l3.24-2.53z"/>

        <path
            fill="#EA4335"
            d="M12 6.13c1.43 0 2.71.49 3.72 1.45l2.79-2.79C16.83 3.23 14.63 2.25 12 2.25a9.75 9.75 0 00-8.71 5.38l3.24 2.53c.77-2.31 2.93-4.03 5.47-4.03z"/>

    </svg>

    Daftar dengan Google

</a>


                        </div>


                        <!-- LOGIN -->
                        <div
                            class="border-t border-white/10
                                   bg-white/[0.02]
                                   px-6 py-4
                                   text-center"
                        >

                            <span class="text-[11px] text-gray-500">
                                Sudah punya akun?
                            </span>

                            <a
                                href="{{ route('login') }}"
                                class="text-[11px]
                                       text-lime-400
                                       hover:text-lime-300
                                       font-semibold
                                       ml-1
                                       transition"
                            >
                                Masuk di sini
                            </a>

                        </div>

                    </div>


                    <!-- FOOTER -->
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

            </main>

        </div>

    </div>


    <!-- PASSWORD TOGGLE -->
    <script>

        function togglePassword(inputId, iconId, button) {

            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);

            if (!input || !icon) {
                return;
            }

            if (input.type === 'password') {

                input.type = 'text';

                if (button) {
                    button.setAttribute(
                        'aria-label',
                        'Sembunyikan password'
                    );
                }

                icon.innerHTML = `
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M13.875 18.825A10.05 10.05 0 0112 19
                           c-4.478 0-8.27-2.943-9.543-7
                           a9.97 9.97 0 011.563-3.029
                           M6.228 6.228A9.953 9.953 0 0112 5
                           c4.478 0 8.27 2.943 9.543 7
                           a9.97 9.97 0 01-4.132 5.411
                           M6.228 6.228L3 3
                           m3.228 3.228l3.016 3.016
                           M17.772 17.772L21 21
                           m-3.228-3.228l-3.016-3.016
                           M9.879 9.879a3 3 0 104.242 4.242
                           M9.879 9.879L14.121 14.121"/>
                `;

            } else {

                input.type = 'password';

                if (button) {
                    button.setAttribute(
                        'aria-label',
                        'Tampilkan password'
                    );
                }

                icon.innerHTML = `
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M15 12a3 3 0 11-6 0
                           3 3 0 016 0z"/>

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M2.46 12C3.73 7.94 7.52 5 12 5
                           c4.48 0 8.27 2.94 9.54 7
                           -1.27 4.06-5.06 7-9.54 7
                           -4.48 0-8.27-2.94-9.54-7z"/>
                `;

            }

        }

    </script>

</body>

</html>