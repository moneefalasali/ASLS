<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class IconController extends Controller
{
    public function index()
    {
        return view('admin.icon_tools');
    }

    public function reindex(Request $request)
    {
        // Run the indexing script via process to ensure it uses current PHP binary
        try {
            $php = PHP_BINARY;
            $script = base_path('scripts/index_sign_images.php');
            if (!file_exists($script)) {
                return response()->json(['success' => false, 'error' => 'Index script missing'], 500);
            }
            $process = new Process([$php, $script]);
            $process->setTimeout(300);
            $process->run();
            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }
            $output = $process->getOutput();
            Log::info('Icon reindex run successfully via admin: ' . substr($output, 0, 200));
            return response()->json(['success' => true, 'message' => 'Reindex completed', 'output' => $output]);
        } catch (\Throwable $e) {
            Log::error('Reindex failed via admin', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function purgeSW(Request $request)
    {
        // Create/overwrite a version file to signal a cache bust
        try {
            $file = public_path('frontend/sw-version.json');
            $version = ['version' => time()];
            file_put_contents($file, json_encode($version));
            // Return success. Frontend will request SW to update/unregister
            return response()->json(['success' => true, 'message' => 'Updated SW version file', 'version' => $version['version']]);
        } catch (\Throwable $e) {
            Log::error('Purge SW failed via admin', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Upload a new logo and regenerate app icons
     */
    public function upload(Request $request)
    {
        $request->validate([ 'logo' => 'required|image|mimes:png,jpg,jpeg,webp|max:5120' ]);
        try {
            $file = $request->file('logo');
            $dest = public_path('frontend');
            if (!is_dir($dest)) mkdir($dest, 0755, true);
            $name = 'logo.png';
            // Backup existing logo if present
            $existing = $dest . '/' . $name;
            if (file_exists($existing)) {
                rename($existing, $existing . '.' . time() . '.bak');
            }
            $file->move($dest, $name);

            // Run generator script directly
            $php = PHP_BINARY;
            $script = base_path('scripts/generate_app_icons.php');
            if (file_exists($script)) {
                $process = new Process([$php, $script, $dest . '/' . $name]);
                $process->setTimeout(300);
                $process->run();
                $output = $process->getOutput();
            } else {
                $output = 'Generator script missing';
            }

            return response()->json(['success' => true, 'message' => 'Uploaded and generated icons', 'output' => $output]);
        } catch (\Throwable $e) {
            Log::error('Icon upload failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
