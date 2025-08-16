<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JamPelajaranSeeder extends Seeder
{
    public function run(): void
    {
        $jam_pelajarans = [
            ['jam_ke' => 1],
            ['jam_ke' => 2],
            ['jam_ke' => 3],
            ['jam_ke' => 4],
            ['jam_ke' => 5],
            ['jam_ke' => 6],
            ['jam_ke' => 7],
            ['jam_ke' => 8],
            ['jam_ke' => 9],
            ['jam_ke' => 10],
            ['jam_ke' => 11],
            ['jam_ke' => 12],
        ];

        DB::table('jam_pelajarans')->insert($jam_pelajarans);
    }
}