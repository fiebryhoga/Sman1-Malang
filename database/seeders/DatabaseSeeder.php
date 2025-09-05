<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PermissionsSeeder::class,
            UserSeeder::class,
            PelanggaranSeeder::class,



            TahunAjaranSeeder::class,
            KelasSeeder::class,
            // SiswaSeeder::class,
            MataPelajaranSeeder::class,
            JamPelajaranSeeder::class,
        ]);
    }
}