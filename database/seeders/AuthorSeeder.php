<?php

namespace Database\Seeders;

use App\Models\Author;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AuthorSeeder extends Seeder
{
    public function run(): void
    {
        $authors = [
            [
                'name' => 'Tere Liye',
                'bio' => 'Tere Liye adalah penulis novel Indonesia yang sangat produktif. Nama aslinya adalah Darwis. Ia telah menulis lebih dari 30 judul buku yang sebagian besar merupakan novel fiksi.',
            ],
            [
                'name' => 'Andrea Hirata',
                'bio' => 'Andrea Hirata adalah novelis Indonesia yang terkenal dengan novel Laskar Pelangi. Ia lahir di Belitung dan karyanya banyak terinspirasi dari kehidupan di pulau tersebut.',
            ],
            [
                'name' => 'Dee Lestari',
                'bio' => 'Dewi Lestari Simangunsong atau yang lebih dikenal sebagai Dee adalah penyanyi, penulis lagu, dan novelis Indonesia. Novel Supernova-nya sangat terkenal di Indonesia.',
            ],
            [
                'name' => 'Raditya Dika',
                'bio' => 'Raditya Dika adalah penulis, komedian, sutradara, dan aktor Indonesia. Bukunya yang pertama, Kambing Jantan, merupakan salah satu buku komedi terlaris di Indonesia.',
            ],
            [
                'name' => 'Pidi Baiq',
                'bio' => 'Pidi Baiq adalah musisi, penulis, dan sutradara Indonesia. Ia dikenal melalui novelnya Dilan yang kemudian diangkat menjadi film.',
            ],
            [
                'name' => 'Ika Natassa',
                'bio' => 'Ika Natassa adalah penulis Indonesia yang menulis novel-novel romance dengan latar dunia perbankan dan bisnis.',
            ],
            [
                'name' => 'Habiburrahman El Shirazy',
                'bio' => 'Habiburrahman El Shirazy adalah novelis Indonesia yang terkenal dengan novel religi. Novel Ayat-Ayat Cinta adalah salah satu karyanya yang paling terkenal.',
            ],
            [
                'name' => 'Pramoedya Ananta Toer',
                'bio' => 'Pramoedya Ananta Toer adalah salah satu sastrawan Indonesia terbesar. Tetralogi Pulau Buru adalah karya masterpiece-nya.',
            ],
            [
                'name' => 'Fiersa Besari',
                'bio' => 'Fiersa Besari adalah musisi dan penulis Indonesia. Karyanya banyak berisi tentang perjalanan, cinta, dan filosofi hidup.',
            ],
            [
                'name' => 'Boy Candra',
                'bio' => 'Boy Candra adalah penulis muda Indonesia yang dikenal dengan puisi-puisi romantisnya. Bukunya Senja, Hujan dan Cerita yang Telah Usai sangat populer.',
            ],
        ];

        foreach ($authors as $author) {
            Author::create([
                'id' => Str::uuid(),
                'name' => $author['name'],
                'bio' => $author['bio'],
            ]);
        }
    }
}
