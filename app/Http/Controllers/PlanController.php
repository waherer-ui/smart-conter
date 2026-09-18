<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Plan;
use App\Models\Store;
use App\Models\User;
use App\Models\Referral;
use App\Models\ReferralReward;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class PlanController extends Controller
{
    public function index()
{
    $user = User::findOrFail(
        session('user_id')
    );

    $plans = Plan::where('is_active', true)
        ->with([
            'features' => function ($query) {
                $query->where('is_active', true);
            },
            'limits',
        ])
        ->orderBy('price')
        ->get();

    $activeStore = Store::find(
        session('active_store_id')
    );

    $subscription = $user->subscription?->load('plan');

    $storeCount = $user->stores()->count();

    return view(
        'paket.index',
        compact(
            'plans',
            'activeStore',
            'subscription',
            'storeCount'
        )
    );
}

    public function select(Request $request, Plan $plan)
    {
        $user = User::findOrFail(
            session('user_id')
        );

        if (!$user->subscription) {
            return redirect()
                ->route('paket')
                ->with(
                    'error',
                    'Subscription Owner tidak ditemukan.'
                );
        }

        return redirect()
            ->route('paket.payment', $plan);
    }

    public function payment(Plan $plan)
    {
        $user = User::findOrFail(
            session('user_id')
        );

        if (!$user->subscription) {
            return redirect()
                ->route('paket')
                ->with(
                    'error',
                    'Subscription Owner tidak ditemukan.'
                );
        }

        return view(
            'paket.payment',
            compact('plan')
        );
    }

/**
 * Membuat transaksi pembayaran baru.
 */
public function createPayment(Request $request, Plan $plan)
{
    $user = User::findOrFail(
        session('user_id')
    );

    if (!$user->subscription) {
        return redirect()
            ->route('paket')
            ->with(
                'error',
                'Subscription Owner tidak ditemukan.'
            );
    }

    $validated = $request->validate([
        'payment_method' => [
            'required',
            'in:qris,transfer',
        ],

        'duration_months' => [
            'required',
            'integer',
            'in:1,3,6,12',
        ],

        'referral_code' => [
    'nullable',
    'string',
    'max:50',
],
    ]);

    $paymentMethod = $validated['payment_method'];
    $durationMonths = (int) $validated['duration_months'];
    $referralCode = $validated['referral_code'] ?? null;

    $referral = null;

if ($referralCode) {

    $referral = Referral::where('code', strtoupper(trim($referralCode)))
        ->where('is_active', true)
        ->first();

    if (!$referral) {
        return back()
            ->withInput()
            ->with(
                'error',
                'Kode referral tidak ditemukan atau sudah tidak aktif.'
            );
    }

    if ((int) $referral->owner_id === (int) $user->id) {
        return back()
            ->withInput()
            ->with(
                'error',
                'Anda tidak dapat menggunakan kode referral milik sendiri.'
            );
    }
}


    /*
     * Diskon berdasarkan masa aktif.
     */
    $durationDiscounts = [
        1 => 0,
        3 => 5,
        6 => 7,
        12 => 12,
    ];

    $discountPercent = $durationDiscounts[$durationMonths];

    /*
     * Harga normal berdasarkan jumlah bulan.
     */
    $baseAmount = $plan->price * $durationMonths;

    /*
     * Hitung diskon masa aktif.
     */
    $discountAmount = $baseAmount * ($discountPercent / 100);

/*
 * Harga setelah diskon masa aktif.
 */
$amountAfterDurationDiscount = $baseAmount - $discountAmount;

/*
 * Diskon referral.
 *
 * Referral hanya berlaku untuk:
 * - Plus / Premium
 * - 6 bulan / 12 bulan
 */
$referralDiscountPercent = 0;
$referralDiscountAmount = 0;

if (
    $referral &&
    in_array($durationMonths, [6, 12], true) &&
    in_array($plan->slug, ['pro', 'premium'], true)
) {
    $referralDiscountPercent = 8;

    $referralDiscountAmount = round(
    $amountAfterDurationDiscount
    * ($referralDiscountPercent / 100)
);
}

/*
 * Harga akhir setelah semua diskon.
 */
$finalAmount = round(
    $amountAfterDurationDiscount
    - $referralDiscountAmount
);

    $payment = Payment::create([
    'owner_id' => $user->id,
    'plan_id' => $plan->id,
    'duration_months' => $durationMonths,
    'amount' => $finalAmount,
    'referral_code' => $referral?->code,
    'referral_discount_percent' => $referralDiscountPercent,
    'referral_discount_amount' => $referralDiscountAmount,
    'payment_method' => $paymentMethod,
    'status' => 'pending',
    'reference' => 'PAY-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(6)),
]);

    return redirect()
        ->route('paket.payment.pending', $payment->id);
}

/**
 * Validasi kode referral untuk UI.
 */
public function validateReferral(Request $request)
{
    $user = User::findOrFail(
        session('user_id')
    );

    $code = strtoupper(
        trim($request->input('code', ''))
    );

    if ($code === '') {
        return response()->json([
            'valid' => false,
            'message' => 'Masukkan kode referral.'
        ]);
    }

    $referral = Referral::where('code', $code)
        ->where('is_active', true)
        ->first();

    if (!$referral) {
        return response()->json([
            'valid' => false,
            'message' => 'Kode referral tidak ditemukan atau sudah tidak aktif.'
        ]);
    }

    if ((int) $referral->owner_id === (int) $user->id) {
        return response()->json([
            'valid' => false,
            'message' => 'Anda tidak dapat menggunakan kode referral milik sendiri.'
        ]);
    }

    return response()->json([
        'valid' => true,
        'message' => 'Kode referral valid. Diskon 8% diterapkan.'
    ]);
}

    /**
     * Halaman menunggu pembayaran.
     */
    public function paymentPending(Payment $payment)
    {
        $user = User::findOrFail(
            session('user_id')
        );

        if ($payment->owner_id !== $user->id) {
            abort(403);
        }

        return view(
            'paket.payment-pending',
            compact('payment')
        );
    }

/**
 * Simulasi pembayaran berhasil.
 *
 * Nanti method ini akan digantikan oleh
 * callback/webhook dari payment gateway.
 */
public function paymentSuccess(Payment $payment)
{
    $user = User::findOrFail(
        session('user_id')
    );

    if ($payment->owner_id !== $user->id) {
        abort(403);
    }

    if ($payment->status !== 'pending') {
        return redirect()
            ->route('paket')
            ->with(
                'error',
                'Pembayaran ini sudah diproses.'
            );
    }

    DB::transaction(function () use (
        $payment,
        $user
    ) {

        /*
         * =====================================================
         * LOCK PAYMENT
         * =====================================================
         *
         * Mencegah dua request memproses payment
         * yang sama secara bersamaan.
         */
        $payment = Payment::where(
            'id',
            $payment->id
        )
            ->lockForUpdate()
            ->firstOrFail();

        /*
         * Cek ulang status setelah payment dikunci.
         *
         * Ini penting karena status bisa saja berubah
         * sebelum request ini mendapatkan lock.
         */
        if ($payment->status !== 'pending') {
            return;
        }

        /*
         * Subscription owner pembeli.
         */
        $subscription = $user->subscription;

        if (!$subscription) {
            throw new \RuntimeException(
                'Subscription Owner tidak ditemukan.'
            );
        }

        /*
         * =====================================================
         * TANDAI PEMBAYARAN BERHASIL
         * =====================================================
         */
        $payment->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        /*
         * =====================================================
         * AKTIFKAN PAKET PEMBELI
         * =====================================================
         */
        $startsAt = now();

        $durationMonths =
            (int) $payment->duration_months;

        $subscription->update([
            'plan_id' => $payment->plan_id,
            'duration_months' => $durationMonths,
            'status' => 'active',
            'starts_at' => $startsAt,
            'ends_at' => $payment->plan->price > 0
                ? $startsAt->copy()->addMonths(
                    $durationMonths
                )
                : null,
        ]);

        /*
         * =====================================================
         * REWARD REFERRAL
         * =====================================================
         */
        if (
            !empty($payment->referral_code) &&
            (int) $payment->referral_discount_percent > 0 &&
            in_array(
                $durationMonths,
                [6, 12],
                true
            )
        ) {

            $referral = Referral::where(
                'code',
                $payment->referral_code
            )
                ->where('is_active', true)
                ->first();

            if ($referral) {

                /*
                 * Pastikan satu payment hanya menghasilkan
                 * satu reward.
                 */
                $existingReward =
                    ReferralReward::where(
                        'payment_id',
                        $payment->id
                    )->first();

                if (!$existingReward) {

                    /*
                     * 6 bulan  = +1 bulan
                     * 12 bulan = +2 bulan
                     */
                    $rewardMonths =
                        $durationMonths === 12
                            ? 2
                            : 1;

                    /*
                     * Subscription pemilik referral.
                     */
                    $referralOwner = User::find(
                        $referral->owner_id
                    );

                    $referralSubscription =
                        $referralOwner?->subscription;

                    if ($referralSubscription) {

                        /*
                         * Jika subscription masih aktif,
                         * reward ditambahkan dari ends_at.
                         *
                         * Jika sudah expired,
                         * reward dimulai dari sekarang.
                         */
                        $rewardBaseDate =
                            $referralSubscription->ends_at &&
                            $referralSubscription
                                ->ends_at
                                ->isFuture()
                                ? $referralSubscription
                                    ->ends_at
                                    ->copy()
                                : now();

                        $referralSubscription->update([
                            'ends_at' => $rewardBaseDate
                                ->addMonths(
                                    $rewardMonths
                                ),
                            'status' => 'active',
                        ]);

                        /*
                         * Simpan histori reward.
                         */
                        ReferralReward::create([
                            'referral_id' =>
                                $referral->id,

                            'payment_id' =>
                                $payment->id,

                            'owner_id' =>
                                $referral->owner_id,

                            'reward_months' =>
                                $rewardMonths,

                            'status' =>
                                'granted',
                        ]);
                    }
                }
            }
        }
    });

    return redirect()
        ->route('paket')
        ->with(
            'success',
            'Paket berhasil diaktifkan.'
        );
}

          /**
       * Riwayat pembayaran owner.
       **/
public function paymentHistory()
{
    $user = User::findOrFail(
        session('user_id')
    );

    $payments = Payment::with('plan')
        ->where('owner_id', $user->id)
        ->latest()
        ->get();

    return view(
        'paket.payment-history',
        compact('payments')
    );
}

/**
 * Detail pembayaran owner.
 **/
public function paymentDetail(Payment $payment)
{
    $user = User::findOrFail(
        session('user_id')
    );

    // Pastikan pembayaran milik owner yang sedang login
    if ($payment->owner_id !== $user->id) {
        abort(403);
    }

    $payment->load('plan');

    return view(
        'paket.payment-detail',
        compact('payment')
    );
}
}