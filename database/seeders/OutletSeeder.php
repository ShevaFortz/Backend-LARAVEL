<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OutletSeeder extends Seeder
{
    public function run()
    {
        DB::table('outlets')->updateOrInsert(
            ['id' => 1], // cek apakah id 1 sudah ada
            [
                'nama' => 'Outlet Utama',
                'alamat' => 'Jl. Mawar No 1',
                'telepon' => '08123456789',
                'created_at' => now(),
                'updated_at' => now()
            ]
        );
    }
}
