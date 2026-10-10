<?php

namespace App\Http\Controllers;

use App\Models\TutorialVideo;
use Illuminate\Http\Request;

class TutorialVideoPageController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->query('kategori');
        $search = $request->query('search');

        // Ambil kategori dari video yang aktif
        $categories = TutorialVideo::query()
            ->where('is_active', true)
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        // Ambil daftar video aktif
        $query = TutorialVideo::query()
            ->where('is_active', true);

        // Filter kategori
        if ($category && $category !== 'all') {
            $query->where('category', $category);
        }

        // Pencarian judul dan deskripsi
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        $videos = $query
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get();

        return view('support.video-tutorial', compact(
            'videos',
            'categories',
            'category',
            'search'
        ));
    }
}