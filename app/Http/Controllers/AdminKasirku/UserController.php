<?php

namespace App\Http\Controllers\AdminKasirku;

use App\Http\Controllers\Controller;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Menampilkan seluruh pengguna KasirKU.
     */
    public function index()
    {
        $users = User::with('stores')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin-kasirku.users.index', compact('users'));
    }
}