<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        $categories = collect([
            ['name' => 'Fiksi', 'slug' => 'fiksi', 'dewey' => '813'],
            ['name' => 'Sains', 'slug' => 'sains', 'dewey' => '500'],
            ['name' => 'Sejarah', 'slug' => 'sejarah', 'dewey' => '900'],
            ['name' => 'Teknologi', 'slug' => 'teknologi', 'dewey' => '004'],
            ['name' => 'Anak', 'slug' => 'anak', 'dewey' => '028'],
        ])->mapWithKeys(function (array $item) {
            $category = Category::query()->firstOrCreate(
                ['slug' => $item['slug']],
                ['name' => $item['name']],
            );

            return [$item['slug'] => ['model' => $category, 'dewey' => $item['dewey']]];
        });

        $authors = collect([
            ['name' => 'Pramoedya Ananta Toer', 'bio' => 'Sastrawan Indonesia; karya tetralogi Buru terkenal secara internasional.'],
            ['name' => 'Andrea Hirata', 'bio' => 'Penulis Laskar Pelangi dan sejumlah novel dengan latar Belitung.'],
            ['name' => 'Tere Liye', 'bio' => 'Penulis produktif novel populer Indonesia lintas usia pembaca.'],
            ['name' => 'Dee Lestari', 'bio' => 'Penulis dan musisi; dikenal lewat serial Supernova.'],
            ['name' => 'Eka Kurniawan', 'bio' => 'Penulis Cantik Itu Luka; gaya narratif yang gelap dan puitis.'],
            ['name' => 'Carl Sagan', 'bio' => 'Astronom dan penulis sains populer; pembawa acara Cosmos.'],
            ['name' => 'Stephen Hawking', 'bio' => 'Fisikawan teoretis; penulis A Brief History of Time.'],
            ['name' => 'Yuval Noah Harari', 'bio' => 'Sejarawan; penulis Sapiens dan Homo Deus.'],
            ['name' => 'Walter Isaacson', 'bio' => 'Penulis biografi tokoh teknologi dan sains.'],
            ['name' => 'Donald Knuth', 'bio' => 'Ilmuwan komputer; penulis The Art of Computer Programming.'],
            ['name' => 'Robert C. Martin', 'bio' => 'Penulis Clean Code; tokoh gerakan software craftsmanship.'],
        ])->map(fn (array $data) => Author::query()->firstOrCreate(
            ['name' => $data['name']],
            ['bio' => $data['bio']],
        ));

        $books = [
            [
                'title' => 'Bumi Manusia',
                'author' => 'Pramoedya Ananta Toer',
                'category' => 'fiksi',
                'suffix' => 'TOE',
                'format' => 'keduanya',
                'stock' => 3,
                'year' => 1980,
                'isbn' => '978-602-291-000-1',
                'synopsis' => 'Kisah Minke di awal abad ke-20: sekolah, kolonialisme, dan kesadaran nasional yang mulai tumbuh.',
            ],
            [
                'title' => 'Laskar Pelangi',
                'author' => 'Andrea Hirata',
                'category' => 'fiksi',
                'suffix' => 'HIR',
                'format' => 'fisik',
                'stock' => 5,
                'year' => 2005,
                'isbn' => '978-979-3062-79-2',
                'synopsis' => 'Sepuluh anak dari keluarga miskin di Belitung bertahan di sekolah Muhammadiyah yang hampir ditutup.',
            ],
            [
                'title' => 'Hujan',
                'author' => 'Tere Liye',
                'category' => 'fiksi',
                'suffix' => 'LIE',
                'format' => 'keduanya',
                'stock' => 2,
                'year' => 2016,
                'isbn' => '978-602-032-478-4',
                'synopsis' => 'Lana bertahan di dunia pasca-bencana, mencari makna kehilangan, harapan, dan hujan yang tak berhenti.',
            ],
            [
                'title' => 'Supernova: Ksatria, Puteri, dan Bintang Jatuh',
                'author' => 'Dee Lestari',
                'category' => 'fiksi',
                'suffix' => 'LES',
                'format' => 'digital',
                'stock' => 0,
                'year' => 2001,
                'isbn' => '978-979-3062-88-4',
                'synopsis' => 'Novel berlapis tentang cinta, sains, dan pencarian jati diri di antara tokoh-tokoh yang saling bersilangan.',
            ],
            [
                'title' => 'Cantik Itu Luka',
                'author' => 'Eka Kurniawan',
                'category' => 'fiksi',
                'suffix' => 'KUR',
                'format' => 'fisik',
                'stock' => 1,
                'year' => 2002,
                'isbn' => '978-602-291-123-8',
                'synopsis' => 'Epik magis-realis tentang Dewi Ayu dan sejarah Indonesia yang penuh luka serta ironi.',
            ],
            [
                'title' => 'Cosmos',
                'author' => 'Carl Sagan',
                'category' => 'sains',
                'suffix' => 'SAG',
                'format' => 'keduanya',
                'stock' => 2,
                'year' => 1980,
                'isbn' => '978-0-345-33135-9',
                'synopsis' => 'Perjalanan popular science dari atom hingga galaksi; mengapa kita layak merasa kagum pada alam semesta.',
            ],
            [
                'title' => 'A Brief History of Time',
                'author' => 'Stephen Hawking',
                'category' => 'sains',
                'suffix' => 'HAW',
                'format' => 'fisik',
                'stock' => 4,
                'year' => 1988,
                'isbn' => '978-0-553-38016-3',
                'synopsis' => 'Pengantar lubang hitam, big bang, dan ruang-waktu untuk pembaca non-fisikawan.',
            ],
            [
                'title' => 'The Demon-Haunted World',
                'author' => 'Carl Sagan',
                'category' => 'sains',
                'suffix' => 'SAG',
                'format' => 'digital',
                'stock' => 0,
                'year' => 1995,
                'isbn' => '978-0-345-40946-1',
                'synopsis' => 'Pembelaan terhadap pikiran ilmiah dan skeptisisme di tengah ombak takhayul modern.',
            ],
            [
                'title' => 'Sapiens: A Brief History of Humankind',
                'author' => 'Yuval Noah Harari',
                'category' => 'sejarah',
                'suffix' => 'HAR',
                'format' => 'keduanya',
                'stock' => 3,
                'year' => 2011,
                'isbn' => '978-0-06-231609-7',
                'synopsis' => 'Narasi besar tentang bagaimana Homo sapiens menaklukkan bumi lewat kisah bersama dan kerja sama massal.',
            ],
            [
                'title' => 'Homo Deus',
                'author' => 'Yuval Noah Harari',
                'category' => 'sejarah',
                'suffix' => 'HAR',
                'format' => 'fisik',
                'stock' => 2,
                'year' => 2015,
                'isbn' => '978-0-06-246431-6',
                'synopsis' => 'Spekulasi sejarah masa depan: apa yang terjadi setelah manusia mengalahkan kelaparan, wabah, dan perang.',
            ],
            [
                'title' => 'Sejarah Indonesia Modern',
                'author' => 'Pramoedya Ananta Toer',
                'category' => 'sejarah',
                'suffix' => 'TOE',
                'format' => 'fisik',
                'stock' => 0,
                'year' => 1999,
                'isbn' => '978-979-433-212-2',
                'synopsis' => 'Kumpulan esai dan catatan yang menautkan memori kolektif dengan pembentukan Indonesia modern.',
            ],
            [
                'title' => 'Steve Jobs',
                'author' => 'Walter Isaacson',
                'category' => 'teknologi',
                'suffix' => 'ISA',
                'format' => 'keduanya',
                'stock' => 2,
                'year' => 2011,
                'isbn' => '978-1-4516-4853-9',
                'synopsis' => 'Biografi berdasarkan wawancara panjang dengan Jobs dan orang-orang di sekelilingnya.',
            ],
            [
                'title' => 'The Art of Computer Programming, Vol. 1',
                'author' => 'Donald Knuth',
                'category' => 'teknologi',
                'suffix' => 'KNU',
                'format' => 'fisik',
                'stock' => 1,
                'year' => 1968,
                'isbn' => '978-0-201-89683-1',
                'synopsis' => 'Fondasi algoritma dan struktur data; rujukan klasik bagi ilmuwan komputer.',
            ],
            [
                'title' => 'The Innovators',
                'author' => 'Walter Isaacson',
                'category' => 'teknologi',
                'suffix' => 'ISA',
                'format' => 'digital',
                'stock' => 0,
                'year' => 2014,
                'isbn' => '978-1-4767-0869-0',
                'synopsis' => 'Bagaimana kolaborasi, bukan genius tunggal, membangun era digital dari Ada Lovelace hingga internet.',
            ],
            [
                'title' => 'Clean Code',
                'author' => 'Robert C. Martin',
                'category' => 'teknologi',
                'suffix' => 'MAR',
                'format' => 'keduanya',
                'stock' => 4,
                'year' => 2008,
                'isbn' => '978-0-13-235088-4',
                'synopsis' => 'Praktik menulis kode yang terbaca, dapat diuji, dan mudah dirawat oleh tim.',
            ],
            [
                'title' => 'Petualangan si Kancil',
                'author' => 'Andrea Hirata',
                'category' => 'anak',
                'suffix' => 'HIR',
                'format' => 'fisik',
                'stock' => 6,
                'year' => 2010,
                'isbn' => '978-602-03-1122-4',
                'synopsis' => 'Kumpulan cerita rakyat ringan tentang kecerdikan Kancil, cocok untuk pembaca muda.',
            ],
            [
                'title' => 'Bumi dan Teman-Temannya',
                'author' => 'Tere Liye',
                'category' => 'anak',
                'suffix' => 'LIE',
                'format' => 'keduanya',
                'stock' => 3,
                'year' => 2018,
                'isbn' => '978-602-03-4581-6',
                'synopsis' => 'Pengantar ramah anak tentang planet, bintang, dan rasa ingin tahu terhadap langit malam.',
            ],
            [
                'title' => 'Kisah Pelangi Kecil',
                'author' => 'Dee Lestari',
                'category' => 'anak',
                'suffix' => 'LES',
                'format' => 'fisik',
                'stock' => 2,
                'year' => 2015,
                'isbn' => '978-602-03-2210-0',
                'synopsis' => 'Cerita bergambar tentang persahabatan dan keberanian mencoba hal baru.',
            ],
            [
                'title' => 'Astronomi untuk Pemula',
                'author' => 'Carl Sagan',
                'category' => 'anak',
                'suffix' => 'SAG',
                'format' => 'digital',
                'stock' => 0,
                'year' => 2001,
                'isbn' => '978-0-345-40999-7',
                'synopsis' => 'Edisi ringkas Cosmos yang disederhanakan: tata surya, bintang, dan pertanyaan sederhana anak tentang langit.',
            ],
            [
                'title' => 'Perpustakaan di Ujung Kota',
                'author' => 'Eka Kurniawan',
                'category' => 'fiksi',
                'suffix' => 'KUR',
                'format' => 'fisik',
                'stock' => 0,
                'year' => 2020,
                'isbn' => '978-602-291-778-9',
                'synopsis' => 'Seorang pustakawan muda menjaga koleksi langka saat kotanya perlahan berganti wajah.',
            ],
        ];

        $authorByName = $authors->keyBy('name');

        foreach ($books as $index => $book) {
            $categoryMeta = $categories[$book['category']];
            $dewey = $categoryMeta['dewey'];
            $callNumber = sprintf('%s.%d · %s', $dewey, ($index % 9) + 1, $book['suffix']);

            Book::query()->updateOrCreate(
                ['isbn' => $book['isbn']],
                [
                    'title' => $book['title'],
                    'author_id' => $authorByName[$book['author']]->id,
                    'category_id' => $categoryMeta['model']->id,
                    'cover_image_path' => null,
                    'file_path' => in_array($book['format'], ['digital', 'keduanya'], true)
                        ? 'ebooks/'.Str::slug($book['title']).'.pdf'
                        : null,
                    'format' => $book['format'],
                    'stock' => $book['stock'],
                    'synopsis' => $book['synopsis'],
                    'published_year' => $book['year'],
                    'call_number' => $callNumber,
                ],
            );
        }
    }
}
