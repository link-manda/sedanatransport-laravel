<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Vehicle;

class VehicleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil ID dari kategori yang sudah ada
        $mpvCategory = Category::where('name', 'Mobil Keluarga (MPV)')->first();
        $cityCarCategory = Category::where('name', 'City Car (Hatchback)')->first();
        $suvCategory = Category::where('name', 'SUV')->first();

        Vehicle::create([
            'category_id' => $mpvCategory->id,
            'plate_number' => 'DK 1234 AB',
            'brand' => 'Toyota',
            'model' => 'Avanza',
            'year' => '2022',
            'daily_rate' => 350000,
            'status' => 'available',
            'photo' => null,
        ]);

        Vehicle::create([
            'category_id' => $cityCarCategory->id,
            'plate_number' => 'DK 5678 CD',
            'brand' => 'Honda',
            'model' => 'Brio Satya',
            'year' => '2023',
            'daily_rate' => 250000,
            'status' => 'available',
            'photo' => null,
        ]);

        Vehicle::create([
            'category_id' => $suvCategory->id,
            'plate_number' => 'DK 9101 EF',
            'brand' => 'Mitsubishi',
            'model' => 'Pajero Sport',
            'year' => '2021',
            'daily_rate' => 800000,
            'status' => 'maintenance',
            'photo' => null,
        ]);

        Vehicle::create([
            'category_id' => $mpvCategory->id,
            'plate_number' => 'DK 1122 GH',
            'brand' => 'Suzuki',
            'model' => 'Ertiga',
            'year' => '2022',
            'daily_rate' => 325000,
            'status' => 'rented',
            'photo' => null,
        ]);
    }
}
