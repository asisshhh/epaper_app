<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Epaper;
use App\Models\EpaperPage;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
        SuperAdminSeeder::class,
    ]);
    }

    /**
     * Create sample epapers and their pages for testing.
     */
    private function createSampleEpapers(): void
    {
        $cities = ['Odisha', 'Ranchi', 'Delhi'];

        for ($i = 0; $i < 10; $i++) {
            $date = Carbon::now()->subDays($i);

            foreach ($cities as $edition) {
                $epaper = Epaper::create([
                    'title' => "{$edition} Edition - " . $date->format('d-M-Y'),
                    'publication_date' => $date,
                    'edition' => $edition,
                    'total_pages' => $totalPages = rand(8, 16),
                    'pdf_path' => "epapers/sample/{$edition}_{$date->format('Ymd')}.pdf",
                    'is_active' => true,
                ]);

                for ($page = 1; $page <= $totalPages; $page++) {
                    EpaperPage::create([
                        'epaper_id' => $epaper->id,
                        'page_number' => $page,
                        'image_path' => "epapers/sample/page_{$page}.jpg",
                        'thumbnail_path' => "thumbnails/sample/thumb_{$page}.jpg",
                    ]);
                }
            }
        }
    }
}
