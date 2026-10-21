<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\MasterData;

return new class extends Migration
{
    public function up(): void
    {
        $newCommodities = [
            ['name' => 'Bawang Bombai', 'code' => 'BWB-01', 'satuan' => 'kg', 'harga_referensi' => 40000],
            ['name' => 'Bawang Putih Honan', 'code' => 'BWH-01', 'satuan' => 'kg', 'harga_referensi' => 39000],
            ['name' => 'Bawang Putih Kating', 'code' => 'BWK-01', 'satuan' => 'kg', 'harga_referensi' => 42500],
            ['name' => 'Beras SPHP Bulog', 'code' => 'BRS-03', 'satuan' => 'kg', 'harga_referensi' => 12500],
            ['name' => 'Cabai Merah Besar', 'code' => 'CBB-01', 'satuan' => 'kg', 'harga_referensi' => 73000],
            ['name' => 'Cabai Rawit Hijau', 'code' => 'CBH-01', 'satuan' => 'kg', 'harga_referensi' => 90000],
            ['name' => 'Daging Ayam Kampung', 'code' => 'DGK-01', 'satuan' => 'ekor', 'harga_referensi' => 80000],
            ['name' => 'Daging Sapi Impor Beku', 'code' => 'DGB-01', 'satuan' => 'kg', 'harga_referensi' => 140000],
            ['name' => 'Daging Sapi Paha Belakang', 'code' => 'DGB-02', 'satuan' => 'kg', 'harga_referensi' => 142000],
            ['name' => 'Daging Sapi Paha Depan', 'code' => 'DGD-01', 'satuan' => 'kg', 'harga_referensi' => 142500],
            ['name' => 'Daging Sapi Tetelan', 'code' => 'DGT-01', 'satuan' => 'kg', 'harga_referensi' => 85000],
            ['name' => 'Garam Halus', 'code' => 'GRM-01', 'satuan' => 'kg', 'harga_referensi' => 12000],
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
            ['name' => 'Minyakita', 'code' => 'MYK-01', 'satuan' => 'liter', 'harga_referensi' => 15700],
            ['name' => 'Pisang Lokal', 'code' => 'PSG-01', 'satuan' => 'sisir', 'harga_referensi' => 15000],
            ['name' => 'Sawi Hijau', 'code' => 'SWH-01', 'satuan' => 'ikat', 'harga_referensi' => 5000],
            ['name' => 'Susu Bubuk', 'code' => 'SSB-01', 'satuan' => 'kotak', 'harga_referensi' => 45000],
            ['name' => 'Susu Bubuk Balita', 'code' => 'SSB-02', 'satuan' => 'kotak', 'harga_referensi' => 50000],
            ['name' => 'Susu Kental Manis', 'code' => 'SKM-01', 'satuan' => 'kaleng', 'harga_referensi' => 13000],
            ['name' => 'Tahu Putih', 'code' => 'THU-01', 'satuan' => 'bungkus', 'harga_referensi' => 6000],
            ['name' => 'Telur Ayam Kampung', 'code' => 'TAK-01', 'satuan' => 'butir', 'harga_referensi' => 3000],
            ['name' => 'Tempe Bungkus', 'code' => 'TMP-01', 'satuan' => 'bungkus', 'harga_referensi' => 5000],
            ['name' => 'Tomat', 'code' => 'TMT-01', 'satuan' => 'kg', 'harga_referensi' => 14000],
            ['name' => 'Udang Basah', 'code' => 'UDG-01', 'satuan' => 'kg', 'harga_referensi' => 75000],
        ];

        foreach ($newCommodities as $item) {
            MasterData::firstOrCreate(
                [
                    'type' => 'komoditas',
                    'name' => $item['name'],
                ],
                [
                    'code'            => $item['code'],
                    'satuan'          => $item['satuan'],
                    'harga_referensi' => $item['harga_referensi'],
                    'is_active'       => true,
                ]
            );
        }
    }

    public function down(): void
    {
        $names = [
            'Bawang Bombai',
            'Bawang Putih Honan',
            'Bawang Putih Kating',
            'Beras SPHP Bulog',
            'Cabai Merah Besar',
            'Cabai Rawit Hijau',
            'Daging Ayam Kampung',
            'Daging Sapi Impor Beku',
            'Daging Sapi Paha Belakang',
            'Daging Sapi Paha Depan',
            'Daging Sapi Tetelan',
            'Garam Halus',
            'Gula Pasir Curah',
            'Gula Pasir Kemasan',
            'Ikan Bandeng',
            'Ikan Kembung',
            'Ikan Teri',
            'Ikan Tongkol',
            'Jagung Lokal Pipilan',
            'Jeruk Lokal',
            'Kacang Hijau',
            'Kacang Panjang',
            'Kacang Tanah',
            'Kangkung',
            'Kedelai Impor',
            'Kedelai Lokal',
            'Kentang Sedang',
            'Ketela Pohon',
            'Ketimun Sedang',
            'Mie Instan',
            'Minyakita',
            'Pisang Lokal',
            'Sawi Hijau',
            'Susu Bubuk',
            'Susu Bubuk Balita',
            'Susu Kental Manis',
            'Tahu Putih',
            'Telur Ayam Kampung',
            'Tempe Bungkus',
            'Tomat',
            'Udang Basah',
        ];

        MasterData::where('type', 'komoditas')->whereIn('name', $names)->delete();
    }
};
