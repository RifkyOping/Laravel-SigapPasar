<?php

namespace Database\Seeders;

use App\Models\CommodityPrice;
use App\Models\MasterData;
use App\Models\User;
use Illuminate\Database\Seeder;

class CommodityPriceSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = User::where('role', 'super_admin')->first();
        $adminId = $superAdmin?->id;

        $dates = ['2026-10-12', '2026-10-13', '2026-10-14', '2026-10-15', '2026-10-16'];

        $masterKomoditas = MasterData::where('type', 'komoditas')->get();

        $rows = [
            ['Bawang Bombai', 'Rp/Kg', [40028, 39472, 39472, 39472, 39472]],
            ['Bawang Merah', 'Rp/Kg', [36806, 36694, 36694, 36694, 36694]],
            ['Bawang Putih Honan', 'Rp/Kg', [39167, 39056, 39056, 39056, 39056]],
            ['Bawang Putih Kating', 'Rp/Kg', [42500, 42500, 42500, 42500, 42500]],
            ['Beras Medium', 'Rp/Kg', [13808, 13808, 13808, 13808, 13808]],
            ['Beras Premium', 'Rp/Kg', [15180, 15180, 15180, 15180, 15180]],
            ['Beras SPHP Bulog', 'Rp/Kg', [12150, 12150, 12150, 12150, 12150]],
            ['Cabai Merah Besar', 'Rp/Kg', [74400, 74733, 73733, 72733, 72733]],
            ['Cabai Merah Keriting', 'Rp/Kg', [83861, 84583, 85278, 87500, 88889]],
            ['Cabai Rawit Hijau', 'Rp/Kg', [89000, 101200, 100533, 100533, 102200]],
            ['Cabai Rawit Merah', 'Rp/Kg', [117778, 117222, 115278, 115278, 115278]],
            ['Daging Ayam Kampung', 'Rp/Ekor', [82083, 82083, 82083, 82083, 82083]],
            ['Daging Ayam Ras', 'Rp/Kg', [40717, 40717, 41050, 40697, 40697]],
            ['Daging Sapi Impor Beku', 'Rp/Kg', [149900, 149900, 149900, 149900, 149900]],
            ['Daging Sapi Paha Belakang', 'Rp/Kg', [142000, 142000, 142000, 142000, 142000]],
            ['Daging Sapi Paha Depan', 'Rp/Kg', [142500, 142500, 142500, 142500, 142500]],
            ['Gas LPG 3kg', 'Rp/Tabung', [20000, 20000, 20000, 20000, 20000]],
            ['BBM', 'Rp/Liter', [10000, 10000, 10000, 10000, 10000]],
        ];

        foreach ($rows as $row) {
            $variant = $row[0];
            $satuan = $row[1];
            $priceList = $row[2];

            $matchedMaster = null;
            $vLower = strtolower($variant);
            foreach ($masterKomoditas as $m) {
                $mLower = strtolower($m->name);
                if ($mLower === $vLower || str_contains($vLower, $mLower) || str_contains($mLower, $vLower)) {
                    $matchedMaster = $m;
                    break;
                }
            }

            foreach ($dates as $idx => $dateStr) {
                $price = $priceList[$idx] ?? $priceList[0];

                CommodityPrice::updateOrCreate(
                    [
                        'nama_variant' => $variant,
                        'tanggal'      => $dateStr,
                    ],
                    [
                        'komoditas_id' => $matchedMaster?->id,
                        'satuan'       => $satuan,
                        'harga'        => $price,
                        'sumber'       => 'import_excel',
                        'created_by'   => $adminId,
                    ]
                );
            }
        }
    }
}
