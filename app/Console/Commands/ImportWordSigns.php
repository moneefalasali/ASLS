<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ImportWordSigns extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'signs:import-words {--path= : Path to words folder (defaults to public/storage/signs/words)} {--lang=ar : language for imported signs}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import word sign images from a folder and create SignAsset records';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $path = $this->option('path') ?: public_path('storage/signs/words');
        $lang = $this->option('lang') ?: 'ar';

        if (!is_dir($path)) {
            $this->error("Path not found: {$path}");
            return 1;
        }

        $files = scandir($path);
        $count = 0;
        foreach ($files as $f) {
            if ($f === '.' || $f === '..') continue;
            $full = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $f;
            if (!is_file($full)) continue;
            $ext = pathinfo($f, PATHINFO_EXTENSION);
            if (!in_array(strtolower($ext), ['png','jpg','jpeg','gif','webp'])) continue;

            $key = pathinfo($f, PATHINFO_FILENAME);
            // Use storage path relative to public/storage
            $src = '/storage/signs/words/' . $f;

            // Insert or update
            DB::table('sign_assets')->updateOrInsert([
                'language' => $lang,
                'key' => $key
            ], [
                'type' => 'image',
                'src' => $src,
                'text' => $key,
                'active' => 1,
                'category' => 'words',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $count++;
        }

        $this->info("Imported/updated {$count} word sign(s) from {$path}");
        return 0;
    }
}
