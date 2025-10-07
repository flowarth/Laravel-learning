<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::insert([
            [
                'name' => 'Dosen',
                'email' => 'dosen@gmail.com',
                'password' => bcrypt('password'),
                'role' => 'dosen',
                'nim_or_nip' => '123456789',
            ],
            [
                'name' => 'Mahasiswa',
                'email' => 'mahasiswa@gmail.com',
                'password' => bcrypt('password'),
                'role' => 'mahasiswa',
                'nim_or_nip' => '987654321',
            ]
        ]);
    }
}
