<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FeatureSeeder extends Seeder
{
    public function run(): void
    {
        $features = [
            ['name' => 'Catatan Utang', 'slug' => 'debt', 'description' => 'Mencatat transaksi utang pelanggan.'],
            ['name' => 'Pembayaran Cicilan', 'slug' => 'debt_payment', 'description' => 'Mencatat pembayaran sebagian atau cicilan utang.'],
            ['name' => 'Jatuh Tempo Utang', 'slug' => 'debt_due_date', 'description' => 'Mengatur tanggal jatuh tempo utang pelanggan.'],
            ['name' => 'Laporan Piutang', 'slug' => 'receivable_report', 'description' => 'Melihat laporan piutang toko.'],
            ['name' => 'Supplier', 'slug' => 'supplier', 'description' => 'Mengelola data supplier.'],
            ['name' => 'Pembelian Stok', 'slug' => 'stock_purchase', 'description' => 'Mencatat pembelian stok dari supplier.'],
            ['name' => 'Laporan HPP dan Laba', 'slug' => 'profit_report', 'description' => 'Melihat HPP, laba kotor, dan laba bersih.'],
            ['name' => 'Export Laporan', 'slug' => 'report_export', 'description' => 'Mengexport laporan toko.'],
            ['name' => 'Cloud Backup', 'slug' => 'cloud_backup', 'description' => 'Backup data toko ke cloud.'],
            ['name' => 'Import Excel', 'slug' => 'product_import_excel', 'description' => 'Import banyak produk menggunakan Excel.'],
            ['name' => 'Import CSV', 'slug' => 'product_import_csv', 'description' => 'Import banyak produk menggunakan CSV.'],
            ['name' => 'Transfer Stok Antar Toko', 'slug' => 'stock_transfer', 'description' => 'Memindahkan stok antar toko atau cabang.'],
            ['name' => 'Log Aktivitas Staff', 'slug' => 'staff_activity_log', 'description' => 'Melihat aktivitas staff dan kasir.'],
            ['name' => 'Harga Member dan Reseller', 'slug' => 'member_reseller_price', 'description' => 'Mengatur harga khusus member dan reseller.'],
            ['name' => 'Harga Bertingkat', 'slug' => 'tiered_price', 'description' => 'Mengatur harga berdasarkan jumlah pembelian.'],
            ['name' => 'Loyalty', 'slug' => 'loyalty', 'description' => 'Program loyalitas pelanggan.'],
            ['name' => 'Promosi', 'slug' => 'promotion', 'description' => 'Membuat dan mengelola promosi.'],
            ['name' => 'Pengingat Utang', 'slug' => 'debt_reminder', 'description' => 'Pengingat pembayaran utang pelanggan.'],
            ['name' => 'Analitik Lanjutan', 'slug' => 'advanced_analytics', 'description' => 'Analitik bisnis lanjutan.'],
            ['name' => 'Import PDF', 'slug' => 'product_import_pdf', 'description' => 'Import produk dari file PDF.'],
            ['name' => 'Import OCR dan AI', 'slug' => 'product_import_ai', 'description' => 'Membaca data produk dari dokumen menggunakan OCR dan AI.'],
            [
              'name' => 'Transaksi Penjualan Tanpa Batas',
              'slug' => 'sales_unlimited',
              'description' => 'Transaksi penjualan tanpa batas.',
              'is_active' => true,
          ],

          [
              'name' => 'Kelola Stok',
              'slug' => 'stock_management',
              'description' => 'Mengelola stok dan ketersediaan produk.',
              'is_active' => true,
          ],

          [
              'name' => 'Pengeluaran',
              'slug' => 'expense_management',
              'description' => 'Mencatat dan mengelola pengeluaran toko.',
              'is_active' => true,
          ],

          [
              'name' => 'Laporan Dasar',
              'slug' => 'basic_report',
              'description' => 'Melihat laporan dasar penjualan dan pengeluaran.',
              'is_active' => true,
          ],
        ];

        foreach ($features as $feature) {
            DB::table('features')->updateOrInsert(
                ['slug' => $feature['slug']],
                [
                    'name' => $feature['name'],
                    'description' => $feature['description'],
                    'is_active' => true,
                    'updated_at' => now(),
                ]
            );
        }
    }
}