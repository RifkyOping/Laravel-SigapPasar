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
            ['name' => 'Bawang Bombai', 'code' => 'BWB-01', 'satuan' => 'kg', 'harga_referensi' => 40000],
            ['name' => 'Bawang Merah', 'code' => 'BWM-01', 'satuan' => 'kg', 'harga_referensi' => 30000],
            ['name' => 'Bawang Putih', 'code' => 'BWP-01', 'satuan' => 'kg', 'harga_referensi' => 35000],
            ['name' => 'Bawang Putih Honan', 'code' => 'BWH-01', 'satuan' => 'kg', 'harga_referensi' => 39000],
            ['name' => 'Bawang Putih Kating', 'code' => 'BWK-01', 'satuan' => 'kg', 'harga_referensi' => 42500],
            ['name' => 'Beras Medium', 'code' => 'BRS-02', 'satuan' => 'kg', 'harga_referensi' => 12500],
            ['name' => 'Beras Premium', 'code' => 'BRS-01', 'satuan' => 'kg', 'harga_referensi' => 14000],
            ['name' => 'Beras SPHP Bulog', 'code' => 'BRS-03', 'satuan' => 'kg', 'harga_referensi' => 12500],
            ['name' => 'Cabai Merah Besar', 'code' => 'CBB-01', 'satuan' => 'kg', 'harga_referensi' => 73000],
            ['name' => 'Cabai Merah Keriting', 'code' => 'CBM-01', 'satuan' => 'kg', 'harga_referensi' => 40000],
            ['name' => 'Cabai Rawit Hijau', 'code' => 'CBH-01', 'satuan' => 'kg', 'harga_referensi' => 90000],
            ['name' => 'Cabai Rawit Merah', 'code' => 'CBR-01', 'satuan' => 'kg', 'harga_referensi' => 50000],
            ['name' => 'Daging Ayam Kampung', 'code' => 'DGK-01', 'satuan' => 'ekor', 'harga_referensi' => 80000],
            ['name' => 'Daging Ayam Ras', 'code' => 'DGA-01', 'satuan' => 'kg', 'harga_referensi' => 35000],
            ['name' => 'Daging Sapi', 'code' => 'DGS-01', 'satuan' => 'kg', 'harga_referensi' => 140000],
            ['name' => 'Daging Sapi Impor Beku', 'code' => 'DGB-01', 'satuan' => 'kg', 'harga_referensi' => 140000],
            ['name' => 'Daging Sapi Paha Belakang', 'code' => 'DGB-02', 'satuan' => 'kg', 'harga_referensi' => 142000],
            ['name' => 'Daging Sapi Paha Depan', 'code' => 'DGD-01', 'satuan' => 'kg', 'harga_referensi' => 142500],
            ['name' => 'Daging Sapi Tetelan', 'code' => 'DGT-01', 'satuan' => 'kg', 'harga_referensi' => 85000],
            ['name' => 'Garam Halus', 'code' => 'GRM-01', 'satuan' => 'kg', 'harga_referensi' => 12000],
            ['name' => 'Gula Pasir', 'code' => 'GLP-01', 'satuan' => 'kg', 'harga_referensi' => 17000],
            ['name' => 'Gula Pasir Curah', 'code' => 'GPC-01', 'satuan' => 'kg', 'harga_referensi' => 17000],
            ['name' => 'Gula Pasir Kemasan', 'code' => 'GPK-01', 'satuan' => 'kg', 'harga_referensi' => 18500],
            ['name' => 'Ikan Bandeng', 'code' => 'IKB-01', 'satuan' => 'kg', 'harga_referensi' => 30000],
            ['name' => 'Ikan Kembung', 'code' => 'IKK-01', 'satuan' => 'kg', 'harga_referensi' => 35000],
            ['name' => 'Ikan Teri', 'code' => 'IKT-01', 'satuan' => 'kg', 'harga_referensi' => 80000],
            ['name' => 'Ikan Tongkol', 'code' => 'IKO-01', 'satuan' => 'kg', 'harga_referensi' => 32000],
            ['name' => 'Jagung Lokal Pipilan', 'code' => 'JGP-01', 'satuan' => 'kg', 'harga_referensi' => 8000],
            ['name' => 'Jeruk Lokal', 'code' => 'JRK-01', 'satuan' => 'kg', 'harga_referensi' => 20000],
            ['name' => 'Kacang Hijau', 'code' => 'KCH-01', 'satuan' => 'kg', 'harga_referensi' => 25000],
            ['name' => 'Kacang Panjang', 'code' => 'KCP-01', 'satuan' => 'kg', 'harga_referensi' => 5000],
            ['name' => 'Kacang Tanah', 'code' => 'KCT-01', 'satuan' => 'kg', 'harga_referensi' => 28000],
            ['name' => 'Kangkung', 'code' => 'KNG-01', 'satuan' => 'ikat', 'harga_referensi' => 4000],
            ['name' => 'Kedelai Impor', 'code' => 'KDI-01', 'satuan' => 'kg', 'harga_referensi' => 13000],
            ['name' => 'Kedelai Lokal', 'code' => 'KDL-01', 'satuan' => 'kg', 'harga_referensi' => 12000],
            ['name' => 'Kentang Sedang', 'code' => 'KTG-01', 'satuan' => 'kg', 'harga_referensi' => 20000],
            ['name' => 'Ketela Pohon', 'code' => 'KTL-01', 'satuan' => 'kg', 'harga_referensi' => 6000],
            ['name' => 'Ketimun Sedang', 'code' => 'KTM-01', 'satuan' => 'kg', 'harga_referensi' => 8000],
            ['name' => 'Mie Instan', 'code' => 'MIE-01', 'satuan' => 'bungkus', 'harga_referensi' => 3500],
            ['name' => 'Minyak Goreng Curah', 'code' => 'MGC-01', 'satuan' => 'liter', 'harga_referensi' => 15000],
            ['name' => 'Minyak Goreng Kemasan', 'code' => 'MGK-01', 'satuan' => 'liter', 'harga_referensi' => 18000],
            ['name' => 'Minyakita', 'code' => 'MYK-01', 'satuan' => 'liter', 'harga_referensi' => 15700],
            ['name' => 'Pisang Lokal', 'code' => 'PSG-01', 'satuan' => 'sisir', 'harga_referensi' => 15000],
            ['name' => 'Sawi Hijau', 'code' => 'SWH-01', 'satuan' => 'ikat', 'harga_referensi' => 5000],
            ['name' => 'Susu Bubuk', 'code' => 'SSB-01', 'satuan' => 'kotak', 'harga_referensi' => 45000],
            ['name' => 'Susu Bubuk Balita', 'code' => 'SSB-02', 'satuan' => 'kotak', 'harga_referensi' => 50000],
            ['name' => 'Susu Kental Manis', 'code' => 'SKM-01', 'satuan' => 'kaleng', 'harga_referensi' => 13000],
            ['name' => 'Tahu Putih', 'code' => 'THU-01', 'satuan' => 'bungkus', 'harga_referensi' => 6000],
            ['name' => 'Telur Ayam Kampung', 'code' => 'TAK-01', 'satuan' => 'butir', 'harga_referensi' => 3000],
            ['name' => 'Telur Ayam Ras', 'code' => 'TEL-01', 'satuan' => 'kg', 'harga_referensi' => 28000],
            ['name' => 'Tempe Bungkus', 'code' => 'TMP-01', 'satuan' => 'bungkus', 'harga_referensi' => 5000],
            ['name' => 'Tepung Terigu', 'code' => 'TPT-01', 'satuan' => 'kg', 'harga_referensi' => 12000],
            ['name' => 'Tomat', 'code' => 'TMT-01', 'satuan' => 'kg', 'harga_referensi' => 14000],
            ['name' => 'Udang Basah', 'code' => 'UDG-01', 'satuan' => 'kg', 'harga_referensi' => 75000],
            ['name' => 'Gas LPG 3kg', 'code' => 'LPG-01', 'satuan' => 'tabung', 'harga_referensi' => 20000],
            ['name' => 'BBM', 'code' => 'BBM-01', 'satuan' => 'liter', 'harga_referensi' => 10000],
        ];

        foreach ($komoditas as $k) {
            MasterData::firstOrCreate(
                ['type' => 'komoditas', 'name' => $k['name']],
                $k
            );
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
            MasterData::firstOrCreate(
                ['type' => 'kabupaten', 'name' => $k['name']],
                $k
            );
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
            MasterData::firstOrCreate(
                ['type' => 'pasar', 'name' => $p['name']],
                $p
            );
        }
    }
}
