<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        // Get categories and authors
        $novel = Category::where('slug', 'novel')->first();
        $fiksi = Category::where('slug', 'fiksi')->first();
        $remaja = Category::where('slug', 'remaja')->first();
        $dewasa = Category::where('slug', 'dewasa')->first();
        $motivasi = Category::where('slug', 'motivasi')->first();
        $romance = Category::where('slug', 'romance')->first();
        $fantasi = Category::where('slug', 'fantasi')->first();

        $tereLiye = Author::where('name', 'Tere Liye')->first();
        $andreaHirata = Author::where('name', 'Andrea Hirata')->first();
        $deeLestari = Author::where('name', 'Dee Lestari')->first();
        $radityaDika = Author::where('name', 'Raditya Dika')->first();
        $pidiBaiq = Author::where('name', 'Pidi Baiq')->first();
        $ikaNatassa = Author::where('name', 'Ika Natassa')->first();
        $elShirazy = Author::where('name', 'Habiburrahman El Shirazy')->first();
        $pram = Author::where('name', 'Pramoedya Ananta Toer')->first();
        $fiersaBesari = Author::where('name', 'Fiersa Besari')->first();
        $boyCandra = Author::where('name', 'Boy Candra')->first();

        $books = [
            // Tere Liye Books
            [
                'title' => 'Bumi',
                'author_id' => $tereLiye->id,
                'category_id' => $fantasi->id,
                'isbn' => '9786020822181',
                'price_cents' => 9500000, // Rp 95,000
                'discount_price_cents' => 8000000, // Rp 80,000
                'stock' => 50,
                'description' => 'Bumi adalah novel pertama dalam serial Bumi yang bercerita tentang petualangan Raib, seorang remaja yang memiliki kekuatan menghilang. Dia bersama teman-temannya Ali dan Seli memulai petualangan yang menakjubkan di dunia paralel.',
                'pages' => 440,
                'language' => 'Indonesia',
                'publisher' => 'Gramedia Pustaka Utama',
                'published_at' => '2014-01-01',
                'rating_avg' => 4.5,
            ],
            [
                'title' => 'Bulan',
                'author_id' => $tereLiye->id,
                'category_id' => $fantasi->id,
                'isbn' => '9786020823478',
                'price_cents' => 9900000,
                'discount_price_cents' => 8500000,
                'stock' => 45,
                'description' => 'Kelanjutan dari serial Bumi. Raib, Ali, dan Seli melanjutkan petualangan mereka menghadapi ancaman yang lebih besar dari klan Bulan.',
                'pages' => 400,
                'language' => 'Indonesia',
                'publisher' => 'Gramedia Pustaka Utama',
                'published_at' => '2015-08-01',
                'rating_avg' => 4.6,
            ],
            [
                'title' => 'Pulang',
                'author_id' => $tereLiye->id,
                'category_id' => $novel->id,
                'isbn' => '9786020331560',
                'price_cents' => 8900000,
                'stock' => 30,
                'description' => 'Novel tentang perjalanan seorang anak muda yang mencari jati diri dan makna pulang yang sebenarnya. Kisah yang menyentuh tentang keluarga dan pengorbanan.',
                'pages' => 370,
                'language' => 'Indonesia',
                'publisher' => 'Republika Penerbit',
                'published_at' => '2015-03-01',
                'rating_avg' => 4.7,
            ],
            [
                'title' => 'Hujan',
                'author_id' => $tereLiye->id,
                'category_id' => $novel->id,
                'isbn' => '9786020495552',
                'price_cents' => 9200000,
                'stock' => 40,
                'description' => 'Kisah tentang Lail, seorang perempuan dengan masa lalu yang kelam, dan Esok, seorang lelaki yang mencoba memahaminya. Sebuah novel tentang cinta, pengampunan, dan harapan.',
                'pages' => 320,
                'language' => 'Indonesia',
                'publisher' => 'Gramedia Pustaka Utama',
                'published_at' => '2016-10-01',
                'rating_avg' => 4.8,
            ],

            // Andrea Hirata Books
            [
                'title' => 'Laskar Pelangi',
                'author_id' => $andreaHirata->id,
                'category_id' => $novel->id,
                'isbn' => '9789793062792',
                'price_cents' => 7500000,
                'discount_price_cents' => 6500000,
                'stock' => 100,
                'description' => 'Novel yang mengisahkan kehidupan 10 anak dari keluarga miskin di Belitung yang bersekolah di SD Muhammadiyah. Kisah inspiratif tentang pendidikan, persahabatan, dan perjuangan.',
                'pages' => 529,
                'language' => 'Indonesia',
                'publisher' => 'Bentang Pustaka',
                'published_at' => '2005-09-01',
                'rating_avg' => 4.9,
            ],
            [
                'title' => 'Sang Pemimpi',
                'author_id' => $andreaHirata->id,
                'category_id' => $novel->id,
                'isbn' => '9789793062808',
                'price_cents' => 7800000,
                'stock' => 60,
                'description' => 'Sekuel Laskar Pelangi yang mengisahkan perjalanan Ikal, Arai, dan Jimbron dalam mengejar mimpi mereka untuk kuliah di Prancis.',
                'pages' => 304,
                'language' => 'Indonesia',
                'publisher' => 'Bentang Pustaka',
                'published_at' => '2006-09-01',
                'rating_avg' => 4.7,
            ],
            [
                'title' => 'Edensor',
                'author_id' => $andreaHirata->id,
                'category_id' => $novel->id,
                'isbn' => '9789793062815',
                'price_cents' => 7900000,
                'stock' => 55,
                'description' => 'Novel ketiga dari tetralogi Laskar Pelangi yang menceritakan petualangan Ikal di Eropa dalam mengejar impiannya.',
                'pages' => 336,
                'language' => 'Indonesia',
                'publisher' => 'Bentang Pustaka',
                'published_at' => '2007-12-01',
                'rating_avg' => 4.6,
            ],

            // Dee Lestari Books
            [
                'title' => 'Supernova: Ksatria, Puteri, dan Bintang Jatuh',
                'author_id' => $deeLestari->id,
                'category_id' => $dewasa->id,
                'isbn' => '9789793062693',
                'price_cents' => 8500000,
                'stock' => 35,
                'description' => 'Novel pertama dari seri Supernova yang mengisahkan tentang pencarian jati diri, cinta, dan filosofi kehidupan melalui sudut pandang yang unik.',
                'pages' => 326,
                'language' => 'Indonesia',
                'publisher' => 'Bentang Pustaka',
                'published_at' => '2001-12-01',
                'rating_avg' => 4.5,
            ],
            [
                'title' => 'Perahu Kertas',
                'author_id' => $deeLestari->id,
                'category_id' => $romance->id,
                'isbn' => '9789793062822',
                'price_cents' => 8800000,
                'discount_price_cents' => 7500000,
                'stock' => 70,
                'description' => 'Kisah cinta Kugy dan Keenan yang penuh lika-liku. Novel tentang mimpi, cinta, dan pilihan hidup.',
                'pages' => 456,
                'language' => 'Indonesia',
                'publisher' => 'Bentang Pustaka',
                'published_at' => '2009-05-01',
                'rating_avg' => 4.8,
            ],

            // Raditya Dika Books
            [
                'title' => 'Kambing Jantan: Sebuah Catatan Harian Pelajar Bodoh',
                'author_id' => $radityaDika->id,
                'category_id' => $novel->id,
                'isbn' => '9789797803988',
                'price_cents' => 5500000,
                'stock' => 80,
                'description' => 'Buku komedi yang berisi cerita-cerita lucu dari masa sekolah dan kuliah Raditya Dika. Buku ini menjadi fenomena dan sangat populer di kalangan anak muda.',
                'pages' => 176,
                'language' => 'Indonesia',
                'publisher' => 'Gagas Media',
                'published_at' => '2005-06-01',
                'rating_avg' => 4.4,
            ],
            [
                'title' => 'Cinta Brontosaurus',
                'author_id' => $radityaDika->id,
                'category_id' => $novel->id,
                'isbn' => '9789797803995',
                'price_cents' => 5800000,
                'stock' => 65,
                'description' => 'Kumpulan cerita tentang pengalaman Raditya Dika dalam urusan cinta dengan gaya bercerita yang kocak dan menghibur.',
                'pages' => 200,
                'language' => 'Indonesia',
                'publisher' => 'Gagas Media',
                'published_at' => '2006-08-01',
                'rating_avg' => 4.3,
            ],

            // Pidi Baiq Books
            [
                'title' => 'Dilan: Dia adalah Dilanku Tahun 1990',
                'author_id' => $pidiBaiq->id,
                'category_id' => $romance->id,
                'isbn' => '9786021866238',
                'price_cents' => 7200000,
                'discount_price_cents' => 6000000,
                'stock' => 120,
                'description' => 'Novel romansa yang mengisahkan kisah cinta Dilan dan Milea di tahun 1990. Kisah yang manis dan nostalgik tentang cinta remaja.',
                'pages' => 332,
                'language' => 'Indonesia',
                'publisher' => 'Pastel Books',
                'published_at' => '2014-07-01',
                'rating_avg' => 4.7,
            ],
            [
                'title' => 'Dilan: Dia adalah Dilanku Tahun 1991',
                'author_id' => $pidiBaiq->id,
                'category_id' => $romance->id,
                'isbn' => '9786021866245',
                'price_cents' => 7500000,
                'stock' => 110,
                'description' => 'Kelanjutan kisah cinta Dilan dan Milea yang semakin rumit dan penuh drama di tahun 1991.',
                'pages' => 344,
                'language' => 'Indonesia',
                'publisher' => 'Pastel Books',
                'published_at' => '2015-02-01',
                'rating_avg' => 4.6,
            ],

            // Ika Natassa Books
            [
                'title' => 'Critical Eleven',
                'author_id' => $ikaNatassa->id,
                'category_id' => $romance->id,
                'isbn' => '9786020318196',
                'price_cents' => 8000000,
                'stock' => 75,
                'description' => 'Novel tentang Ale dan Anya yang bertemu dalam penerbangan menuju Sydney. Kisah cinta yang dimulai dari 11 menit yang menentukan.',
                'pages' => 296,
                'language' => 'Indonesia',
                'publisher' => 'Gramedia Pustaka Utama',
                'published_at' => '2015-06-01',
                'rating_avg' => 4.5,
            ],
            [
                'title' => 'Antologi Rasa',
                'author_id' => $ikaNatassa->id,
                'category_id' => $romance->id,
                'isbn' => '9786020324548',
                'price_cents' => 7800000,
                'stock' => 60,
                'description' => 'Kumpulan cerita pendek tentang cinta dalam berbagai bentuknya. Setiap cerita menghadirkan rasa yang berbeda.',
                'pages' => 280,
                'language' => 'Indonesia',
                'publisher' => 'Gramedia Pustaka Utama',
                'published_at' => '2016-02-01',
                'rating_avg' => 4.4,
            ],

            // Habiburrahman El Shirazy Books
            [
                'title' => 'Ayat-Ayat Cinta',
                'author_id' => $elShirazy->id,
                'category_id' => $romance->id,
                'isbn' => '9789793062778',
                'price_cents' => 7000000,
                'discount_price_cents' => 6000000,
                'stock' => 90,
                'description' => 'Novel religi yang mengisahkan kehidupan Fahri, seorang mahasiswa Indonesia di Al-Azhar, Mesir. Sebuah kisah cinta yang islami dan inspiratif.',
                'pages' => 419,
                'language' => 'Indonesia',
                'publisher' => 'Republika Penerbit',
                'published_at' => '2004-12-01',
                'rating_avg' => 4.6,
            ],
            [
                'title' => 'Ayat-Ayat Cinta 2',
                'author_id' => $elShirazy->id,
                'category_id' => $romance->id,
                'isbn' => '9786021318744',
                'price_cents' => 8500000,
                'stock' => 55,
                'description' => 'Sekuel dari Ayat-Ayat Cinta yang mengisahkan kehidupan Fahri di Edinburgh, Skotlandia dengan berbagai cobaan baru.',
                'pages' => 512,
                'language' => 'Indonesia',
                'publisher' => 'Republika Penerbit',
                'published_at' => '2015-12-01',
                'rating_avg' => 4.5,
            ],

            // Pramoedya Ananta Toer Books
            [
                'title' => 'Bumi Manusia',
                'author_id' => $pram->id,
                'category_id' => $novel->id,
                'isbn' => '9789799101129',
                'price_cents' => 9500000,
                'stock' => 40,
                'description' => 'Novel pertama dari Tetralogi Pulau Buru yang mengisahkan kehidupan Minke, seorang pribumi Jawa di era kolonial Belanda. Karya sastra masterpiece Indonesia.',
                'pages' => 535,
                'language' => 'Indonesia',
                'publisher' => 'Hasta Mitra',
                'published_at' => '1980-01-01',
                'rating_avg' => 4.9,
            ],
            [
                'title' => 'Anak Semua Bangsa',
                'author_id' => $pram->id,
                'category_id' => $novel->id,
                'isbn' => '9789799101136',
                'price_cents' => 9500000,
                'stock' => 35,
                'description' => 'Novel kedua dari Tetralogi Pulau Buru yang melanjutkan kisah perjuangan Minke melawan kolonialisme.',
                'pages' => 512,
                'language' => 'Indonesia',
                'publisher' => 'Hasta Mitra',
                'published_at' => '1980-01-01',
                'rating_avg' => 4.8,
            ],

            // Fiersa Besari Books
            [
                'title' => 'Garis Waktu',
                'author_id' => $fiersaBesari->id,
                'category_id' => $novel->id,
                'isbn' => '9786027870055',
                'price_cents' => 6500000,
                'stock' => 85,
                'description' => 'Novel tentang perjalanan hidup yang penuh filosofi. Mengisahkan pencarian makna dalam setiap momen kehidupan.',
                'pages' => 256,
                'language' => 'Indonesia',
                'publisher' => 'Media Kita',
                'published_at' => '2016-04-01',
                'rating_avg' => 4.3,
            ],
            [
                'title' => 'Arah Langkah',
                'author_id' => $fiersaBesari->id,
                'category_id' => $motivasi->id,
                'isbn' => '9786027870062',
                'price_cents' => 6800000,
                'stock' => 70,
                'description' => 'Kumpulan kata-kata motivasi dan cerita inspiratif tentang perjalanan, mimpi, dan kehidupan.',
                'pages' => 232,
                'language' => 'Indonesia',
                'publisher' => 'Media Kita',
                'published_at' => '2017-05-01',
                'rating_avg' => 4.4,
            ],

            // Boy Candra Books
            [
                'title' => 'Senja, Hujan dan Cerita yang Telah Usai',
                'author_id' => $boyCandra->id,
                'category_id' => $romance->id,
                'isbn' => '9786027870079',
                'price_cents' => 5900000,
                'stock' => 95,
                'description' => 'Kumpulan puisi dan prosa tentang cinta, kenangan, dan perpisahan yang ditulis dengan gaya romantis dan melankolis.',
                'pages' => 176,
                'language' => 'Indonesia',
                'publisher' => 'Media Kita',
                'published_at' => '2015-11-01',
                'rating_avg' => 4.2,
            ],
            [
                'title' => 'Catatan Pendek untuk Cinta yang Panjang',
                'author_id' => $boyCandra->id,
                'category_id' => $romance->id,
                'isbn' => '9786027870086',
                'price_cents' => 6200000,
                'stock' => 80,
                'description' => 'Kumpulan catatan singkat tentang cinta yang indah dan menyentuh hati. Cocok untuk para pencinta puisi romantis.',
                'pages' => 192,
                'language' => 'Indonesia',
                'publisher' => 'Media Kita',
                'published_at' => '2016-08-01',
                'rating_avg' => 4.3,
            ],
        ];

        foreach ($books as $book) {
            Book::create([
                'id' => Str::uuid(),
                'title' => $book['title'],
                'slug' => Str::slug($book['title']),
                'author_id' => $book['author_id'],
                'category_id' => $book['category_id'],
                'isbn' => $book['isbn'],
                'price_cents' => $book['price_cents'],
                'discount_price_cents' => $book['discount_price_cents'] ?? null,
                'stock' => $book['stock'],
                'cover_url' => 'https://via.placeholder.com/400x600?text=' . urlencode($book['title']),
                'description' => $book['description'],
                'pages' => $book['pages'],
                'language' => $book['language'],
                'publisher' => $book['publisher'],
                'published_at' => $book['published_at'],
                'is_active' => true,
                'rating_avg' => $book['rating_avg'],
            ]);
        }
    }
}
