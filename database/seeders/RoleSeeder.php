<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->insert([
            ['id' => 1, 'name' => 'Admin'],
            ['id' => 2, 'name' => 'Mentor'],
            ['id' => 3, 'name' => 'Peserta'],
            ['id' => 4, 'name' => 'Karyawan'],
        ]);
    }
}

