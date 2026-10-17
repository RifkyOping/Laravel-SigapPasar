<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name'      => 'Pimpinan Diskoperindag Sulbar',
                'email'     => 'pimpinan@sigappasar.go.id',
                'password'  => Hash::make('password'),
                'role'      => 'pimpinan',
                'nip'       => '197501011998031001',
                'instansi'  => 'Dinas Koperasi dan UKM, Perindustrian dan Perdagangan Provinsi Sulawesi Barat',
                'is_active' => true,
            ],
            [
                'name'      => 'Admin Sigap Sulbar',
                'email'     => 'admin@sigappasar.go.id',
                'password'  => Hash::make('password'),
                'role'      => 'admin',
                'nip'       => '198205152003121002',
                'instansi'  => 'Dinas Koperasi dan UKM, Perindustrian dan Perdagangan Provinsi Sulawesi Barat',
                'is_active' => true,
            ],
            [
                'name'      => 'Budi Pengawas',
                'email'     => 'pengawas@sigappasar.go.id',
                'password'  => Hash::make('password'),
                'role'      => 'pengawas_siber',
                'nip'       => '199003102010121003',
                'instansi'  => 'Dinas Koperasi dan UKM, Perindustrian dan Perdagangan Provinsi Sulawesi Barat',
                'is_active' => true,
            ],
            [
                'name'      => 'Sari Verifikator',
                'email'     => 'verifikator@sigappasar.go.id',
                'password'  => Hash::make('password'),
                'role'      => 'verifikator',
                'nip'       => '199205202012122004',
                'instansi'  => 'Dinas Koperasi dan UKM, Perindustrian dan Perdagangan Provinsi Sulawesi Barat',
                'is_active' => true,
            ],
            [
                'name'      => 'Agus Petugas Mamuju',
                'email'     => 'petugas@sigappasar.go.id',
                'password'  => Hash::make('password'),
                'role'      => 'petugas_lapangan',
                'phone'     => '08121234567',
                'nip'       => '199407152015121005',
                'instansi'  => 'Diskoperindag Kab. Mamuju / Prov. Sulbar',
                'is_active' => true,
            ],
            [
                'name'      => 'Dewi Petugas Polman',
                'email'     => 'petugas2@sigappasar.go.id',
                'password'  => Hash::make('password'),
                'role'      => 'petugas_lapangan',
                'phone'     => '08127654321',
                'nip'       => '199509082016122006',
                'instansi'  => 'Diskoperindag Kab. Polewali Mandar / Prov. Sulbar',
                'is_active' => true,
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
