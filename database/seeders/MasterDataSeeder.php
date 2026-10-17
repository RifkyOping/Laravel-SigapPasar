<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MasterData;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        // Komoditas
        $komoditas = [
            ['name' => 'Beras Premium', 'code' => 'BRS-01', 'satuan' => 'kg', 'harga_referensi' => 14000],
            ['name' => 'Beras Medium', 'code' => 'BRS-02', 'satuan' => 'kg', 'harga_referensi' => 12500],
            ['name' => 'Cabai Merah Keriting', 'code' => 'CBM-01', 'satuan' => 'kg', 'harga_referensi' => 40000],
            ['name' => 'Cabai Rawit Merah', 'code' => 'CBR-01', 'satuan' => 'kg', 'harga_referensi' => 50000],
            ['name' => 'Bawang Merah', 'code' => 'BWM-01', 'satuan' => 'kg', 'harga_referensi' => 30000],
            ['name' => 'Bawang Putih', 'code' => 'BWP-01', 'satuan' => 'kg', 'harga_referensi' => 35000],
            ['name' => 'Minyak Goreng Curah', 'code' => 'MGC-01', 'satuan' => 'liter', 'harga_referensi' => 15000],
            ['name' => 'Minyak Goreng Kemasan', 'code' => 'MGK-01', 'satuan' => 'liter', 'harga_referensi' => 18000],
            ['name' => 'Gula Pasir', 'code' => 'GLP-01', 'satuan' => 'kg', 'harga_referensi' => 17000],
            ['name' => 'Tepung Terigu', 'code' => 'TPT-01', 'satuan' => 'kg', 'harga_referensi' => 12000],
            ['name' => 'Daging Sapi', 'code' => 'DGS-01', 'satuan' => 'kg', 'harga_referensi' => 140000],
            ['name' => 'Daging Ayam Ras', 'code' => 'DGA-01', 'satuan' => 'kg', 'harga_referensi' => 35000],
            ['name' => 'Telur Ayam Ras', 'code' => 'TEL-01', 'satuan' => 'kg', 'harga_referensi' => 28000],
            ['name' => 'Gas LPG 3kg', 'code' => 'LPG-01', 'satuan' => 'tabung', 'harga_referensi' => 20000],
            ['name' => 'BBM', 'code' => 'BBM-01', 'satuan' => 'liter', 'harga_referensi' => 10000],
        ];

        foreach ($komoditas as $k) {
            MasterData::create(array_merge($k, ['type' => 'komoditas']));
        }

        // Kabupaten (Provinsi Sulawesi Barat)
        $kabupaten = [
            ['name' => 'Kabupaten Mamuju', 'code' => 'MMJ', 'deskripsi' => 'Ibukota Provinsi Sulawesi Barat'],
            ['name' => 'Kabupaten Majene', 'code' => 'MJN', 'deskripsi' => 'Kota Pendidikan Sulawesi Barat'],
            ['name' => 'Kabupaten Polewali Mandar', 'code' => 'PLM', 'deskripsi' => 'Sentra Pangan & Pertanian Mandar'],
            ['name' => 'Kabupaten Mamasa', 'code' => 'MMS', 'deskripsi' => 'Wilayah Dataran Tinggi Sulawesi Barat'],
            ['name' => 'Kabupaten Pasangkayu', 'code' => 'PKG', 'deskripsi' => 'Sentra Perkebunan & Perbatasan Palu'],
            ['name' => 'Kabupaten Mamuju Tengah', 'code' => 'MTG', 'deskripsi' => 'Wilayah Penyangga Sentral Sulbar'],
        ];

        foreach ($kabupaten as $k) {
            MasterData::create(array_merge($k, ['type' => 'kabupaten']));
        }

        // Pasar di Sulawesi Barat
        $pasar = [
            ['name' => 'Pasar Regional Mamuju', 'code' => 'PSR-MMJ-01', 'deskripsi' => 'Kabupaten Mamuju'],
            ['name' => 'Pasar Baru Mamuju', 'code' => 'PSR-MMJ-02', 'deskripsi' => 'Kabupaten Mamuju'],
            ['name' => 'Pasar Sentral Majene', 'code' => 'PSR-MJN-01', 'deskripsi' => 'Kabupaten Majene'],
            ['name' => 'Pasar Somba', 'code' => 'PSR-MJN-02', 'deskripsi' => 'Kabupaten Majene'],
            ['name' => 'Pasar Sentral Pekkabata', 'code' => 'PSR-PLM-01', 'deskripsi' => 'Kabupaten Polewali Mandar'],
            ['name' => 'Pasar Sentral Wonomulyo', 'code' => 'PSR-PLM-02', 'deskripsi' => 'Kabupaten Polewali Mandar'],
            ['name' => 'Pasar Tradisional Mamasa', 'code' => 'PSR-MMS-01', 'deskripsi' => 'Kabupaten Mamasa'],
            ['name' => 'Pasar Smart Pasangkayu', 'code' => 'PSR-PKG-01', 'deskripsi' => 'Kabupaten Pasangkayu'],
            ['name' => 'Pasar Sentral Topoyo', 'code' => 'PSR-MTG-01', 'deskripsi' => 'Kabupaten Mamuju Tengah'],
        ];

        foreach ($pasar as $p) {
            MasterData::create(array_merge($p, ['type' => 'pasar']));
        }
    }
}
