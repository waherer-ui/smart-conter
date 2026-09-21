<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Plan;
use App\Models\Store;
use App\Models\User;
use App\Models\Subscription;
use App\Models\Referral;
use App\Models\ReferralReward;
use App\Services\AuditLogService;
use App\Services\MidtransService;

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
    public function createPayment(
        Request $request,
        Plan $plan,
        MidtransService $midtransService
    ) {
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

        $paymentMethod =
            $validated['payment_method'];

        $durationMonths =
            (int) $validated['duration_months'];

        $referralCode =
            $validated['referral_code'] ?? null;

        $referral = null;

        /*
         * =====================================================
         * VALIDASI REFERRAL
         * =====================================================
         */
        if ($referralCode) {

            $referral = Referral::where(
                'code',
                strtoupper(trim($referralCode))
            )
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

            if (
                (int) $referral->owner_id ===
                (int) $user->id
            ) {
                return back()
                    ->withInput()
                    ->with(
                        'error',
                        'Anda tidak dapat menggunakan kode referral milik sendiri.'
                    );
            }
        }

        /*
         * =====================================================
         * DISKON BERDASARKAN MASA AKTIF
         * =====================================================
         */
        $durationDiscounts = [
            1 => 0,
            3 => 5,
            6 => 7,
            12 => 12,
        ];

        $discountPercent =
            $durationDiscounts[$durationMonths];

        /*
         * =====================================================
         * HARGA NORMAL
         * =====================================================
         */
        $baseAmount =
            $plan->price *
            $durationMonths;

        /*
         * =====================================================
         * DISKON DURASI
         * =====================================================
         */
        $discountAmount =
            $baseAmount *
            ($discountPercent / 100);

        /*
         * Harga setelah diskon durasi.
         */
        $amountAfterDurationDiscount =
            $baseAmount -
            $discountAmount;

        /*
         * =====================================================
         * DISKON REFERRAL
         * =====================================================
         *
         * Referral hanya berlaku untuk:
         * - Pro
         * - Premium
         * - 6 bulan
         * - 12 bulan
         */
        $referralDiscountPercent = 0;

        $referralDiscountAmount = 0;

        if (
            $referral &&
            in_array(
                $durationMonths,
                [6, 12],
                true
            ) &&
            in_array(
                $plan->slug,
                ['pro', 'premium'],
                true
            )
        ) {
            $referralDiscountPercent = 8;

            $referralDiscountAmount =
                round(
                    $amountAfterDurationDiscount *
                    (
                        $referralDiscountPercent /
                        100
                    )
                );
        }

        /*
         * =====================================================
         * HARGA AKHIR
         * =====================================================
         */
        $finalAmount = round(
            $amountAfterDurationDiscount -
            $referralDiscountAmount
        );

        /*
         * =====================================================
         * BUAT PAYMENT
         * =====================================================
         */
        $payment = Payment::create([
            'owner_id' =>
                $user->id,

            'plan_id' =>
                $plan->id,

            'duration_months' =>
                $durationMonths,

            'amount' =>
                $finalAmount,

            'referral_code' =>
                $referral?->code,

            'referral_discount_percent' =>
                $referralDiscountPercent,

            'referral_discount_amount' =>
                $referralDiscountAmount,

            'payment_method' =>
                $paymentMethod,

            'status' =>
                'pending',

            'reference' =>
                'PAY-' .
                now()->format('YmdHis') .
                '-' .
                strtoupper(
                    Str::random(6)
                ),
        ]);

        /*
 * =====================================================
 * MIDTRANS
 * =====================================================
 */

$midtransOrderId =
    'KASIRKU-' .
    $payment->id .
    '-' .
    strtoupper(Str::random(8));

$snapParams = [

    'transaction_details' => [
        'order_id' =>
            $midtransOrderId,

        'gross_amount' =>
            (int) $payment->amount,
    ],

    'customer_details' => [
        'first_name' =>
            $user->name,

        'email' =>
            $user->email,
    ],

    'item_details' => [
        [
            'id' =>
                'PLAN-' . $plan->id,

            'price' =>
                (int) $payment->amount,

            'quantity' =>
                1,

            'name' =>
                $plan->name .
                ' - ' .
                $durationMonths .
                ' bulan',
        ],
    ],

    'custom_expiry' => [
        'expiry_duration' =>
            1,

        'unit' =>
            'day',
    ],
];

try {

    $snapToken =
        $midtransService
            ->createSnapToken(
                $snapParams
            );

    $payment->update([
        'midtrans_order_id' =>
            $midtransOrderId,

        'midtrans_snap_token' =>
            $snapToken,

        'midtrans_transaction_status' =>
            'pending',
    ]);

} catch (\Throwable $e) {

    /*
     * Jika gagal membuat transaksi Midtrans,
     * jangan biarkan Payment KasirKU
     * menggantung sebagai transaksi aktif.
     */

    $payment->update([
        'status' =>
            'failed',
    ]);

    return back()
        ->withInput()
        ->with(
            'error',
            'Gagal membuat transaksi pembayaran. Silakan coba lagi.'
        );
}

        /*
         * =====================================================
         * AUDIT LOG
         * =====================================================
         */
        AuditLogService::log(
            'payment_created',

            'Membuat pembayaran "' .
            $payment->reference .
            '" untuk paket "' .
            $plan->name .
            '" selama ' .
            $durationMonths .
            ' bulan sebesar Rp' .
            number_format(
                $finalAmount,
                0,
                ',',
                '.'
            ) .
            '.',

            $payment,

            $user->id,

            session('active_store_id'),

            null,

            [
                'reference' =>
                    $payment->reference,

                'plan_id' =>
                    $plan->id,

                'plan_name' =>
                    $plan->name,

                'duration_months' =>
                    $durationMonths,

                'amount' =>
                    $finalAmount,

                'payment_method' =>
                    $paymentMethod,

                'referral_code' =>
                    $payment->referral_code,

                'referral_discount_percent' =>
                    $referralDiscountPercent,

                'referral_discount_amount' =>
                    $referralDiscountAmount,

                'status' =>
                    $payment->status,
            ]
        );

        return redirect()
            ->route(
                'paket.payment.pending',
                $payment->id
            );
    }

    /**
     * Validasi kode referral untuk UI.
     */
    public function validateReferral(
        Request $request
    ) {
        $user = User::findOrFail(
            session('user_id')
        );

        $code = strtoupper(
            trim(
                $request->input(
                    'code',
                    ''
                )
            )
        );

        if ($code === '') {
            return response()->json([
                'valid' => false,
                'message' =>
                    'Masukkan kode referral.',
            ]);
        }

        $referral = Referral::where(
            'code',
            $code
        )
            ->where('is_active', true)
            ->first();

        if (!$referral) {
            return response()->json([
                'valid' => false,
                'message' =>
                    'Kode referral tidak ditemukan atau sudah tidak aktif.',
            ]);
        }

        if (
            (int) $referral->owner_id ===
            (int) $user->id
        ) {
            return response()->json([
                'valid' => false,
                'message' =>
                    'Anda tidak dapat menggunakan kode referral milik sendiri.',
            ]);
        }

        return response()->json([
            'valid' => true,
            'message' =>
                'Kode referral valid. Diskon 8% diterapkan.',
        ]);
    }

    /**
     * Halaman menunggu pembayaran.
     */
    public function paymentPending(
        Payment $payment
    ) {
        $user = User::findOrFail(
            session('user_id')
        );

        if (
            $payment->owner_id !==
            $user->id
        ) {
            abort(403);
        }

        return view(
            'paket.payment-pending',
            compact('payment')
        );
    }

    /**
 * Mengecek status payment untuk auto-redirect.
 *
 * Method ini hanya membaca status dari database.
 * Aktivasi tetap dilakukan oleh webhook Midtrans.
 */
public function paymentStatus(
    Payment $payment
) {
    $userId = session('user_id');

    if (
        !$userId ||
        (int) $payment->owner_id !== (int) $userId
    ) {
        abort(403);
    }

    return response()->json([
        'status' => $payment->fresh()->status,
    ]);
}

/**
 * Mengecek status pembayaran langsung ke Midtrans.
 *
 * Method ini hanya sebagai fallback/manual check.
 * Aktivasi utama tetap melalui notification Midtrans.
 */
public function checkStatus(
    Payment $payment,
    MidtransService $midtransService
)
{
    $userId = session('user_id');

    if (!$userId || (int) $payment->owner_id !== (int) $userId) {
        abort(403);
    }

    if (!$payment->midtrans_order_id) {
        return back()->with(
            'error',
            'Order ID Midtrans tidak ditemukan.'
        );
    }

    try {

        $status = $midtransService->getTransactionStatus(
            $payment->midtrans_order_id
        );

        /*
         * =====================================================
         * VALIDASI ORDER ID
         * =====================================================
         */
        if (
            !isset($status->order_id) ||
            $status->order_id !== $payment->midtrans_order_id
        ) {
            return back()->with(
                'error',
                'Order ID dari Midtrans tidak cocok.'
            );
        }

        /*
         * =====================================================
         * VALIDASI NOMINAL
         * =====================================================
         */
        if (
            isset($status->gross_amount) &&
            (float) $status->gross_amount !== (float) $payment->amount
        ) {
            return back()->with(
                'error',
                'Nominal pembayaran tidak cocok.'
            );
        }

        /*
         * Simpan status terbaru dari Midtrans.
         */
        $payment->update([
            'midtrans_transaction_status' =>
                $status->transaction_status,
        ]);

        /*
         * =====================================================
         * PEMBAYARAN BERHASIL
         * =====================================================
         */
        if (
            $status->transaction_status === 'settlement' &&
            (
                !isset($status->fraud_status) ||
                $status->fraud_status === 'accept'
            )
        ) {

            $this->processSuccessfulPayment(
                $payment,
                (int) $payment->owner_id
            );

            return redirect()
                ->route('paket')
                ->with(
                    'success',
                    'Pembayaran berhasil. Paket Anda telah diaktifkan.'
                );
        }

        /*
         * =====================================================
         * BELUM BERHASIL
         * =====================================================
         */
        return back()->with(
            'error',
            'Status pembayaran saat ini: ' .
            $status->transaction_status
        );

    } catch (\Throwable $e) {

        return back()->with(
            'error',
            'Gagal mengecek status pembayaran: ' .
            $e->getMessage()
        );
    }
}

/**
 * Notification / webhook dari Midtrans.
 *
 * Endpoint ini dipanggil langsung oleh Midtrans.
 * Tidak menggunakan session login.
 */
public function midtransNotification(
    Request $request
) {
    try {

        /*
         * =====================================================
         * AMBIL DATA NOTIFICATION
         * =====================================================
         */
        $orderId = $request->input('order_id');
        $statusCode = $request->input('status_code');
        $grossAmount = $request->input('gross_amount');
        $signatureKey = $request->input('signature_key');
        $transactionStatus = $request->input('transaction_status');
        $fraudStatus = $request->input('fraud_status');

        /*
         * =====================================================
         * VALIDASI DATA WAJIB
         * =====================================================
         */
        if (
            !$orderId ||
            !$statusCode ||
            !$grossAmount ||
            !$signatureKey ||
            !$transactionStatus
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Data notification tidak lengkap.',
            ], 400);
        }

        /*
         * =====================================================
         * VALIDASI SIGNATURE MIDTRANS
         * =====================================================
         *
         * SHA512:
         *
         * order_id + status_code + gross_amount + ServerKey
         */
        $expectedSignature = hash(
            'sha512',
            $orderId .
            $statusCode .
            $grossAmount .
            config('midtrans.server_key')
        );

        if (
            !hash_equals(
                $expectedSignature,
                $signatureKey
            )
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Signature notification tidak valid.',
            ], 403);
        }

        /*
         * =====================================================
         * CARI PAYMENT
         * =====================================================
         */
        $payment = Payment::where(
            'midtrans_order_id',
            $orderId
        )
            ->with('plan')
            ->first();

        if (!$payment) {

            \Log::warning(
                'Midtrans notification: payment tidak ditemukan.',
                [
                    'order_id' => $orderId,
                ]
            );

            return response()->json([
                'success' => false,
                'message' => 'Payment tidak ditemukan.',
            ], 404);
        }

        /*
         * =====================================================
         * VALIDASI NOMINAL
         * =====================================================
         */
        if (
            (float) $grossAmount !==
            (float) $payment->amount
        ) {

            \Log::warning(
                'Midtrans notification: nominal tidak cocok.',
                [
                    'order_id' => $orderId,
                    'payment_amount' => $payment->amount,
                    'midtrans_amount' => $grossAmount,
                ]
            );

            return response()->json([
                'success' => false,
                'message' => 'Nominal pembayaran tidak cocok.',
            ], 400);
        }

        /*
         * =====================================================
         * SIMPAN STATUS MIDTRANS
         * =====================================================
         */
        $payment->update([
            'midtrans_transaction_status' =>
                $transactionStatus,
        ]);

        /*
         * =====================================================
         * TRANSAKSI BERHASIL
         * =====================================================
         */
        if (
            $transactionStatus === 'settlement' &&
            (
                !$fraudStatus ||
                $fraudStatus === 'accept'
            )
        ) {

            $this->processSuccessfulPayment(
                $payment,
                (int) $payment->owner_id
            );
        }

        /*
         * =====================================================
         * RESPONSE KE MIDTRANS
         * =====================================================
         */
        return response()->json([
            'success' => true,
            'message' => 'Notification berhasil diproses.',
        ], 200);

    } catch (\Throwable $e) {

        \Log::error(
            'Midtrans notification error.',
            [
                'message' => $e->getMessage(),
                'order_id' => $request->input('order_id'),
            ]
        );

        return response()->json([
            'success' => false,
            'message' => 'Notification gagal diproses.',
        ], 500);
    }
}

/**
 * Memproses pembayaran yang sudah dikonfirmasi berhasil
 * oleh Midtrans.
 *
 * Method ini tidak menggunakan session sehingga aman
 * dipanggil oleh webhook maupun checkStatus().
 */
protected function processSuccessfulPayment(
    Payment $payment,
    int $ownerId
): void {

    DB::transaction(function () use (
        $payment,
        $ownerId
    ) {

        /*
         * =====================================================
         * LOCK PAYMENT
         * =====================================================
         */
        $payment = Payment::where(
            'id',
            $payment->id
        )
            ->lockForUpdate()
            ->firstOrFail();

        /*
         * =====================================================
         * IDEMPOTENCY
         * =====================================================
         *
         * Jika notification Midtrans dikirim lebih dari sekali,
         * pembayaran tidak akan diproses dua kali.
         */
        if ($payment->status === 'paid') {
            return;
        }

        if ($payment->status !== 'pending') {
            return;
        }

        /*
         * =====================================================
         * LOAD PLAN
         * =====================================================
         */
        $payment->load('plan');

        if (!$payment->plan) {
            throw new \RuntimeException(
                'Plan pembayaran tidak ditemukan.'
            );
        }

        /*
         * =====================================================
         * OWNER
         * =====================================================
         */
        $user = User::find($ownerId);

        if (!$user) {
            throw new \RuntimeException(
                'Owner pembayaran tidak ditemukan.'
            );
        }

        /*
         * =====================================================
         * LOCK SUBSCRIPTION OWNER
         * =====================================================
         *
         * Penting untuk model subscription owner-based.
         *
         * Satu owner dapat memiliki banyak toko,
         * tetapi subscription tetap satu di level owner.
         */
        $subscription = Subscription::where(
            'owner_id',
            $ownerId
        )
            ->lockForUpdate()
            ->first();

        if (!$subscription) {
            throw new \RuntimeException(
                'Subscription Owner tidak ditemukan.'
            );
        }

        /*
         * Simpan status lama untuk audit.
         */
        $oldPaymentStatus =
            $payment->status;

        /*
         * =====================================================
         * PAYMENT → PAID
         * =====================================================
         */
        $payment->update([
            'status' =>
                'paid',

            'paid_at' =>
                now(),
        ]);

        /*
         * =====================================================
         * AUDIT PAYMENT
         * =====================================================
         *
         * Tidak ada session karena proses ini bisa berasal
         * dari webhook Midtrans.
         */
        AuditLogService::log(
            'payment_success',

            'Pembayaran "' .
            $payment->reference .
            '" berhasil diproses sebesar Rp' .
            number_format(
                $payment->amount,
                0,
                ',',
                '.'
            ) .
            '.',

            $payment,

            $ownerId,

            null,

            [
                'status' =>
                    $oldPaymentStatus,
            ],

            [
                'status' =>
                    'paid',

                'paid_at' =>
                    $payment->paid_at,
            ]
        );

        /*
         * =====================================================
         * HITUNG MASA AKTIF BARU
         * =====================================================
         */
        $durationMonths =
            (int) $payment->duration_months;

        $now = now();

        /*
         * =====================================================
         * SIMPAN SUBSCRIPTION LAMA
         * =====================================================
         */
        $oldSubscriptionValues = [
            'plan_id' =>
                $subscription->plan_id,

            'duration_months' =>
                $subscription->duration_months,

            'status' =>
                $subscription->status,

            'starts_at' =>
                $subscription->starts_at,

            'ends_at' =>
                $subscription->ends_at,
        ];

        /*
         * =====================================================
         * TENTUKAN TANGGAL MULAI
         * =====================================================
         *
         * Jika subscription lama masih aktif:
         *
         * Premium sampai 21 Sep 2027
         * beli Premium 12 bulan
         *
         * menjadi:
         * 21 Sep 2028
         *
         * BUKAN reset dari tanggal pembayaran.
         */
        if (
            $subscription->status === 'active' &&
            $subscription->ends_at &&
            $subscription->ends_at->isFuture()
        ) {

            $startsAt =
                $subscription->starts_at ??
                $now;

            $endsAt =
                $subscription->ends_at
                    ->copy()
                    ->addMonths(
                        $durationMonths
                    );

        } else {

            $startsAt =
                $now;

            $endsAt =
                $payment->plan->price > 0
                    ? $startsAt
                        ->copy()
                        ->addMonths(
                            $durationMonths
                        )
                    : null;
        }

        /*
         * =====================================================
         * UPDATE SUBSCRIPTION
         * =====================================================
         */
        $subscription->update([
            'plan_id' =>
                $payment->plan_id,

            'duration_months' =>
                $durationMonths,

            'status' =>
                'active',

            'starts_at' =>
                $startsAt,

            'ends_at' =>
                $endsAt,
        ]);

        /*
         * =====================================================
         * AUDIT SUBSCRIPTION
         * =====================================================
         */
        AuditLogService::log(
            'subscription_updated',

            'Mengaktifkan paket "' .
            $payment->plan->name .
            '" selama ' .
            $durationMonths .
            ' bulan untuk owner.',

            $subscription,

            $ownerId,

            null,

            $oldSubscriptionValues,

            [
                'plan_id' =>
                    $subscription->plan_id,

                'plan_name' =>
                    $payment->plan->name,

                'duration_months' =>
                    $durationMonths,

                'status' =>
                    'active',

                'starts_at' =>
                    $subscription->starts_at,

                'ends_at' =>
                    $subscription->ends_at,
            ]
        );

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
                ->where(
                    'is_active',
                    true
                )
                ->first();

            if ($referral) {

                /*
                 * Satu payment hanya boleh menghasilkan
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
                     * Owner pemilik referral.
                     */
                    $referralOwner =
                        User::find(
                            $referral->owner_id
                        );

                    $referralSubscription =
                        $referralOwner
                            ?->subscription;

                    if ($referralSubscription) {

                        /*
                         * Jika masih aktif,
                         * reward ditambahkan dari ends_at.
                         */
                        $rewardBaseDate =
                            $referralSubscription->ends_at &&
                            $referralSubscription->ends_at->isFuture()
                                ? $referralSubscription
                                    ->ends_at
                                    ->copy()
                                : now();

                        $newRewardEndsAt =
                            $rewardBaseDate
                                ->copy()
                                ->addMonths(
                                    $rewardMonths
                                );

                        $referralSubscription->update([
                            'ends_at' =>
                                $newRewardEndsAt,

                            'status' =>
                                'active',
                        ]);

                        /*
                         * Simpan histori reward.
                         */
                        $reward =
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

                        /*
                         * =====================================================
                         * AUDIT REFERRAL
                         * =====================================================
                         */
                        AuditLogService::log(
                            'referral_reward_granted',

                            'Memberikan reward referral kepada owner "' .
                            (
                                $referralOwner
                                    ?->name ??
                                'Owner'
                            ) .
                            '" selama ' .
                            $rewardMonths .
                            ' bulan.',

                            $reward,

                            $ownerId,

                            null,

                            null,

                            [
                                'referral_id' =>
                                    $referral->id,

                                'referral_code' =>
                                    $referral->code,

                                'payment_id' =>
                                    $payment->id,

                                'payment_reference' =>
                                    $payment->reference,

                                'owner_id' =>
                                    $referral->owner_id,

                                'owner_name' =>
                                    $referralOwner
                                        ?->name,

                                'reward_months' =>
                                    $rewardMonths,

                                'status' =>
                                    'granted',

                                'ends_at' =>
                                    $newRewardEndsAt,
                            ]
                        );
                    }
                }
            }
        }
    });
}

    /**
     * Riwayat pembayaran owner.
     */
    public function paymentHistory()
    {
        $user = User::findOrFail(
            session('user_id')
        );

        $payments = Payment::with('plan')
            ->where(
                'owner_id',
                $user->id
            )
            ->latest()
            ->get();

        return view(
            'paket.payment-history',
            compact('payments')
        );
    }

    /**
     * Detail pembayaran owner.
     */
    public function paymentDetail(
        Payment $payment
    ) {
        $user = User::findOrFail(
            session('user_id')
        );

        /*
         * Pastikan pembayaran milik
         * owner yang sedang login.
         */
        if (
            $payment->owner_id !==
            $user->id
        ) {
            abort(403);
        }

        $payment->load('plan');

        return view(
            'paket.payment-detail',
            compact('payment')
        );
    }
}