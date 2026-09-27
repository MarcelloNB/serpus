<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $books = [
            ['Laskar Pelangi', 'Andrea Hirata', 'Sastra', 5, 'Kisah sepuluh anak sekolah di Belitung yang memburu mimpi di tengah keterbatasan.'],
            ['Bumi', 'Tere Liye', 'Fiksi', 4, 'Petualangan tiga remaja menyelesaikan misteri yang menyelamatkan dunia.'],
            ['Filosofi Teras', 'Henry Manampiring', 'Non-Fiksi', 6, 'Pengantar filsafat Stoikisme untuk menghadapi stres kehidupan sehari-hari.'],
            ['Sapiens', 'Yuval Noah Harari', 'Sejarah', 3, 'Rekam jejak umat manusia dari era batu hingga era digital.'],
            ['Atomic Habits', 'James Clear', 'Non-Fiksi', 7, 'Strategi membangun kebiasaan kecil yang menghasilkan perubahan besar.'],
            ['Harry Potter dan Batu Bertuah', 'J.K. Rowling', 'Fiksi', 2, 'Seorang anak yatim menemukan takdirnya di sekolah sihir Hogwarts.'],
            ['Dongeng Anak Nusantara', 'Kolektif', 'Anak', 8, 'Kumpulan cerita rakyat dari berbagai penjuru Nusantara.'],
            ['Pemrograman Web Modern', 'Ethan Brown', 'Teknologi', 3, 'Panduan praktis membangun aplikasi web dengan JavaScript dan PHP.'],
            ['Biologi untuk Pemula', 'Tim Penulis', 'Sains', 5, 'Pengenalan sel, gen, dan ekosistem dengan bahasa yang mudah dipahami.'],
            ['Si Doel Anak Betawi', 'Motinggo Busye', 'Sastra', 0, 'Kehidupan anak Betawi di masa lampau dengan humor khasnya.'],
            ['Ekonomi Indonesia Naik Kelas', 'Darmawan Prasodjo', 'Ekonomi', 0, 'Analisis transformasi ekonomi Indonesia menuju negara maju.'],
            ['Kisah Nabi untuk Anak', 'Tim Penulis', 'Anak', 6, 'Kisah teladan para nabi yang disederhanakan untuk pembaca muda.'],
        ];

        foreach ($books as [$title, $author, $categoryName, $stock, $description]) {
            $category = Category::firstOrCreate(['name' => $categoryName]);

            Book::firstOrCreate(
                ['title' => $title],
                [
                    'category_id' => $category->id,
                    'author' => $author,
                    'description' => $description,
                    'stock' => $stock,
                ],
            );
        }
    }
}
