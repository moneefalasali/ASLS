<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SignAsset;

class SignAssetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $englishAlphabet = range('A', 'Z');
        $now = now();
        $inserts = [];
        foreach ($englishAlphabet as $char) {
            $inserts[] = [
                'key' => strtolower($char),
                'language' => 'en',
                'type' => 'image',
                'src' => '/storage/signs/en_' . strtolower($char) . '.png',
                'text' => $char,
                'active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        $arabicAlphabet = ['ا','ب','ت','ث','ج','ح','خ','د','ذ','ر','ز','س','ش','ص','ض','ط','ظ','ع','غ','ف','ق','ك','ل','م','ن','ه','و','ي'];
        foreach ($arabicAlphabet as $ch) {
            $key = 'ar_' . bin2hex(mb_convert_encoding($ch, 'UTF-8'));
            $inserts[] = [
                'key' => $key,
                'language' => 'ar',
                'type' => 'image',
                'src' => '/storage/signs/ar_' . urlencode($ch) . '.png',
                'text' => $ch,
                'active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        \Illuminate\Support\Facades\DB::table('sign_assets')->insert($inserts);
    }
}

