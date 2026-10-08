@extends('layouts.app')

@section('content')

<div class="max-w-lg mx-auto px-4 py-4">

    {{-- INFORMASI --}}
    <div class="rounded-2xl border border-white/10 bg-gray-800 p-4 mb-4">

        <p class="text-xs text-gray-400">
            Akun
        </p>

        <h2 class="text-lg font-semibold text-white">
            {{ $user->name }}
        </h2>

        @if($user->face_registered_at)

            <p class="text-xs text-emerald-400 mt-1">
                ✓ Wajah sudah terdaftar
            </p>

        @else

            <p class="text-xs text-gray-400 mt-1">
                Wajah belum terdaftar
            </p>

        @endif

    </div>


    {{-- KAMERA --}}
    <div class="rounded-2xl border border-white/10 bg-gray-800 p-4">

        <div class="text-center mb-4">

            <h2 class="text-lg font-bold text-white">
                📷 Daftarkan Wajah
            </h2>

            <p class="text-xs text-gray-400 mt-1">
                Posisikan wajah di tengah kamera.
            </p>

        </div>


        <div
            class="relative overflow-hidden rounded-2xl bg-black
                   aspect-[3/4]"
        >

            <video
                id="faceVideo"
                autoplay
                muted
                playsinline
                class="w-full h-full object-cover"
            ></video>

            <div
                id="faceGuide"
                class="absolute inset-0 flex items-center
                       justify-center pointer-events-none"
            >
                <div
                    class="w-52 h-64 rounded-[50%]
                           border-2 border-emerald-400/80"
                ></div>
            </div>

        </div>


        {{-- STATUS --}}

        <div
            id="faceStatus"
            class="text-center text-sm text-gray-400 mt-4"
        >
            Memuat sistem pengenalan wajah...
        </div>


        {{-- BUTTON --}}

        <button
            type="button"
            id="registerFaceButton"
            disabled
            class="w-full mt-4 rounded-xl
                   bg-emerald-600 hover:bg-emerald-500
                   disabled:bg-gray-700
                   disabled:text-gray-500
                   text-white font-semibold
                   py-3 transition"
        >
            📷 Daftarkan Wajah
        </button>


        {{-- FORM --}}

        <form
            method="POST"
            action="{{ route('profil.face.store') }}"
            id="faceForm"
            class="hidden"
        >
            @csrf

            <input
                type="hidden"
                name="face_embedding"
                id="faceEmbedding"
            >
        </form>


        @if($user->face_registered_at)

            <form
                method="POST"
                action="{{ route('profil.face.destroy') }}"
                class="mt-3"
                onsubmit="return confirm('Hapus data wajah yang sudah terdaftar?')"
            >

                @csrf
                @method('DELETE')

                <button
                    type="submit"
                    class="w-full rounded-xl
                           bg-red-600/20
                           border border-red-500/30
                           text-red-400
                           py-3 text-sm font-medium"
                >
                    🗑️ Hapus Pendaftaran Wajah
                </button>

            </form>

        @endif

    </div>

</div>


<script src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api/dist/face-api.js"></script>

<script>

document.addEventListener('DOMContentLoaded', async function () {

    const video =
        document.getElementById('faceVideo');

    const status =
        document.getElementById('faceStatus');

    const button =
        document.getElementById('registerFaceButton');

    const form =
        document.getElementById('faceForm');

    const embeddingInput =
        document.getElementById('faceEmbedding');


    const MODEL_URL =
        'https://cdn.jsdelivr.net/npm/@vladmandic/face-api/model';


    function setStatus(message, type = 'normal') {

        status.textContent = message;

        status.className =
            'text-center text-sm mt-4';

        if (type === 'success') {
            status.classList.add('text-emerald-400');
        } else if (type === 'error') {
            status.classList.add('text-red-400');
        } else {
            status.classList.add('text-gray-400');
        }
    }


    try {

        setStatus(
            '⏳ Memuat sistem pengenalan wajah...'
        );

        await faceapi.nets.tinyFaceDetector.loadFromUri(
            MODEL_URL
        );

        await faceapi.nets.faceLandmark68Net.loadFromUri(
            MODEL_URL
        );

        await faceapi.nets.faceRecognitionNet.loadFromUri(
            MODEL_URL
        );


        setStatus(
            '⏳ Meminta izin kamera...'
        );


        const stream =
            await navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: 'user',
                    width: {
                        ideal: 720
                    },
                    height: {
                        ideal: 960
                    }
                },
                audio: false
            });


        video.srcObject = stream;


        video.onloadedmetadata = function () {

            video.play();

            button.disabled = false;

            setStatus(
                '✅ Kamera siap. Pastikan wajah terlihat jelas.'
            );

        };


    } catch (error) {

        console.error(error);

        setStatus(
            '❌ Kamera atau sistem wajah gagal dimuat.',
            'error'
        );

    }


    button.addEventListener('click', async function () {

        button.disabled = true;

        button.textContent =
            '⏳ Membaca wajah...';

        setStatus(
            '⏳ Jangan bergerak. Sedang membaca wajah...'
        );


        try {

            const detection =
                await faceapi
                    .detectSingleFace(
                        video,
                        new faceapi.TinyFaceDetectorOptions({
                            inputSize: 320,
                            scoreThreshold: 0.5
                        })
                    )
                    .withFaceLandmarks()
                    .withFaceDescriptor();


            if (!detection) {

                throw new Error(
                    'Wajah tidak terdeteksi.'
                );

            }


            const descriptor =
                Array.from(
                    detection.descriptor
                );


            if (descriptor.length !== 128) {

                throw new Error(
                    'Data wajah tidak valid.'
                );

            }


            embeddingInput.value =
                JSON.stringify(descriptor);


            setStatus(
                '✅ Wajah berhasil terbaca. Menyimpan...'
            );


            form.submit();


        } catch (error) {

            console.error(error);

            setStatus(
                '❌ ' +
                (error.message ||
                    'Wajah tidak dapat dibaca.') +
                ' Silakan coba lagi.',
                'error'
            );

            button.disabled = false;

            button.textContent =
                '📷 Daftarkan Wajah';

        }

    });

});

</script>

@endsection