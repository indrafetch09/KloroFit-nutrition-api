<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NutritionLibrary;
use Illuminate\Support\Facades\File;

class NutritionLibrarySeeder extends Seeder
{
    public function run(): void
    {
        $file = database_path('seeders/data/data-nutrition-id.csv');

        if (!File::exists($file)) {
            $this->command->error("File not found: $file");
            return;
        }

        $csv = array_map('str_getcsv', file($file));
        $header = array_map('trim', $csv[0]);
        unset($csv[0]); // Hapus baris header

        foreach ($csv as $row) {
            $data = array_combine($header, array_map('trim', $row));

            NutritionLibrary::create([
                'id'          => $data['id'],
                'name'        => $data['name'],
                'calories'    => (float) $data['calories'],
                'fat'         => (float) $data['fat'],
                'protein'     => (float) $data['proteins'],
                'carbs'       => (float) $data['carbohydrate'],
                'image'       => $data['image'],
                'is_verified' => true,
            ]);
        }

        $this->command->info('Nutrition library seeded using model!');
    }
}
