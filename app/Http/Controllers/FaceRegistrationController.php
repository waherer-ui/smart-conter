<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class FaceRegistrationController extends Controller
{
    /**
     * Halaman pendaftaran wajah.
     */
    public function create()
    {
        $userId = (int) session('user_id');

        $user = User::findOrFail($userId);

        return view('profile.face', compact('user'));
    }

    /**
     * Simpan face embedding.
     */
    public function store(Request $request)
    {
        $userId = (int) session('user_id');

        $user = User::findOrFail($userId);

        $validated = $request->validate([
            'face_embedding' => [
                'required',
                'string',
            ],
        ]);

        $embedding = json_decode(
            $validated['face_embedding'],
            true
        );

        if (
            !is_array($embedding) ||
            count($embedding) !== 128
        ) {
            return back()
                ->with(
                    'error',
                    'Data wajah tidak valid. Silakan ulangi pendaftaran.'
                );
        }

        // Pastikan semua nilai berupa angka
        foreach ($embedding as $value) {
            if (!is_numeric($value)) {
                return back()
                    ->with(
                        'error',
                        'Data wajah tidak valid. Silakan ulangi pendaftaran.'
                    );
            }
        }

        $user->update([
            'face_embedding' => json_encode(
                array_map('floatval', $embedding)
            ),
            'face_registered_at' => now(),
        ]);

        return redirect()
            ->route('profil.face')
            ->with(
                'success',
                'Wajah berhasil didaftarkan. Sekarang wajah Anda dapat digunakan untuk absensi.'
            );
    }

    /**
     * Hapus pendaftaran wajah.
     */
    public function destroy()
    {
        $userId = (int) session('user_id');

        $user = User::findOrFail($userId);

        $user->update([
            'face_embedding' => null,
            'face_registered_at' => null,
        ]);

        return redirect()
            ->route('profil.face')
            ->with(
                'success',
                'Data wajah berhasil dihapus.'
            );
    }
}