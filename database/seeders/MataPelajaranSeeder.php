<?php
    
    namespace Database\Seeders;
    
    use App\Models\MataPelajaran;
    use Illuminate\Database\Seeder;
    
    class MataPelajaranSeeder extends Seeder
    {
        public function run(): void
        {
            $mapels = ['Matematika', 'Bahasa Indonesia', 'Bahasa Inggris', 'Fisika', 'Kimia', 'Biologi', 'Sejarah', 'Pendidikan Jasmani'];
    
            foreach ($mapels as $mapel) {
                MataPelajaran::firstOrCreate(['nama' => $mapel]);
            }
        }
    }
    