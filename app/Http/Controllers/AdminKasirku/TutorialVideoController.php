<?php

namespace App\Http\Controllers\AdminKasirku;

use App\Http\Controllers\Controller;
use App\Models\TutorialVideo;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TutorialVideoController extends Controller
{
    /**
     * Daftar video tutorial.
     */
    public function index()
    {
        $videos = TutorialVideo::query()
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->paginate(10);

        return view(
            'admin-kasirku.tutorial-videos.index',
            compact('videos')
        );
    }

    /**
     * Form tambah video.
     */
    public function create()
    {
        return view('admin-kasirku.tutorial-videos.create');
    }

    /**
     * Simpan video baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:5000'],
            'video_url' => ['required', 'url', 'max:2048'],
            'video_provider' => [
                'required',
                Rule::in(['youtube', 'google_drive']),
            ],
            'thumbnail_url' => ['nullable', 'url', 'max:2048'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $this->validateVideoUrl(
            $validated['video_url'],
            $validated['video_provider']
        );

        $title = $validated['title'];

        $slug = Str::slug($title);

        if ($slug === '') {
            $slug = 'tutorial';
        }

        $baseSlug = $slug;
        $counter = 1;

        while (TutorialVideo::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }

        TutorialVideo::create([
            ...$validated,
            'slug' => $slug,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => false,
            'created_by' => session('user_id'),
        ]);

        return redirect()
            ->route('admin-kasirku.tutorial-videos.index')
            ->with(
                'success',
                'Video tutorial berhasil ditambahkan sebagai nonaktif.'
            );
    }

    /**
     * Form edit video.
     */
    public function edit(TutorialVideo $tutorialVideo)
    {
        return view(
            'admin-kasirku.tutorial-videos.edit',
            compact('tutorialVideo')
        );
    }

    /**
     * Perbarui video.
     */
    public function update(
        Request $request,
        TutorialVideo $tutorialVideo
    ) {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:5000'],
            'video_url' => ['required', 'url', 'max:2048'],
            'video_provider' => [
                'required',
                Rule::in(['youtube', 'google_drive']),
            ],
            'thumbnail_url' => ['nullable', 'url', 'max:2048'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $this->validateVideoUrl(
            $validated['video_url'],
            $validated['video_provider']
        );

        $slug = Str::slug($validated['title']);

        if ($slug === '') {
            $slug = 'tutorial';
        }

        $baseSlug = $slug;
        $counter = 1;

        while (
            TutorialVideo::where('slug', $slug)
                ->where('id', '!=', $tutorialVideo->id)
                ->exists()
        ) {
            $slug = $baseSlug . '-' . $counter++;
        }

        $tutorialVideo->update([
            ...$validated,
            'slug' => $slug,
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        return redirect()
            ->route('admin-kasirku.tutorial-videos.index')
            ->with('success', 'Video tutorial berhasil diperbarui.');
    }

    /**
     * Aktifkan atau nonaktifkan video.
     */
    public function toggle(TutorialVideo $tutorialVideo)
    {
        $tutorialVideo->update([
            'is_active' => !$tutorialVideo->is_active,
        ]);

        return redirect()
            ->route('admin-kasirku.tutorial-videos.index')
            ->with(
                'success',
                $tutorialVideo->is_active
                    ? 'Video tutorial berhasil diaktifkan.'
                    : 'Video tutorial berhasil dinonaktifkan.'
            );
    }

    /**
     * Hapus video.
     */
    public function destroy(TutorialVideo $tutorialVideo)
    {
        $tutorialVideo->delete();

        return redirect()
            ->route('admin-kasirku.tutorial-videos.index')
            ->with('success', 'Video tutorial berhasil dihapus.');
    }

    /**
     * Pastikan URL berasal dari penyedia yang dipilih.
     */
    private function validateVideoUrl(
        string $url,
        string $provider
    ): void {
        $host = strtolower(
            parse_url($url, PHP_URL_HOST) ?? ''
        );

        $allowedHosts = [
            'youtube' => [
                'youtube.com',
                'www.youtube.com',
                'm.youtube.com',
                'youtu.be',
                'www.youtube-nocookie.com',
            ],
            'google_drive' => [
                'drive.google.com',
                'docs.google.com',
            ],
        ];

        abort_unless(
            in_array($host, $allowedHosts[$provider] ?? [], true),
            422,
            'Link video tidak sesuai dengan sumber yang dipilih.'
        );
    }
}