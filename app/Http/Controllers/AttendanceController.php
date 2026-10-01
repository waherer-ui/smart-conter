<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Store;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    /**
     * Menampilkan halaman absensi staf.
     */
    public function index()
    {
        $userId = (int) session('user_id');
        $storeId = (int) session('active_store_id');

        $store = Store::findOrFail($storeId);

        $isMember = $store->users()
            ->where('users.id', $userId)
            ->exists();

        if (!$isMember) {
            abort(403, 'Anda tidak terdaftar sebagai staf toko ini.');
        }

        $attendance = Attendance::where('store_id', $storeId)
            ->where('user_id', $userId)
            ->whereDate('date', today())
            ->first();

        return view('attendance.index', compact(
            'store',
            'attendance'
        ));
    }

    /**
     * Riwayat absensi.
     *
     * Admin:
     * - Bisa melihat semua staf.
     *
     * Kasir:
     * - Hanya bisa melihat absensinya sendiri.
     */
    public function history(Request $request)
    {
        $userId = (int) session('user_id');
        $storeId = (int) session('active_store_id');
        $role = session('user_role');

        $store = Store::findOrFail($storeId);

        $isMember = $store->users()
            ->where('users.id', $userId)
            ->exists();

        if (!$isMember) {
            abort(403, 'Anda tidak terdaftar sebagai staf toko ini.');
        }

        $dateFrom = $request->input(
            'date_from',
            now()->startOfMonth()->format('Y-m-d')
        );

        $dateTo = $request->input(
            'date_to',
            now()->format('Y-m-d')
        );

        $staffId = $request->input('staff_id');

        $query = Attendance::with('user')
            ->where('store_id', $storeId)
            ->whereDate('date', '>=', $dateFrom)
            ->whereDate('date', '<=', $dateTo);

        /*
         * Admin bisa melihat semua staf.
         *
         * Kasir hanya boleh melihat data absensinya sendiri.
         */
        if ($role !== 'admin') {
            $staffId = $userId;

            $query->where('user_id', $userId);
        } elseif ($staffId) {
            $query->where('user_id', $staffId);
        }

        $query
            ->orderByDesc('date')
            ->orderByDesc('check_in');

        $attendances = $query
            ->paginate(20)
            ->withQueryString();

        /*
         * Dropdown staf hanya diperlukan untuk admin.
         *
         * Untuk kasir, kita kirim user sendiri agar Blade
         * tetap aman apabila menggunakan variabel $staff.
         */
        if ($role === 'admin') {
            $staff = $store->users()
                ->orderBy('name')
                ->get();
        } else {
            $staff = $store->users()
                ->where('users.id', $userId)
                ->get();
        }

        return view(
            'attendance.history',
            compact(
                'store',
                'attendances',
                'staff',
                'dateFrom',
                'dateTo',
                'staffId'
            )
        );
    }

    /**
     * Rekap absensi staf.
     *
     * Admin:
     * - Bisa melihat rekap semua staf.
     *
     * Kasir:
     * - Hanya bisa melihat rekap dirinya sendiri.
     */
    public function summary(Request $request)
    {
        $userId = (int) session('user_id');
        $storeId = (int) session('active_store_id');
        $role = session('user_role');

        $store = Store::findOrFail($storeId);

        $isMember = $store->users()
            ->where('users.id', $userId)
            ->exists();

        if (!$isMember) {
            abort(403, 'Anda tidak terdaftar sebagai staf toko ini.');
        }

        $month = $request->input(
            'month',
            now()->format('Y-m')
        );

        $staffId = $request->input('staff_id');

        /*
         * Validasi format bulan agar Carbon tidak error
         * apabila parameter URL diubah secara manual.
         */
        try {
            $startDate = \Carbon\Carbon::createFromFormat(
                'Y-m',
                $month
            )->startOfMonth();
        } catch (\Throwable $e) {
            $month = now()->format('Y-m');

            $startDate = now()->startOfMonth();
        }

        $endDate = $startDate->copy()->endOfMonth();

        $query = Attendance::with('user')
            ->where('store_id', $storeId)
            ->whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate);

        /*
         * Admin bisa memilih staf.
         *
         * Kasir selalu dipaksa menggunakan user_id miliknya
         * sendiri, meskipun mencoba mengubah staff_id dari URL.
         */
        if ($role !== 'admin') {
            $staffId = $userId;

            $query->where('user_id', $userId);
        } elseif ($staffId) {
            $query->where('user_id', $staffId);
        }

        $attendances = $query
            ->orderBy('date')
            ->get();

        /*
         * Dropdown staf.
         */
        if ($role === 'admin') {
            $staff = $store->users()
                ->orderBy('name')
                ->get();
        } else {
            $staff = $store->users()
                ->where('users.id', $userId)
                ->get();
        }

        $summary = $attendances
            ->groupBy('user_id')
            ->map(function ($items) {

                $totalDays = $items->count();

                $completeDays = $items
                    ->filter(function ($attendance) {
                        return $attendance->check_in &&
                            $attendance->check_out;
                    })
                    ->count();

                $incompleteDays = $items
                    ->filter(function ($attendance) {
                        return $attendance->check_in &&
                            !$attendance->check_out;
                    })
                    ->count();

                $totalMinutes = $items
                    ->filter(function ($attendance) {
                        return $attendance->check_in &&
                            $attendance->check_out;
                    })
                    ->sum(function ($attendance) {
                        return $attendance->check_in
                            ->diffInMinutes(
                                $attendance->check_out
                            );
                    });

                $lateDays = $items
                    ->filter(function ($attendance) {
                        return $attendance->status === 'terlambat';
                    })
                    ->count();

                return [
                    'user' => $items->first()->user,
                    'total_days' => $totalDays,
                    'complete_days' => $completeDays,
                    'incomplete_days' => $incompleteDays,
                    'total_minutes' => $totalMinutes,
                    'late_days' => $lateDays,
                ];
            })
            ->values();

        return view(
            'attendance.summary',
            compact(
                'store',
                'summary',
                'staff',
                'month',
                'staffId'
            )
        );
    }

    /**
     * Check In.
     */
    public function checkIn(Request $request)
    {
        $userId = (int) session('user_id');
        $storeId = (int) session('active_store_id');

        $store = Store::findOrFail($storeId);

        // Pastikan user terdaftar di toko aktif.
        $isMember = $store->users()
            ->where('users.id', $userId)
            ->exists();

        if (!$isMember) {
            return redirect()
                ->back()
                ->with(
                    'error',
                    'Anda tidak terdaftar sebagai staf toko ini.'
                );
        }

        // Cegah check-in dua kali.
        $existing = Attendance::where('store_id', $storeId)
            ->where('user_id', $userId)
            ->whereDate('date', today())
            ->first();

        if ($existing) {
            return redirect()
                ->back()
                ->with(
                    'error',
                    'Anda sudah melakukan absensi hari ini.'
                );
        }

        // Validasi koordinat GPS staf.
        $validated = $request->validate([
            'latitude' => [
                'required',
                'numeric',
                'between:-90,90',
            ],
            'longitude' => [
                'required',
                'numeric',
                'between:-180,180',
            ],
        ]);

        // Pastikan lokasi toko sudah diatur.
        if (
            $store->latitude === null ||
            $store->longitude === null
        ) {
            return redirect()
                ->back()
                ->with(
                    'error',
                    'Lokasi toko belum diatur. Silakan atur lokasi toko terlebih dahulu.'
                );
        }

        // Radius absensi toko.
        $radius = (int) ($store->attendance_radius ?: 100);

        // Hitung jarak staf ke toko.
        $distance = $this->calculateDistance(
            (float) $validated['latitude'],
            (float) $validated['longitude'],
            (float) $store->latitude,
            (float) $store->longitude
        );

        // Di luar radius.
        if ($distance > $radius) {
            return redirect()
                ->back()
                ->with(
                    'error',
                    'Check-in ditolak. Anda berada sekitar '
                    . round($distance)
                    . ' meter dari lokasi toko. '
                    . 'Maksimal radius absensi adalah '
                    . $radius
                    . ' meter.'
                );
        }

        /*
         * Tentukan status keterlambatan.
         */
        $workStartTime = $store->work_start_time ?: '08:00:00';

        $lateTolerance = (int) (
            $store->late_tolerance ?? 15
        );

        $checkInTime = now();

        $startTime = \Carbon\Carbon::parse(
            $checkInTime->format('Y-m-d')
            . ' '
            . $workStartTime
        );

        $lateLimit = $startTime->copy()
            ->addMinutes($lateTolerance);

        $status = $checkInTime->gt($lateLimit)
            ? 'terlambat'
            : 'hadir';

        // Simpan absensi.
        Attendance::create([
            'store_id' => $storeId,
            'user_id' => $userId,
            'date' => today(),
            'check_in' => $checkInTime,
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'status' => $status,
        ]);

        if ($status === 'terlambat') {

            $lateMinutes = $startTime->diffInMinutes(
                $checkInTime
            );

            return redirect()
                ->back()
                ->with(
                    'success',
                    'Check-in berhasil. Anda terlambat '
                    . $lateMinutes
                    . ' menit. Jarak Anda sekitar '
                    . round($distance)
                    . ' meter dari toko.'
                );
        }

        return redirect()
            ->back()
            ->with(
                'success',
                'Check-in berhasil. Jarak Anda sekitar '
                . round($distance)
                . ' meter dari toko.'
        );
    }

    /**
     * Check Out.
     */
    public function checkOut(Request $request)
    {
        $userId = (int) session('user_id');
        $storeId = (int) session('active_store_id');

        $store = Store::findOrFail($storeId);

        $attendance = Attendance::where('store_id', $storeId)
            ->where('user_id', $userId)
            ->whereDate('date', today())
            ->first();

        if (!$attendance) {
            return redirect()
                ->back()
                ->with(
                    'error',
                    'Anda belum melakukan check-in hari ini.'
                );
        }

        if ($attendance->check_out) {
            return redirect()
                ->back()
                ->with(
                    'error',
                    'Anda sudah melakukan check-out hari ini.'
                );
        }

        // Pastikan lokasi toko sudah diatur.
        if (
            $store->latitude === null ||
            $store->longitude === null
        ) {
            return redirect()
                ->back()
                ->with(
                    'error',
                    'Lokasi toko belum diatur. Silakan hubungi admin toko.'
                );
        }

        // Validasi GPS staf.
        $validated = $request->validate([
            'latitude' => [
                'required',
                'numeric',
                'between:-90,90',
            ],
            'longitude' => [
                'required',
                'numeric',
                'between:-180,180',
            ],
        ]);

        $radius = (int) ($store->attendance_radius ?: 100);

        // Hitung jarak staf ke toko.
        $distance = $this->calculateDistance(
            (float) $validated['latitude'],
            (float) $validated['longitude'],
            (float) $store->latitude,
            (float) $store->longitude
        );

        // Di luar radius.
        if ($distance > $radius) {
            return redirect()
                ->back()
                ->with(
                    'error',
                    'Check-out ditolak. Anda berada sekitar '
                    . round($distance)
                    . ' meter dari lokasi toko. '
                    . 'Maksimal radius absensi adalah '
                    . $radius
                    . ' meter.'
                );
        }

        // Dalam radius → check-out.
        $attendance->update([
            'check_out' => now(),
        ]);

        return redirect()
            ->back()
            ->with(
                'success',
                'Check-out berhasil. Jarak Anda sekitar '
                . round($distance)
                . ' meter dari toko.'
            );
    }

    /**
     * Menghitung jarak dua titik koordinat menggunakan
     * rumus Haversine.
     *
     * Hasil dalam meter.
     */
    private function calculateDistance(
        float $latitude1,
        float $longitude1,
        float $latitude2,
        float $longitude2
    ): float {
        $earthRadius = 6371000;

        $latitudeDifference = deg2rad(
            $latitude2 - $latitude1
        );

        $longitudeDifference = deg2rad(
            $longitude2 - $longitude1
        );

        $a =
            sin($latitudeDifference / 2) ** 2
            +
            cos(deg2rad($latitude1))
            *
            cos(deg2rad($latitude2))
            *
            sin($longitudeDifference / 2) ** 2;

        $c = 2 * atan2(
            sqrt($a),
            sqrt(1 - $a)
        );

        return $earthRadius * $c;
    }

    /**
     * Halaman verifikasi wajah sebelum check-in.
     */
    public function faceCheck(Request $request)
    {
        $userId = (int) session('user_id');

        $user = \App\Models\User::findOrFail($userId);

        $latitude = $request->query('latitude');
        $longitude = $request->query('longitude');

        if (
            $latitude === null ||
            $longitude === null
        ) {
            return redirect()
                ->route('attendance.index')
                ->with(
                    'error',
                    'Lokasi GPS tidak tersedia. Silakan coba check-in lagi.'
                );
        }

        return view(
            'attendance.face-check',
            compact(
                'user',
                'latitude',
                'longitude'
            )
        );
    }

    /**
     * Verifikasi wajah sebelum check-in.
     */
    public function verifyFace(Request $request)
    {
        $userId = (int) session('user_id');

        $user = \App\Models\User::findOrFail($userId);

        if (!$user->face_embedding) {
            return redirect()
                ->route('attendance.index')
                ->with(
                    'error',
                    'Wajah Anda belum terdaftar. Silakan daftarkan wajah terlebih dahulu.'
                );
        }

        $validated = $request->validate([
            'face_embedding' => [
                'required',
                'string',
            ],
            'latitude' => [
                'required',
                'numeric',
                'between:-90,90',
            ],
            'longitude' => [
                'required',
                'numeric',
                'between:-180,180',
            ],
        ]);

        $registered = json_decode(
            $user->face_embedding,
            true
        );

        $current = json_decode(
            $validated['face_embedding'],
            true
        );

        if (
            !is_array($registered) ||
            !is_array($current) ||
            count($registered) !== 128 ||
            count($current) !== 128
        ) {
            return back()
                ->with(
                    'error',
                    'Data wajah tidak valid. Silakan coba lagi.'
                );
        }

        $distance = 0;

        for ($i = 0; $i < 128; $i++) {

            $difference =
                (float) $registered[$i]
                -
                (float) $current[$i];

            $distance +=
                $difference * $difference;
        }

        $distance = sqrt($distance);

        /*
         * Threshold awal.
         * Semakin kecil = semakin mirip.
         */
        $threshold = 0.60;

        if ($distance > $threshold) {
            return back()
                ->with(
                    'error',
                    'Wajah tidak cocok. Pastikan Anda menggunakan wajah yang sudah terdaftar.'
                );
        }

        // Wajah cocok → lanjut ke check-in GPS.
        $request->merge([
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
        ]);

        return $this->checkIn($request);
    }
}