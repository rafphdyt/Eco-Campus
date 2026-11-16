<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Category;
use App\Models\Challenge;
use App\Models\Department;
use App\Models\Institution;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $inst = Institution::create([
            'name' => 'Universitas Padjadjaran',
            'type' => 'Universitas',
            'location' => 'Jatinangor',
        ]);

        $fakultas = 'Fakultas Matematika dan Ilmu Pengetahuan Alam';
        $jurusan = ['Teknik Informatika', 'Teknik Elektro', 'Matematika', 'Kimia', 'Fisika',
                    'Biologi', 'Statistika', 'Geofisika', 'Ilmu Aktuaria'];
        foreach ($jurusan as $nama) {
            Department::create([
                'institution_id' => $inst->institution_id,
                'name' => $nama,
                'faculty' => $fakultas,
            ]);
        }

        foreach (['Modul', 'Artikel', 'Berita'] as $nama) {
            Category::create(['name' => $nama]);
        }

        Challenge::create(['title' => 'Bawa tumbler', 'description' => 'Bawa tempat minum sendiri', 'points' => 20]);
        Challenge::create(['title' => 'Jalan kaki', 'description' => 'Jalan kaki ke kampus', 'points' => 50]);
        Challenge::create(['title' => 'Buang sampah', 'description' => 'Ambil sampah yang dibuang sembarangan dan buang ke tempat sampah yang sesuai', 'points' => 5]);

        Article::create(['category_id' => 3, 'title' => 'Berita perubahan iklim terbaru', 'description' => 'Kabar iklim terkini dari NASA.', 'author' => 'NASA Climate', 'url' => 'https://climate.nasa.gov/news/']);
        Article::create(['category_id' => 2, 'title' => 'Apa itu perubahan iklim?', 'description' => 'Penjelasan dasar dari PBB.', 'author' => 'United Nations', 'url' => 'https://www.un.org/en/climatechange']);
        Article::create(['category_id' => 1, 'title' => 'SDG 13: Aksi Iklim', 'description' => 'Target dan indikator SDG 13.', 'author' => 'UN SDGs', 'url' => 'https://sdgs.un.org/goals/goal13']);

        User::create(['institution_id' => 1, 'department_id' => 1, 'name' => 'Admin',
            'email' => 'admin@kampus.ac.id', 'password' => Hash::make('password'), 'role' => 'admin']);
        User::create(['institution_id' => 1, 'department_id' => 1, 'name' => 'Mahasiswa Demo',
            'npm' => '140810250001', 'email' => 'mahasiswa@kampus.ac.id',
            'password' => Hash::make('password'), 'role' => 'mahasiswa']);
    }
}