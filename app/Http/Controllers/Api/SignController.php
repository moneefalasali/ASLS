<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SignAsset;
use App\Models\Upload;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SignController extends Controller
{
    /**
     * Convert text to sign language sequence
     */
    public function convertText(Request $request)
    {
        $request->validate([
            "text" => "required|string|max:1000",
            "language" => "sometimes|string|in:ar,en",
            "speed" => "sometimes|string|in:slow,normal,fast",
            "style" => "sometimes|string|in:standard,formal,casual"
        ]);

        $text = trim($request->input('text'));
        $lang = $request->input('language', 'ar');
        $speed = $request->input('speed', 'normal');
        $style = $request->input('style', 'standard');

        try {
            // Enhanced text preprocessing
            $processedText = $this->preprocessText($text, $lang);
            
            // Get sign sequence with enhanced mapping
            $sequence = SignAsset::mapTextToSequence($processedText, $lang);
            
            // Apply speed and style modifications
            $sequence = $this->applySequenceModifications($sequence, $speed, $style);
            
            // Log successful conversion for analytics
            Log::info('Text converted to signs', [
                'text_length' => strlen($text),
                'language' => $lang,
                'sequence_count' => count($sequence),
                'user_id' => optional($request->user())->id
            ]);

            return response()->json([
                'success' => true,
                'sequence' => $sequence,
                'metadata' => [
                    'original_text' => $text,
                    'processed_text' => $processedText,
                    'language' => $lang,
                    'speed' => $speed,
                    'style' => $style,
                    'total_signs' => count($sequence),
                    'estimated_duration' => $this->calculateDuration($sequence, $speed)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Text conversion failed', [
                'error' => $e->getMessage(),
                'text' => $text,
                'language' => $lang
            ]);

            return response()->json([
                'success' => false,
                'error' => 'فشل في تحويل النص إلى لغة الإشارة',
                'message' => 'حدث خطأ أثناء معالجة النص'
            ], 500);
        }
    }

    /**
     * Convert audio to sign language sequence with enhanced STT mockup
     */
    public function convertAudio(Request $request)
    {
        $request->validate([
            "audio" => "required|file|mimes:mp3,wav,webm,ogg,m4a|max:10240", // 10MB max
            "language" => "sometimes|string|in:ar,en"
        ]);

        $file = $request->file('audio');
        $originalName = $file->getClientOriginalName() ?? 'recording';
        $mime = $file->getClientMimeType();
        $lang = $request->input('language', 'ar');

        try {
            // Store original upload
            $path = $file->store('uploads', 'public');
            $storedFull = storage_path('app/public/' . $path);
            $finalPath = $path;

            // Enhanced audio processing
            $processedAudio = $this->processAudioFile($storedFull, $path);
            if ($processedAudio) {
                $finalPath = $processedAudio;
                $mime = 'audio/wav';
            }

            // Enhanced STT mockup with realistic transcription
            $transcribed = $this->mockSpeechToText($originalName, $lang);
            
            // Save upload record with enhanced metadata
            $upload = Upload::create([
                'path' => '/storage/' . $finalPath,
                'original_name' => $originalName,
                'mimetype' => $mime,
                'transcription' => $transcribed,
                'user_id' => optional($request->user())->id,
                'file_size' => $file->getSize(),
                'duration' => $this->estimateAudioDuration($storedFull),
                'language' => $lang,
                'processing_status' => 'completed'
            ]);

            // Convert transcribed text to sign sequence
            $sequence = SignAsset::mapTextToSequence($transcribed, $lang);
            
            // Apply default modifications for audio-derived sequences
            $sequence = $this->applySequenceModifications($sequence, 'normal', 'casual');

            Log::info('Audio converted to signs', [
                'upload_id' => $upload->id,
                'original_name' => $originalName,
                'transcription' => $transcribed,
                'language' => $lang,
                'sequence_count' => count($sequence)
            ]);

            return response()->json([
                'success' => true,
                'upload_id' => $upload->id,
                'transcription' => $transcribed,
                'sequence' => $sequence,
                'metadata' => [
                    'audio_path' => $upload->path,
                    'language' => $lang,
                    'file_size' => $upload->file_size,
                    'duration' => $upload->duration,
                    'total_signs' => count($sequence),
                    'confidence' => $this->calculateTranscriptionConfidence($transcribed)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Audio conversion failed', [
                'error' => $e->getMessage(),
                'file_name' => $originalName,
                'language' => $lang
            ]);

            return response()->json([
                'success' => false,
                'error' => 'فشل في تحويل الصوت إلى لغة الإشارة',
                'message' => 'حدث خطأ أثناء معالجة الملف الصوتي'
            ], 500);
        }
    }

    /**
     * Get available signs library
     */
    public function getSignsLibrary(Request $request)
    {
        $request->validate([
            'language' => 'sometimes|string|in:ar,en',
            'category' => 'sometimes|string',
            'search' => 'sometimes|string|max:100',
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:50'
        ]);

        $lang = $request->input('language', 'ar');
        $category = $request->input('category');
        $search = $request->input('search');
        $perPage = $request->input('per_page', 20);

        $query = SignAsset::where('language', $lang)
                          ->where('active', true);

        if ($category) {
            $query->where('category', $category);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('text', 'like', "%{$search}%")
                  ->orWhere('key', 'like', "%{$search}%");
            });
        }

        $signs = $query->orderBy('text')
                      ->paginate($perPage);

        return response()->json([
            'success' => true,
            'signs' => $signs->items(),
            'pagination' => [
                'current_page' => $signs->currentPage(),
                'total_pages' => $signs->lastPage(),
                'total_items' => $signs->total(),
                'per_page' => $signs->perPage()
            ]
        ]);
    }

    /**
     * Identify a sign asset by image src or key and return associated text and metadata.
     */
    public function identifySign(Request $request)
    {
        $request->validate([
            'src' => 'sometimes|string',
            'key' => 'sometimes|string'
        ]);

        $src = $request->input('src');
        $key = $request->input('key');

        try {
            $query = SignAsset::query();

            if ($src) {
                // Normalize possible absolute URL to storage path
                $parsed = parse_url($src);
                $path = $parsed['path'] ?? $src;
                // Accept variants with or without leading /storage
                $candidates = [$path];
                if (!str_starts_with($path, '/storage/')) {
                    $candidates[] = '/storage/' . ltrim($path, '/');
                }
                // try exact src match first
                $asset = $query->whereIn('src', $candidates)->where('active', true)->first();
                if (!$asset) {
                    // try filename match
                    $filename = basename($path);
                    $asset = $query->where('src', 'like', "%{$filename}%")->where('active', true)->first();
                }
            } elseif ($key) {
                $asset = $query->where('key', $key)->where('active', true)->first();
            } else {
                return response()->json(['success' => false, 'error' => 'src or key is required'], 400);
            }

            if (!$asset) {
                return response()->json(['success' => false, 'error' => 'Sign not found'], 404);
            }

            return response()->json([
                'success' => true,
                'sign' => [
                    'key' => $asset->key,
                    'text' => $asset->text ?? $asset->key,
                    'language' => $asset->language,
                    'src' => $asset->src,
                    'category' => $asset->category ?? null,
                    'description' => $asset->description ?? null
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('identifySign failed', ['error' => $e->getMessage(), 'src' => $src, 'key' => $key]);
            return response()->json(['success' => false, 'error' => 'Internal server error'], 500);
        }
    }

    /**
     * Generate server-side TTS audio for a sign (attempts local engine like espeak).
     * Returns the URL to the generated audio file in storage if successful.
     */
    public function generateTTS(Request $request)
    {
        $request->validate([
            'key' => 'sometimes|string',
            'src' => 'sometimes|string',
            'format' => 'sometimes|string|in:wav,mp3'
        ]);

        $key = $request->input('key');
        $src = $request->input('src');
        $format = $request->input('format', 'wav');

        try {
            $query = SignAsset::query();
            $asset = null;
            if ($key) {
                $asset = $query->where('key', $key)->where('active', true)->first();
            } elseif ($src) {
                $asset = $query->where('src', $src)->where('active', true)->first();
                if (!$asset) {
                    $filename = basename(parse_url($src, PHP_URL_PATH));
                    $asset = $query->where('src', 'like', "%{$filename}%")->where('active', true)->first();
                }
            }

            if (!$asset) {
                return response()->json(['success' => false, 'error' => 'Sign not found'], 404);
            }

            $text = $asset->text ?? $asset->key;
            $safeKey = preg_replace('/[^A-Za-z0-9_\-]/', '_', $asset->key);
            $audioDir = storage_path('app/public/signs/audio');
            if (!is_dir($audioDir)) mkdir($audioDir, 0755, true);

            $audioFilename = $safeKey . '.' . $format;
            $audioPath = $audioDir . DIRECTORY_SEPARATOR . $audioFilename;

            // If file already exists, return URL
            if (file_exists($audioPath)) {
                return response()->json(['success' => true, 'url' => '/storage/signs/audio/' . $audioFilename]);
            }

            // Try to use espeak (common on Linux) to generate wav
            $engineFound = false;
            $cmd = null;

            // prefer espeak for a quick local TTS
            exec('which espeak 2>/dev/null', $out, $rc);
            if ($rc === 0 && !empty($out)) {
                $engineFound = 'espeak';
            }

            // On Windows, try where
            if (!$engineFound && strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                exec('where espeak 2>&1', $out2, $rc2);
                if ($rc2 === 0) $engineFound = 'espeak';
            }

            if ($engineFound === 'espeak') {
                // espeak writes WAV with -w
                $escapedText = escapeshellarg($text);
                $cmd = "espeak -v \"\" -w " . escapeshellarg($audioPath) . " " . $escapedText;
                // try Arabic voice if available
                // note: voice names vary; we try default first
            }

            if ($cmd) {
                exec($cmd . ' 2>&1', $outCmd, $rcCmd);
                if ($rcCmd === 0 && file_exists($audioPath)) {
                    return response()->json(['success' => true, 'url' => '/storage/signs/audio/' . $audioFilename]);
                }
            }

            return response()->json([
                'success' => false,
                'error' => 'No local TTS engine found or generation failed',
                'hint' => 'Install espeak or configure external TTS and try again'
            ], 501);

        } catch (\Exception $e) {
            Log::error('generateTTS failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => 'Internal error generating TTS'], 500);
        }
    }

    /**
     * Enhanced text preprocessing
     */
    private function preprocessText(string $text, string $lang): string
    {
        // Remove extra whitespace
        $text = preg_replace('/\s+/', ' ', trim($text));
        
        // Handle Arabic text preprocessing
        if ($lang === 'ar') {
            // Remove diacritics for better matching
            $text = preg_replace('/[\x{064B}-\x{065F}\x{0670}\x{06D6}-\x{06ED}]/u', '', $text);
            
            // Normalize Arabic characters
            $text = str_replace(['أ', 'إ', 'آ'], 'ا', $text);
            $text = str_replace('ة', 'ه', $text);
        }
        
        // Handle English text preprocessing
        if ($lang === 'en') {
            $text = strtolower($text);
        }
        
        // Remove punctuation for sign language
        $text = preg_replace('/[^\p{L}\p{N}\s]/u', '', $text);
        
        return trim($text);
    }

    /**
     * Apply speed and style modifications to sequence
     */
    private function applySequenceModifications(array $sequence, string $speed, string $style): array
    {
        $speedMultipliers = [
            'slow' => 1.5,
            'normal' => 1.0,
            'fast' => 0.7
        ];

        $baseDuration = 1000; // 1 second base duration
        $duration = $baseDuration * ($speedMultipliers[$speed] ?? 1.0);

        foreach ($sequence as &$sign) {
            $sign['duration'] = $duration;
            $sign['speed'] = $speed;
            $sign['style'] = $style;
            
            // Add transition effects for smoother playback
            $sign['transition'] = [
                'type' => 'fade',
                'duration' => 200
            ];
        }

        return $sequence;
    }

    /**
     * Calculate estimated duration for sequence
     */
    private function calculateDuration(array $sequence, string $speed): int
    {
        $speedMultipliers = [
            'slow' => 1.5,
            'normal' => 1.0,
            'fast' => 0.7
        ];

        $baseDuration = 1000; // 1 second per sign
        $multiplier = $speedMultipliers[$speed] ?? 1.0;
        
        return (int) (count($sequence) * $baseDuration * $multiplier);
    }

    /**
     * Enhanced audio file processing
     */
    private function processAudioFile(string $inputPath, string $originalPath): ?string
    {
        $ext = strtolower(pathinfo($originalPath, PATHINFO_EXTENSION));
        $needConvert = in_array($ext, ['webm', 'mkv']) || !in_array($ext, ['mp3', 'wav', 'ogg']);
        
        // Check if ffmpeg is available
        $ffmpegAvailable = false;
        exec('ffmpeg -version 2>&1', $output, $returnCode);
        if ($returnCode === 0) {
            $ffmpegAvailable = true;
        }

        if ($needConvert && $ffmpegAvailable) {
            $wavName = pathinfo($originalPath, PATHINFO_FILENAME) . '.wav';
            $wavRel = 'uploads/' . $wavName;
            $wavFull = storage_path('app/public/' . $wavRel);
            
            // Convert to mono 16kHz WAV for better STT compatibility
            $cmd = sprintf(
                'ffmpeg -y -i %s -ar 16000 -ac 1 -acodec pcm_s16le %s 2>&1',
                escapeshellarg($inputPath),
                escapeshellarg($wavFull)
            );
            
            exec($cmd, $output, $returnCode);
            
            if ($returnCode === 0 && file_exists($wavFull)) {
                return $wavRel;
            }
        }

        return null;
    }

    /**
     * Enhanced STT mockup with realistic Arabic and English transcriptions
     */
    private function mockSpeechToText(string $filename, string $lang): string
    {
        // Extract meaningful text from filename
        $basename = pathinfo($filename, PATHINFO_FILENAME);
        $cleanName = str_replace(['_', '-', '.'], ' ', $basename);
        
        // If filename contains meaningful text, use it
        if (strlen($cleanName) > 3 && !preg_match('/^(recording|audio|voice|sound)/i', $cleanName)) {
            return $cleanName;
        }

        // Return realistic sample transcriptions based on language
        $sampleTranscriptions = [
            'ar' => [
                'مرحبا كيف حالك',
                'أهلا وسهلا بك',
                'شكرا لك على المساعدة',
                'أريد أن أتعلم لغة الإشارة',
                'هل يمكنك مساعدتي',
                'أحتاج إلى ترجمة هذا النص',
                'الطقس جميل اليوم',
                'أين يمكنني العثور على المساعدة',
                'أنا سعيد لرؤيتك',
                'وداعا إلى اللقاء'
            ],
            'en' => [
                'hello how are you',
                'welcome to our application',
                'thank you for your help',
                'I want to learn sign language',
                'can you help me please',
                'I need to translate this text',
                'the weather is nice today',
                'where can I find help',
                'I am happy to see you',
                'goodbye see you later'
            ]
        ];

        $samples = $sampleTranscriptions[$lang] ?? $sampleTranscriptions['ar'];
        return $samples[array_rand($samples)];
    }

    /**
     * Estimate audio duration (mockup)
     */
    private function estimateAudioDuration(string $filePath): ?int
    {
        // Try to get actual duration using ffprobe if available
        $cmd = sprintf(
            'ffprobe -v quiet -show_entries format=duration -of csv="p=0" %s 2>&1',
            escapeshellarg($filePath)
        );
        
        exec($cmd, $output, $returnCode);
        
        if ($returnCode === 0 && isset($output[0]) && is_numeric($output[0])) {
            return (int) round(floatval($output[0]));
        }

        // Fallback: estimate based on file size (rough approximation)
        $fileSize = filesize($filePath);
        if ($fileSize) {
            // Assume ~128kbps average bitrate
            return (int) round($fileSize / (128 * 1024 / 8));
        }

        return null;
    }

    /**
     * Calculate transcription confidence (mockup)
     */
    private function calculateTranscriptionConfidence(string $transcription): float
    {
        // Simple confidence calculation based on text characteristics
        $length = strlen($transcription);
        $wordCount = str_word_count($transcription);
        
        if ($length < 5) return 0.6;
        if ($wordCount < 2) return 0.7;
        if ($length > 50) return 0.95;
        
        return 0.85; // Default confidence
    }

    /**
     * Health check endpoint for API monitoring
     */
    public function healthCheck()
    {
        return response()->json([
            'status' => 'healthy',
            'service' => 'AISL Sign Language API',
            'version' => '1.2.0',
            'timestamp' => now()->toISOString(),
            'features' => [
                'text_to_sign' => true,
                'audio_to_sign' => true,
                'signs_library' => true,
                'offline_support' => true
            ]
        ]);
    }

    /**
     * Read sign images from storage/signs/words and return as signs list.
     * Each file becomes an entry with key/text = filename (without ext) and src = /storage/signs/words/<file>
     */
    public function getWordsFromFolder(Request $request)
    {
        $lang = $request->input('language', 'ar');
        $storageFolder = storage_path('app/public/signs/words');
        $publicFolder = public_path('storage/signs/words');

        $allowed = ['png','jpg','jpeg','webp','gif','svg'];
        $results = [];

        // Prefer storage folder (when using storage:link) but fall back to public/storage path
        $folder = null;
        if (is_dir($storageFolder)) {
            $folder = $storageFolder;
            $usePublicSrc = true; // '/storage/...' will map to storage path
        } elseif (is_dir($publicFolder)) {
            $folder = $publicFolder;
            $usePublicSrc = true; // src will be /storage/signs/words/<file>
        } else {
            return response()->json(['success' => true, 'signs' => []]);
        }

        $files = scandir($folder);
        foreach ($files as $f) {
            if (in_array($f, ['.','..'])) continue;
            $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed)) continue;

            $name = pathinfo($f, PATHINFO_FILENAME);
            $text = $name;

            // Basic detection of language based on presence of arabic letters
            $detectedLang = preg_match('/\p{Arabic}/u', $name) ? 'ar' : 'en';

            $results[] = [
                'key' => $name,
                'text' => $text,
                'language' => $detectedLang,
                'src' => '/storage/signs/words/' . $f,
                'category' => 'words',
                'type' => 'image'
            ];
        }

        return response()->json(['success' => true, 'signs' => $results]);
    }
}
