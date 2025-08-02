<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Database\Seeder;

class SiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus data lama untuk menghindari duplikat
        Siswa::query()->delete();

        $namaSiswaList = [
            'Budi Santoso', 'Agus Setiawan', 'Eko Prasetyo', 'Joko Susilo', 'Slamet Riyadi', 'Andi Wijaya', 'Rizki Maulana', 'Fajar Nugroho', 'Dedi Kurniawan', 'Ahmad Fauzi',
            'Muhammad Yusuf', 'Hendro Siswanto', 'Toni Saputra', 'Irfan Hakim', 'Doni Hidayat', 'Rian Ardianto', 'Bayu Prakoso', 'Galih Purnama', 'Heru Purnomo', 'Indra Gunawan',
            'Wahyu Ramadhan', 'Aditya Pratama', 'Candra Gupta', 'Dian Permana', 'Farhan Abdullah', 'Gilang Ramadhan', 'Hadi Santoso', 'Imam Prasetyo', 'Jamaludin', 'Krisna Murti',
            'Leo Wijaya', 'Maulana Malik', 'Nanda Kusuma', 'Oscar Maulana', 'Panji Asmoro', 'Qodir Jaelani', 'Rahmat Hidayat', 'Samsul Arifin', 'Taufik Hidayat', 'Umar Said',
            'Vino Bastian', 'Wawan Hendrawan', 'Yoga Pratama', 'Zainal Abidin', 'Arif Rahman', 'Bambang Pamungkas', 'Cecep Arif', 'Dodo Rosada', 'Edi Santoso', 'Febri Haryadi',
            'Guntur Alam', 'Hasan Basri', 'Iwan Fals', 'Jajang Mulyana', 'Kurniawan Dwi', 'Lutfi Hakim', 'Maman Abdurrahman', 'Nanang Iskandar', 'Oki Setiawan', 'Purnomo',
            'Qomarudin', 'Rudi Hartono', 'Sugeng Raharjo', 'Toto Riyanto', 'Uus Suhendar', 'Vicky Prasetyo', 'Wahyudi', 'Yanto Basna', 'Zulfikar', 'Ananda Putra',
            'Brian Wicaksono', 'Cahyo Kumolo', 'Dimas Anggara', 'Endra Setyawan', 'Fandi Eko', 'Gede Sukadana', 'Hendra Bayauw', 'Ilham Udin', 'Jefri Nichol', 'Kevin Sanjaya',
            'Lukman Sardi', 'Miftahul Hamdi', 'Novri Setiawan', 'Osvaldo Haay', 'Pratama Arhan', 'Rendy Juliansyah', 'Septian David', 'Teuku Wisnu', 'Utomo', 'Valdo',
            'Witan Sulaeman', 'Yabes Roni', 'Zico', 'Aldi Taher', 'Bachrul Alam', 'Chairil Anwar', 'Darius Sinathrya', 'Evan Dimas', 'Fachrudin Aryanto', 'Gading Marten',

            'Siti Aminah', 'Dewi Lestari', 'Sri Wahyuni', 'Indah Permatasari', 'Rina Marlina', 'Putri Ayu', 'Wulan Sari', 'Nurul Hidayah', 'Fitriani', 'Eka Putri',
            'Anisa Rahma', 'Bella Graceva', 'Citra Kirana', 'Dian Sastro', 'Eva Celia', 'Fatin Shidqia', 'Gita Gutawa', 'Hana Malasan', 'Isyana Sarasvati', 'Jessica Mila',
            'Kartika Putri', 'Laudya Cynthia', 'Maudy Ayunda', 'Nagita Slavina', 'Olivia Jensen', 'Pevita Pearce', 'Raisa Andriana', 'Sandra Dewi', 'Tara Basro', 'Ussy Sulistiawaty',
            'Vanesha Prescilla', 'Widyawati', 'Yuki Kato', 'Zaskia Adya', 'Acha Septriasa', 'Bunga Citra', 'Chelsea Islan', 'Dinda Hauw', 'Eriska Rein', 'Febby Rastanty',
            'Gisella Anastasia', 'Herfiza Novianti', 'Ira Wibowo', 'Julie Estelle', 'Kamila Andini', 'Lulu Tobing', 'Marshanda', 'Nia Ramadhani', 'Olla Ramlan', 'Prilly Latuconsina',
            'Ratna Galih', 'Shandy Aulia', 'Titi Kamal', 'Vanessa Angel', 'Wulan Guritno', 'Yasmine Wildblood', 'Zee Zee Shahab', 'Adhisty Zara', 'Amanda Manopo', 'Ayu Ting Ting',
            'Cathy Sharon', 'Dahlia Poland', 'Enzy Storia', 'Franda', 'Gracia Indri', 'Happy Salma', 'Irish Bella', 'Jihan Fahira', 'Kinaryosih', 'Luna Maya',
            'Marsha Aruan', 'Nabila Syakieb', 'Nikita Willy', 'Poppy Sovia', 'Raline Shah', 'Revalina S.', 'Sabai Morscheck', 'Tantri Syalindri', 'Tyas Mirasih', 'Velove Vexia',
            'Windy Apsari', 'Yoriko Angeline', 'Zoe Abbas', 'Audi Marissa', 'Cut Meyriska', 'Donita', 'Evi Masamba', 'Felicya Angelista', 'Ghea Indrawari', 'Hayria Lontoh',
            'Inul Daratista', 'Jenita Janet', 'Keisya Levronka', 'Lyodra Ginting', 'Marion Jola', 'Novia Bachmid', 'Putri Delina', 'Rossa', 'Syahrini', 'Tiara Andini'
        ];

        // Acak urutan nama agar distribusi lebih random
        shuffle($namaSiswaList);

        $semuaKelas = Kelas::all();
        $studentCounter = 0;

        if ($semuaKelas->isEmpty()) {
            return; // Hentikan jika tidak ada kelas
        }

        foreach ($semuaKelas as $kelas) {
            for ($i = 0; $i < 20; $i++) {
                if ($studentCounter >= count($namaSiswaList)) break; // Berhenti jika nama sudah habis

                $namaSiswa = $namaSiswaList[$studentCounter];
                $jenisKelamin = ($studentCounter < 100) ? 'L' : 'P';

                // Membuat NIS unik yang hanya terdiri dari angka
                // Contoh: 25260001, 25260002, dst.
                $nisSiswa = 25260001 + $studentCounter;

                Siswa::create([
                    'kelas_id' => $kelas->id,
                    'nis' => (string)$nisSiswa, // Pastikan dikirim sebagai string
                    'nama_lengkap' => $namaSiswa,
                    'jenis_kelamin' => $jenisKelamin,
                ]);

                $studentCounter++;
            }
        }
    }
}
