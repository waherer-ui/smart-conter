@extends('layouts.app')

@section('header', '📷 Verifikasi Wajah')

@section('content')

<div class="max-w-lg mx-auto px-4 py-4">

    {{-- INFORMASI AKUN --}}
    <div class="rounded-2xl border border-white/10 bg-gray-800 p-4 mb-4">
        <p class="text-xs text-gray-400">Verifikasi untuk</p>

        <h2 class="text-lg font-semibold text-white mt-1">
            {{ $user->name }}
        </h2>

        <p class="text-xs text-gray-400 mt-1">
            Pastikan wajah terlihat jelas di kamera.
        </p>
    </div>


    {{-- KAMERA --}}
    <div class="rounded-2xl border border-white/10 bg-gray-800 p-4">

        <div class="text-center mb-4">
            <h2 class="text-lg font-bold text-white">
                📷 Verifikasi Wajah
            </h2>

            <p class="text-xs text-gray-400 mt-1">
                Posisikan wajah di tengah kamera.
            </p>
        </div>


        {{-- CAMERA BOX --}}
        <div class="relative overflow-hidden rounded-2xl bg-black aspect-[3/4]">

            <video
                id="faceVideo"
                autoplay
                muted
                playsinline
                class="w-full h-full object-cover">
            </video>

            {{-- FRAME WAJAH --}}
            <div
                class="absolute inset-0 flex items-center justify-center pointer-events-none">

                <div
                    class="w-52 h-64 rounded-[50%] border-2 border-emerald-400/80 shadow-[0_0_25px_rgba(16,185,129,0.25)]">
                </div>

            </div>

        </div>


        {{-- STATUS --}}
        <div
            id="faceStatus"
            class="text-center text-sm text-gray-400 mt-4">

            ⏳ Memuat sistem pengenalan wajah...

        </div>


        {{-- BUTTON --}}
        <button
            type="button"
            id="verifyFaceButton"
            disabled
            class="w-full mt-4 rounded-xl bg-emerald-600 hover:bg-emerald-500
                   disabled:bg-gray-700 disabled:text-gray-500
                   text-white font-semibold py-3 transition">

            📷 Verifikasi Wajah

        </button>


        {{-- FORM --}}
        <form
            method="POST"
            action="{{ route('attendance.face.verify') }}"
            id="faceVerifyForm"
            class="hidden">

            @csrf

            <input
                type="hidden"
                name="face_embedding"
                id="faceEmbedding">

            <input
                type="hidden"
                name="latitude"
                value="{{ $latitude }}">

            <input
                type="hidden"
                name="longitude"
                value="{{ $longitude }}">

        </form>

    </div>


    {{-- BATAL --}}
    <a
        href="{{ route('attendance.index') }}"
        class="block text-center text-sm text-gray-400 hover:text-white mt-4">

        ← Kembali ke Absensi

    </a>

</div>


{{-- FACE API --}}
<script src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api/dist/face-api.js"></script>


<script>

document.addEventListener('DOMContentLoaded', async function () {

    const video = document.getElementById('faceVideo');

    const status = document.getElementById('faceStatus');

    const button = document.getElementById('verifyFaceButton');

    const form = document.getElementById('faceVerifyForm');

    const embeddingInput =
        document.getElementById('faceEmbedding');


    /*
     * MODEL FACE API
     */
    const MODEL_URL =
        'https://cdn.jsdelivr.net/npm/@vladmandic/face-api/model';


    /*
     * STATUS
     */
    function setStatus(message, type = 'normal') {

        status.textContent = message;

        status.className =
            'text-center text-sm mt-4';

        if (type === 'success') {

            status.classList.add(
                'text-emerald-400'
            );

        } else if (type === 'error') {

            status.classList.add(
                'text-red-400'
            );

        } else {

            status.classList.add(
                'text-gray-400'
            );

        }

    }


    /*
     * LOAD MODEL
     */
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


        /*
         * CAMERA
         */
        setStatus(
            '⏳ Meminta izin kamera...'
        );


        if (
            !navigator.mediaDevices ||
            !navigator.mediaDevices.getUserMedia
        ) {

            throw new Error(
                'Browser tidak mendukung kamera.'
            );

        }


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


    /*
     * VERIFIKASI WAJAH
     */
    button.addEventListener(
        'click',
        async function () {

            button.disabled = true;

            button.textContent =
                '⏳ Membaca wajah...';


            setStatus(
                '⏳ Jangan bergerak. Sedang membaca wajah...'
            );


            try {

                /*
                 * DETEKSI WAJAH
                 */
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


                /*
                 * TIDAK ADA WAJAH
                 */
                if (!detection) {

                    throw new Error(
                        'Wajah tidak terdeteksi.'
                    );

                }


                /*
                 * AMBIL DESCRIPTOR
                 */
                const descriptor =
                    Array.from(
                        detection.descriptor
                    );


                /*
                 * VALIDASI DESCRIPTOR
                 */
                if (
                    descriptor.length !== 128
                ) {

                    throw new Error(
                        'Data wajah tidak valid.'
                    );

                }


                /*
                 * SIMPAN KE FORM
                 */
                embeddingInput.value =
    JSON.stringify(descriptor);

setStatus(
    '✅ Wajah terbaca. Memverifikasi...'
);

/*
 * Matikan kamera sebelum form dikirim
 */
if (video.srcObject) {

    video.srcObject
        .getTracks()
        .forEach(function (track) {
            track.stop();
        });

    video.srcObject = null;
}

setTimeout(function () {
    form.submit();
}, 100);


            } catch (error) {

                console.error(error);


                setStatus(
                    '❌ ' +
                    (
                        error.message ||
                        'Wajah tidak dapat dibaca.'
                    ) +
                    ' Silakan coba lagi.',
                    'error'
                );


                button.disabled = false;

                button.textContent =
                    '📷 Verifikasi Wajah';

            }

        }
    );


    /*
     * MATIKAN KAMERA SAAT MENINGGALKAN HALAMAN
     */
    window.addEventListener(
        'beforeunload',
        function () {

            if (video.srcObject) {

                video.srcObject
                    .getTracks()
                    .forEach(function (track) {

                        track.stop();

                    });

            }

        }
    );

});

</script>

@endsection