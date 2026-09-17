<?php

namespace App\Http\Controllers\AdminKasirku;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    /**
     * Halaman pengaturan Admin Platform.
     */
    public function index()
    {
        $userId = session('user_id');

        $user = User::findOrFail($userId);

        return view('admin-kasirku.settings', compact('user'));
    }


    /**
     * Update nama dan email Admin Platform.
     */
    public function updateProfile(Request $request)
    {
        $userId = session('user_id');

        $user = User::findOrFail($userId);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
        ]);

        $user->name = trim($validated['name']);
        $user->email = strtolower(trim($validated['email']));
        $user->save();

        // Perbarui session jika nama/email digunakan di layout.
        session([
            'username' => $user->name,
        ]);

        return redirect()
            ->route('admin-kasirku.settings')
            ->with('success', 'Profil Admin KasirKU berhasil diperbarui.');
    }


    /**
     * Update password Admin Platform.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => [
                'required',
                'string',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
        ]);

        $userId = session('user_id');

        $user = User::findOrFail($userId);

        // Pastikan password lama benar.
        if (!Hash::check($request->current_password, $user->password)) {
            return back()
                ->withErrors([
                    'current_password' => 'Password saat ini tidak benar.',
                ])
                ->withInput();
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()
            ->route('admin-kasirku.settings')
            ->with('success', 'Password Admin KasirKU berhasil diperbarui.');
    }
}