<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::create([
            'name' => 'Mobil Keluarga (MPV)',
            'description' => 'Multi-Purpose Vehicle, cocok untuk keluarga.'
        ]);

        Category::create([
            'name' => 'City Car (Hatchback)',
            'description' => 'Mobil kecil yang lincah untuk perkotaan.'
        ]);

        Category::create([
            'name' => 'SUV',
            'description' => 'Sport Utility Vehicle, tangguh di berbagai medan.'
        ]);

        Category::create([
            'name' => 'Mobil Mewah',
            'description' => 'Kendaraan premium dengan kenyamanan ekstra.'
        ]);
    }
}
