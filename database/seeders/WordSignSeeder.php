<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SignAsset;

class WordSignSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $words = [
            [
                'key' => 'شكرا',
                'language' => 'ar',
                'type' => 'image',
                'src' => 'signs/words/shukran.png',
                'text' => 'شكرا',
                'active' => true,
                'category' => 'words',
                'description' => 'شكرا - كلمة شائعة',
                'usage_count' => 0,
            ],
            [
                'key' => 'مرحبا',
                'language' => 'ar',
                'type' => 'image',
                'src' => 'signs/words/marhaba.png',
                'text' => 'مرحبا',
                'active' => true,
                'category' => 'words',
                'description' => 'مرحبا - تحية',
                'usage_count' => 0,
            ],
            [
                'key' => 'نعم',
                'language' => 'ar',
                'type' => 'image',
                'src' => 'signs/words/naam.png',
                'text' => 'نعم',
                'active' => true,
                'category' => 'words',
                'description' => 'نعم - إجابة إيجابية',
                'usage_count' => 0,
            ],
            [
                'key' => 'لا',
                'language' => 'ar',
                'type' => 'image',
                'src' => 'signs/words/laa.png',
                'text' => 'لا',
                'active' => true,
                'category' => 'words',
                'description' => 'لا - إجابة سلبية',
                'usage_count' => 0,
            ],
        ];

        $now = now();
        foreach ($words as $w) {
            // Only insert fields that exist in the current `sign_assets` table schema
            $data = [
                'key' => $w['key'],
                'language' => $w['language'],
                'type' => $w['type'],
                'src' => '/storage/' . ltrim($w['src'], '/'),
                'text' => $w['text'] ?? $w['key'],
                'active' => $w['active'] ?? true,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            \Illuminate\Support\Facades\DB::table('sign_assets')
                ->updateOrInsert(
                    ['language' => $w['language'], 'key' => $w['key']],
                    $data
                );
        }
    }
}
