<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FileBasedSignSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        $folders = [
            // Prefer storage_path public folder first (when `php artisan storage:link` used)
            storage_path('app/public/signs/words'),
            public_path('storage/signs/words'),
            storage_path('app/public/signs/letters'),
            public_path('storage/signs/letters'),
            storage_path('app/public/signs/english'),
            public_path('storage/signs/english'),
            storage_path('app/public/signs/arabic'),
            public_path('storage/signs/arabic'),
            public_path('storage/signs')
        ];

        // Normalize to unique list and prioritize existing directories
        $folders = array_unique(array_filter($folders, function ($f) {
            return is_dir($f);
        }));

        // Process words and letters separately for category classification
        $this->processFolderForType(storage_path('app/public/signs/words'), 'words', $now);
        $this->processFolderForType(public_path('storage/signs/words'), 'words', $now);
        $this->processFolderForType(storage_path('app/public/signs/letters'), 'letters', $now);
        $this->processFolderForType(public_path('storage/signs/letters'), 'letters', $now);

        // also check english and arabic alternate folders
        $this->processFolderForType(storage_path('app/public/signs/english'), 'letters', $now);
        $this->processFolderForType(public_path('storage/signs/english'), 'letters', $now);
        $this->processFolderForType(storage_path('app/public/signs/arabic'), 'letters', $now);
        $this->processFolderForType(public_path('storage/signs/arabic'), 'letters', $now);
    }

    private function processFolderForType($folder, $category, $now)
    {
        if (!is_dir($folder)) return;

        $files = scandir($folder);
        $allowed = ['png','jpg','jpeg','webp','gif','svg'];

        foreach ($files as $file) {
            if ($file === '.' || $file === '..') continue;
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed)) continue;

            $name = pathinfo($file, PATHINFO_FILENAME);

            $detectedLang = preg_match('/\p{Arabic}/u', $name) ? 'ar' : 'en';

            // For common filename patterns like en_a.png or ar_%D8%A3.png
            $key = $name;
            $text = $name;

            // If it is a word with underscores, convert underscores to spaces for better readability
            $text = str_replace('_', ' ', $text);
            // If filename is percent encoded, decode
            $decoded = urldecode($text);
            if ($decoded !== $text) {
                $text = $decoded;
            }

            // Normalize text for english letters
            if ($detectedLang === 'en') {
                // For english letters, allow lowercase key
                $key = strtolower($key);
            }

            // Build src relative to /storage path
            // Determine which folder matched (words vs letters) to construct src path
            $pathRel = str_replace(public_path(), '', $folder);
            if (str_starts_with($pathRel, DIRECTORY_SEPARATOR)) {
                $pathRel = ltrim($pathRel, DIRECTORY_SEPARATOR);
            }
            // Fallback if pathRel empty
            if ($pathRel === '') {
                if ($category === 'words') {
                    $pathRel = 'storage/signs/words';
                } else {
                    $pathRel = 'storage/signs/letters';
                }
            }

            $src = '/' . trim($pathRel, '\\/') . '/' . $file;
            // For typical storage path starting with 'storage/...' ensure leading slash
            $src = '/' . ltrim($src, '/');

            // Determine type
            $type = 'image';

            // Insert or update record, mapping values accordingly
            // Debug: output what we are inserting/updating
            if (php_sapi_name() === 'cli') {
                echo "Processing file: $file -> key=$key lang=$detectedLang category=$category src=$src\n";
            }
            DB::table('sign_assets')->updateOrInsert(
                ['language' => $detectedLang, 'key' => $key],
                [
                    'key' => $key,
                    'language' => $detectedLang,
                    'type' => $type,
                    'src' => $src,
                    'text' => $text,
                    'active' => true,
                    'category' => $category,
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }
    }
}
