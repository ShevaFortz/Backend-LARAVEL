<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'id_outlet' => '1',
                'nama' => 'Admin',
                'username' => 'admin',
                'email' => 'admin@gmail.com',
                'password' => '123',
                'role' => 'admin',
                'foto' => 'Admin.jpg'
            ],
            [
                'id_outlet' => '1',
                'nama' => 'KrisnaAdmin',
                'username' => 'Krisna',
                'email' => 'krisna@gmail.com',
                'password' => '123',
                'role' => 'admin',
                'foto' => 'Admin.jpg'
            ],
        ];

        foreach ($users as $data) {
            User::updateOrCreate(
                ['email' => $data['email']], // cek berdasarkan email
                [
                    'id_outlet' => $data['id_outlet'],
                    'nama' => $data['nama'],
                    'username' => $data['username'],
                    'password' => Hash::make($data['password']),
                    'role' => $data['role'],
                    'foto' => $data['foto']
                ]
            );
        }
    }
}
