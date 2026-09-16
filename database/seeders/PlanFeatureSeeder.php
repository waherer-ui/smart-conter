<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanFeatureSeeder extends Seeder
{
    public function run(): void
    {
        $freeFeatures = [
            'sales_unlimited',
            'stock_management',
            'expense_management',
            'basic_report',
        ];

        $proFeatures = [
            'debt',
            'debt_payment',
            'debt_due_date',
            'receivable_report',
            'supplier',
            'stock_purchase',
            'profit_report',
            'report_export',
            'cloud_backup',
            'product_import_excel',
            'product_import_csv',
        ];

        $premiumFeatures = [
            'stock_transfer',
            'staff_activity_log',
            'member_reseller_price',
            'tiered_price',
            'loyalty',
            'promotion',
            'debt_reminder',
            'advanced_analytics',
            'product_import_pdf',
            'product_import_ai',
        ];

        $freePlan = DB::table('plans')
            ->where('slug', 'free')
            ->first();

        $proPlan = DB::table('plans')
            ->where('slug', 'pro')
            ->first();

        $premiumPlan = DB::table('plans')
            ->where('slug', 'premium')
            ->first();

        $featureIds = DB::table('features')
            ->whereIn(
                'slug',
                array_merge(
                    $freeFeatures,
                    $proFeatures,
                    $premiumFeatures
                )
            )
            ->pluck('id', 'slug');

        // FREE
        foreach ($freeFeatures as $slug) {
            DB::table('plan_features')->updateOrInsert(
                [
                    'plan_id' => $freePlan->id,
                    'feature_id' => $featureIds[$slug],
                ],
                [
                    'updated_at' => now(),
                ]
            );
        }

        // PRO
        foreach ($proFeatures as $slug) {
            DB::table('plan_features')->updateOrInsert(
                [
                    'plan_id' => $proPlan->id,
                    'feature_id' => $featureIds[$slug],
                ],
                [
                    'updated_at' => now(),
                ]
            );
        }

        // PREMIUM
        foreach (array_merge($proFeatures, $premiumFeatures) as $slug) {
            DB::table('plan_features')->updateOrInsert(
                [
                    'plan_id' => $premiumPlan->id,
                    'feature_id' => $featureIds[$slug],
                ],
                [
                    'updated_at' => now(),
                ]
            );
        }
    }
}