<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DestinationsSeeder extends Seeder
{
    public function run()
    {
        $json = file_get_contents(database_path('seeders/destinations.json'));
        $data = json_decode($json, true);
        $now = Carbon::now();

        $categories = [
            8 => 'bukit',
            7 => 'gunung',
            6 => 'sungai',
            5 => 'danau',
            4 => 'hutan',
            3 => 'waduk',
            2 => 'rawa',
            1 => 'pulau',
        ];

        foreach ($data as $item) {
            $id = (string) Str::uuid();

            $category_id = null;
            $lower = strtolower($item['Nama']);
            foreach ($categories as $cid => $keyword) {
                if (str_contains($lower, $keyword)) {
                    $category_id = $cid;
                    break;
                }
            }

            DB::table('destinations')->insert([
                'id' => $id,
                'name' => $item['Nama'] ?? fake()->company(),
                'thumb_image' => Str::limit($item['Image_URL'] ?? fake()->imageUrl(), 250),
                'imagekit_file_id' => null,
                'content' => '',
                'facility' => '',
                'lat' => empty($item['Latitude']) ? 0.0 : floatval(str_replace(',', '.', $item['Latitude'])),
                'lon' => empty($item['Longitude']) ? 0.0 : floatval(str_replace(',', '.', $item['Longitude'])),
                'address' => $item['Tempat'] ?? fake()->address(),
                'operating_hours' => null,
                'pic_id' => 1,
                'published' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            if ($category_id) {
                DB::table('destination_category')->insert([
                    'destination_id' => $id,
                    'category_id' => $category_id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            if (!empty(trim($item['Review'] ?? ''))) {
                DB::table('testimonials')->insert([
                    'destination_id' => $id,
                    'name' => fake()->name(),
                    'comment' => Str::limit($item['Review'], 250),
                    'rating' => intval(round(str_replace(',', '.', floatval(str_replace(',', '.', $item['Rating'])) ?? fake()->numberBetween(1, 5)))),
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }
}
