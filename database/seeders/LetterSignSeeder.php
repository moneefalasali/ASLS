<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LetterSignSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $signs = [
            // Arabic Letters
            ['key' => 'أ', 'type' => 'image', 'language' => 'ar', 'src' => 'signs/letters/ar_alif.png', 'text' => 'أ'],
            ['key' => 'ب', 'type' => 'image', 'language' => 'ar', 'src' => 'signs/letters/ar_baa.png', 'text' => 'ب'],
            ['key' => 'ت', 'type' => 'image', 'language' => 'ar', 'src' => 'signs/letters/ar_taa.png', 'text' => 'ت'],
            ['key' => 'ث', 'type' => 'image', 'language' => 'ar', 'src' => 'signs/letters/ar_thaa.png', 'text' => 'ث'],
            ['key' => 'ج', 'type' => 'image', 'language' => 'ar', 'src' => 'signs/letters/ar_jeem.png', 'text' => 'ج'],
            ['key' => 'ح', 'type' => 'image', 'language' => 'ar', 'src' => 'signs/letters/ar_haa.png', 'text' => 'ح'],
            ['key' => 'خ', 'type' => 'image', 'language' => 'ar', 'src' => 'signs/letters/ar_khaa.png', 'text' => 'خ'],
            ['key' => 'د', 'type' => 'image', 'language' => 'ar', 'src' => 'signs/letters/ar_daal.png', 'text' => 'د'],
            ['key' => 'ذ', 'type' => 'image', 'language' => 'ar', 'src' => 'signs/letters/ar_thaal.png', 'text' => 'ذ'],
            ['key' => 'ر', 'type' => 'image', 'language' => 'ar', 'src' => 'signs/letters/ar_raa.png', 'text' => 'ر'],
            ['key' => 'ز', 'type' => 'image', 'language' => 'ar', 'src' => 'signs/letters/ar_zaay.png', 'text' => 'ز'],
            ['key' => 'س', 'type' => 'image', 'language' => 'ar', 'src' => 'signs/letters/ar_seen.png', 'text' => 'س'],
            ['key' => 'ش', 'type' => 'image', 'language' => 'ar', 'src' => 'signs/letters/ar_sheen.png', 'text' => 'ش'],
            ['key' => 'ص', 'type' => 'image', 'language' => 'ar', 'src' => 'signs/letters/ar_saad.png', 'text' => 'ص'],
            ['key' => 'ض', 'type' => 'image', 'language' => 'ar', 'src' => 'signs/letters/ar_daad.png', 'text' => 'ض'],
            ['key' => 'ط', 'type' => 'image', 'language' => 'ar', 'src' => 'signs/letters/ar_taa_h.png', 'text' => 'ط'],
            ['key' => 'ظ', 'type' => 'image', 'language' => 'ar', 'src' => 'signs/letters/ar_zaa.png', 'text' => 'ظ'],
            ['key' => 'ع', 'type' => 'image', 'language' => 'ar', 'src' => 'signs/letters/ar_ayn.png', 'text' => 'ع'],
            ['key' => 'غ', 'type' => 'image', 'language' => 'ar', 'src' => 'signs/letters/ar_ghayn.png', 'text' => 'غ'],
            ['key' => 'ف', 'type' => 'image', 'language' => 'ar', 'src' => 'signs/letters/ar_faa.png', 'text' => 'ف'],
            ['key' => 'ق', 'type' => 'image', 'language' => 'ar', 'src' => 'signs/letters/ar_qaaf.png', 'text' => 'ق'],
            ['key' => 'ك', 'type' => 'image', 'language' => 'ar', 'src' => 'signs/letters/ar_kaaf.png', 'text' => 'ك'],
            ['key' => 'ل', 'type' => 'image', 'language' => 'ar', 'src' => 'signs/letters/ar_laam.png', 'text' => 'ل'],
            ['key' => 'م', 'type' => 'image', 'language' => 'ar', 'src' => 'signs/letters/ar_meem.png', 'text' => 'م'],
            ['key' => 'ن', 'type' => 'image', 'language' => 'ar', 'src' => 'signs/letters/ar_noon.png', 'text' => 'ن'],
            ['key' => 'ه', 'type' => 'image', 'language' => 'ar', 'src' => 'signs/letters/ar_haa_w.png', 'text' => 'ه'],
            ['key' => 'و', 'type' => 'image', 'language' => 'ar', 'src' => 'signs/letters/ar_waaw.png', 'text' => 'و'],
            ['key' => 'ي', 'type' => 'image', 'language' => 'ar', 'src' => 'signs/letters/ar_yaa.png', 'text' => 'ي'],

            // English Letters
            ['key' => 'A', 'type' => 'image', 'language' => 'en', 'src' => 'signs/letters/en_a.png', 'text' => 'A'],
            ['key' => 'B', 'type' => 'image', 'language' => 'en', 'src' => 'signs/letters/en_b.png', 'text' => 'B'],
            ['key' => 'C', 'type' => 'image', 'language' => 'en', 'src' => 'signs/letters/en_c.png', 'text' => 'C'],
            ['key' => 'D', 'type' => 'image', 'language' => 'en', 'src' => 'signs/letters/en_d.png', 'text' => 'D'],
            ['key' => 'E', 'type' => 'image', 'language' => 'en', 'src' => 'signs/letters/en_e.png', 'text' => 'E'],
            ['key' => 'F', 'type' => 'image', 'language' => 'en', 'src' => 'signs/letters/en_f.png', 'text' => 'F'],
            ['key' => 'G', 'type' => 'image', 'language' => 'en', 'src' => 'signs/letters/en_g.png', 'text' => 'G'],
            ['key' => 'H', 'type' => 'image', 'language' => 'en', 'src' => 'signs/letters/en_h.png', 'text' => 'H'],
            ['key' => 'I', 'type' => 'image', 'language' => 'en', 'src' => 'signs/letters/en_i.png', 'text' => 'I'],
            ['key' => 'J', 'type' => 'image', 'language' => 'en', 'src' => 'signs/letters/en_j.png', 'text' => 'J'],
            ['key' => 'K', 'type' => 'image', 'language' => 'en', 'src' => 'signs/letters/en_k.png', 'text' => 'K'],
            ['key' => 'L', 'type' => 'image', 'language' => 'en', 'src' => 'signs/letters/en_l.png', 'text' => 'L'],
            ['key' => 'M', 'type' => 'image', 'language' => 'en', 'src' => 'signs/letters/en_m.png', 'text' => 'M'],
            ['key' => 'N', 'type' => 'image', 'language' => 'en', 'src' => 'signs/letters/en_n.png', 'text' => 'N'],
            ['key' => 'O', 'type' => 'image', 'language' => 'en', 'src' => 'signs/letters/en_o.png', 'text' => 'O'],
            ['key' => 'P', 'type' => 'image', 'language' => 'en', 'src' => 'signs/letters/en_p.png', 'text' => 'P'],
            ['key' => 'Q', 'type' => 'image', 'language' => 'en', 'src' => 'signs/letters/en_q.png', 'text' => 'Q'],
            ['key' => 'R', 'type' => 'image', 'language' => 'en', 'src' => 'signs/letters/en_r.png', 'text' => 'R'],
            ['key' => 'S', 'type' => 'image', 'language' => 'en', 'src' => 'signs/letters/en_s.png', 'text' => 'S'],
            ['key' => 'T', 'type' => 'image', 'language' => 'en', 'src' => 'signs/letters/en_t.png', 'text' => 'T'],
            ['key' => 'U', 'type' => 'image', 'language' => 'en', 'src' => 'signs/letters/en_u.png', 'text' => 'U'],
            ['key' => 'V', 'type' => 'image', 'language' => 'en', 'src' => 'signs/letters/en_v.png', 'text' => 'V'],
            ['key' => 'W', 'type' => 'image', 'language' => 'en', 'src' => 'signs/letters/en_w.png', 'text' => 'W'],
            ['key' => 'X', 'type' => 'image', 'language' => 'en', 'src' => 'signs/letters/en_x.png', 'text' => 'X'],
            ['key' => 'Y', 'type' => 'image', 'language' => 'en', 'src' => 'signs/letters/en_y.png', 'text' => 'Y'],
            ['key' => 'Z', 'type' => 'image', 'language' => 'en', 'src' => 'signs/letters/en_z.png', 'text' => 'Z'],
        ];

        foreach ($signs as $sign) {
            // Check if the sign already exists to prevent duplicates
            $exists = DB::table('sign_assets')->where('key', $sign['key'])->where('type', $sign['type'])->exists();
            
            if (!$exists) {
                DB::table('sign_assets')->insert([
                    'key' => $sign['key'],
                    'type' => $sign['type'],
                    'language' => $sign['language'],
                    'src' => $sign['src'],
                    'text' => $sign['text'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
