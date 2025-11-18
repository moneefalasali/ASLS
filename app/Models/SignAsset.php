<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SignAsset extends Model
{
    use HasFactory;
    
    protected $table = 'sign_assets';
    
    protected $fillable = [
        'key',
        'language', 
        'type',
        'src',
        'text',
        'active',
        'category',
        'difficulty_level',
        'usage_count',
        'description',
        'tags'
    ];

    protected $casts = [
        'active' => 'boolean',
        'tags' => 'array',
        'usage_count' => 'integer'
    ];

    /**
     * Enhanced text to sign sequence mapping with caching and analytics
     */
    public static function mapTextToSequence(string $text, string $lang = 'ar'): array
    {
        $sequence = [];
        
        // Auto-detect language if caller passed default 'ar' but text contains Latin letters
        if ($lang === 'ar') {
            // If the text contains any ASCII letters, prefer English mapping
            if (preg_match('/[A-Za-z]/', $text)) {
                $lang = 'en';
            }
            // If the text contains Arabic letters, keep Arabic
            if (preg_match('/[\p{Arabic}]/u', $text)) {
                $lang = 'ar';
            }
        }

        // Normalize input
        $text = trim($text);
        if ($text === '') {
            return $sequence;
        }

        // Try direct full-text/phrase match before splitting into words
        $fullCandidates = [];
        $fullCandidates[] = $text;
        // normalized (remove extra spaces)
        $fullCandidates[] = preg_replace('/\s+/', ' ', $text);
        // underscore variant (files often use underscores for spaces)
        $fullCandidates[] = str_replace(' ', '_', $text);
        // url-encoded variant
        $fullCandidates[] = rawurlencode($text);

        foreach ($fullCandidates as $cand) {
            $direct = self::findDirectWordMatch($cand, $lang);
            if ($direct) {
                // enhance and return immediately
                $direct = self::enhanceSequenceMetadata([$direct], $text, $lang);
                return $direct;
            }
        }

        // Create cache key for this mapping
        $cacheKey = "sign_sequence:" . md5($text . $lang);
        
        // Try to get from cache first
        $cached = Cache::get($cacheKey);
        if ($cached) {
            Log::info('Sign sequence served from cache', ['text' => $text, 'language' => $lang]);
            return $cached;
        }

        try {
            // Enhanced word processing
            $words = self::preprocessWords($text, $lang);
            
            foreach ($words as $word) {
                $wordSequence = self::mapWordToSigns($word, $lang);
                $sequence = array_merge($sequence, $wordSequence);
            }

            // Add sequence metadata
            $sequence = self::enhanceSequenceMetadata($sequence, $text, $lang);
            
            // Cache the result for 1 hour
            Cache::put($cacheKey, $sequence, 3600);
            
            // Update usage statistics
            self::updateUsageStatistics($sequence);
            
            Log::info('Sign sequence generated', [
                'text' => $text,
                'language' => $lang,
                'word_count' => count($words),
                'sign_count' => count($sequence)
            ]);

        } catch (\Exception $e) {
            Log::error('Sign sequence generation failed', [
                'error' => $e->getMessage(),
                'text' => $text,
                'language' => $lang
            ]);
            
            // Return fallback sequence
            $sequence = self::getFallbackSequence($text, $lang);
        }

        return $sequence;
    }

    /**
     * Preprocess words for better sign matching
     */
    private static function preprocessWords(string $text, string $lang): array
    {
        // Split by whitespace and punctuation
        $words = preg_split('/[\s\p{P}]+/u', $text, -1, PREG_SPLIT_NO_EMPTY);
        
        $processedWords = [];
        
        foreach ($words as $word) {
            if ($lang === 'ar') {
                // Arabic-specific preprocessing
                $word = self::normalizeArabicWord($word);
            } else {
                // English-specific preprocessing
                $word = strtolower(trim($word));
            }
            
            if (strlen($word) > 0) {
                $processedWords[] = $word;
            }
        }
        
        return $processedWords;
    }

    /**
     * Normalize Arabic words for better matching
     */
    private static function normalizeArabicWord(string $word): string
    {
        // Remove diacritics
        $word = preg_replace('/[\x{064B}-\x{065F}\x{0670}\x{06D6}-\x{06ED}]/u', '', $word);
        
        // Normalize common Arabic character variations
        $normalizations = [
            'أ' => 'ا', 'إ' => 'ا', 'آ' => 'ا',
            'ة' => 'ه',
            'ى' => 'ي',
            'ؤ' => 'و',
            'ئ' => 'ي'
        ];
        
        foreach ($normalizations as $from => $to) {
            $word = str_replace($from, $to, $word);
        }
        
        return trim($word);
    }

    /**
     * Map a single word to sign sequence
     */
    private static function mapWordToSigns(string $word, string $lang): array
    {
        $wordSigns = [];
        
        // First, try to find a direct match for the whole word
        $directMatch = self::findDirectWordMatch($word, $lang);
        if ($directMatch) {
            $wordSigns[] = $directMatch;
            return $wordSigns;
        }
        
        // Try compound word matching (for words like "مرحبا" -> "مرحب" + "ا")
        $compoundMatch = self::findCompoundWordMatch($word, $lang);
        if (!empty($compoundMatch)) {
            return $compoundMatch;
        }
        
        // Fall back to character-by-character mapping
        $charSigns = self::mapWordToCharacters($word, $lang);
        return $charSigns;
    }

    /**
     * Find direct word match in database
     */
    private static function findDirectWordMatch(string $word, string $lang): ?array
    {
        if ($lang === 'en') {
            // English keys may be stored uppercased; match case-insensitively
            $asset = self::where('language', $lang)
                         ->whereRaw('LOWER(key) = ?', [strtolower($word)])
                         ->where('active', true)
                         ->first();
        } else {
            $asset = self::where('language', $lang)
                         ->where('key', $word)
                         ->where('active', true)
                         ->first();
        }

        if ($asset) {
            $src = self::normalizeSrc($asset->src);
            if (!self::publicFileExists($src)) {
                $resolved = self::tryResolveSrcFromKey($asset->key, $word, $lang);
                if ($resolved) $src = $resolved;
            }

            return [
                'type' => $asset->type,
                'src' => $src,
                'text' => $asset->text ?? $word,
                'key' => $asset->key,
                'category' => $asset->category,
                'confidence' => 1.0,
                'match_type' => 'direct_word'
            ];
        }

        return null;
    }

    /**
     * Find compound word matches (experimental)
     */
    private static function findCompoundWordMatch(string $word, string $lang): array
    {
        $matches = [];
        
        // Try to break word into meaningful parts
        if ($lang === 'ar' && strlen($word) > 4) {
            // Try common Arabic prefixes and suffixes
            $prefixes = ['ال', 'و', 'ب', 'ل', 'ف'];
            $suffixes = ['ها', 'هم', 'هن', 'ني', 'ك', 'ت'];
            
            foreach ($prefixes as $prefix) {
                if (str_starts_with($word, $prefix)) {
                    $remaining = substr($word, strlen($prefix));
                    $prefixMatch = self::findDirectWordMatch($prefix, $lang);
                    $remainingMatch = self::findDirectWordMatch($remaining, $lang);
                    
                    if ($prefixMatch && $remainingMatch) {
                        return [$prefixMatch, $remainingMatch];
                    }
                }
            }
        }
        
        return $matches;
    }

    /**
     * Map word to individual character signs
     */
    private static function mapWordToCharacters(string $word, string $lang): array
    {
        $charSigns = [];
        $chars = preg_split('//u', $word, -1, PREG_SPLIT_NO_EMPTY);
        
        foreach ($chars as $char) {
            // normalize Arabic characters to base forms for better matching
            if ($lang === 'ar') {
                $char = self::normalizeArabicChar($char);
            }
            $charSign = self::findCharacterSign($char, $lang);
            if ($charSign) {
                $charSigns[] = $charSign;
            }
        }
        
        return $charSigns;
    }

    /**
     * Normalize single Arabic character to base form (e.g., أ,إ,آ -> ا)
     */
    private static function normalizeArabicChar(string $char): string
    {
        $map = [
            'أ' => 'ا', 'إ' => 'ا', 'آ' => 'ا',
            'ؤ' => 'و', 'ئ' => 'ي', 'ى' => 'ي', 'ة' => 'ه'
        ];

        return $map[$char] ?? $char;
    }

    /**
     * Find sign for individual character
     */
    private static function findCharacterSign(string $char, string $lang): ?array
    {
        // Use character directly as key for both Arabic and English
        $key = $char;
        if ($lang === 'en') {
            $key = strtoupper($char); // English letters are stored in uppercase
        }
        // Try several key formats (direct char, language-prefixed hex) to support legacy keys
        $query = self::where('language', $lang)
                     ->where('active', true);

        // English: uppercase letter keys
        if ($lang === 'en') {
            $query = $query->where('key', strtoupper($char));
            $asset = $query->first();
        } else {
            // Arabic or others: try direct character key first
            $asset = (clone $query)->where('key', $key)->first();

            if (!$asset) {
                // try legacy binary-hex prefixed keys like 'ar_d8a7'
                // get UTF-8 bytes hex
                $hex = strtolower(bin2hex(mb_convert_encoding($char, 'UTF-8')));
                $legacyKey = 'ar_' . $hex;
                $asset = (clone $query)->where('key', $legacyKey)->first();
            }

            if (!$asset) {
                // try URL-encoded filename key (in case stored that way)
                $encoded = rawurlencode($char);
                $asset = (clone $query)->where('key', 'ar_' . $encoded)->first();
            }
        }

        if ($asset) {
            $src = self::normalizeSrc($asset->src);

            // If the normalized src doesn't exist on disk, attempt to resolve alternate filenames
            if (!self::publicFileExists($src)) {
                $resolved = self::tryResolveSrcFromKey($asset->key, $char, $lang);
                if ($resolved) {
                    $src = $resolved;
                } else {
                    // last resort: try resolving from the character itself
                    $charResolved = self::tryResolveSrcFromChar($char, $lang);
                    if ($charResolved) $src = $charResolved;
                }
            }

            return [
                'type' => $asset->type,
                'src' => $src,
                'text' => $asset->text ?? $char,
                'key' => $asset->key,
                'category' => $asset->category ?? 'letters',
                'confidence' => 0.8,
                'match_type' => 'character'
            ];
        }

        // If no DB asset, try resolving a file path by character heuristics
        $resolved = self::tryResolveSrcFromChar($char, $lang);
        if ($resolved) {
            return [
                'type' => 'image',
                'src' => $resolved,
                'text' => $char,
                'key' => $char,
                'category' => 'letters',
                'confidence' => 0.5,
                'match_type' => 'character_resolved'
            ];
        }

        // Return placeholder for unknown characters
        return [
            'type' => 'image',
            'src' => '/storage/signs/placeholder.png',
            'text' => $char,
            'key' => 'placeholder',
            'category' => 'unknown',
            'confidence' => 0.3,
            'match_type' => 'placeholder'
        ];
    }

    /**
     * Check if a public path exists on disk (accepts web path starting with /storage/...)
     */
    private static function publicFileExists(string $webPath): bool
    {
        $trim = ltrim($webPath, '/');
        $full = public_path($trim);
        return file_exists($full);
    }

    /**
     * Try to resolve a src path from a DB key (legacy or encoded forms)
     */
    private static function tryResolveSrcFromKey(string $key, string $char, string $lang): ?string
    {
        // If key already looks like a path (contains / or .png), try normalizeSrc
        if (str_contains($key, '/') || str_ends_with($key, '.png')) {
            $candidate = self::normalizeSrc($key);
            if (self::publicFileExists($candidate)) return $candidate;
        }

        // Common legacy patterns: ar_d8a7, ar_%D8%A7.png, en_a
        $candidates = [];

        if ($lang === 'ar') {
            // hex form
            if (preg_match('/^ar_([0-9a-fA-F]+)$/', $key, $m)) {
                $hex = $m[1];
                $bytes = hex2bin($hex);
                if ($bytes !== false) {
                    $charStr = mb_convert_encoding($bytes, 'UTF-8', '8bit');
                    $encoded = rawurlencode($charStr);
                    $candidates[] = "/storage/signs/letters/ar_{$encoded}.png";
                    $candidates[] = "/storage/signs/letters/ar_{$hex}.png";
                    $candidates[] = "/storage/signs/letters/ar_{$charStr}.png";
                }
            }

            // percent-encoded style
            if (preg_match('/^ar_%/i', $key) || str_contains($key, '%')) {
                $k = $key;
                $k = preg_replace('/^ar_?/', '', $k);
                $k = preg_replace('/[^%0-9A-Fa-f]/', '', $k);
                if ($k) {
                    $candidates[] = "/storage/signs/letters/ar_{$k}.png";
                }
            }

            // plain char
            $encoded = rawurlencode($char);
            $candidates[] = "/storage/signs/letters/ar_{$encoded}.png";
            $candidates[] = "/storage/signs/letters/ar_{$char}.png";
        } else {
            // English or other languages: try letter + png variants
            $base = strtolower($key);
            $candidates[] = "/storage/signs/letters/{$base}.png";
            $candidates[] = "/storage/signs/letters/en_{$base}.png";
            $candidates[] = "/storage/signs/letters/{$base}.PNG";
        }

        foreach ($candidates as $cand) {
            if (self::publicFileExists($cand)) return $cand;
        }

        // try a glob search fallback (scan letters folder for files containing the char or hex)
        $lettersDir = public_path('storage/signs/letters');
        if (is_dir($lettersDir)) {
            $files = scandir($lettersDir);
            foreach ($files as $f) {
                if ($f === '.' || $f === '..') continue;
                // match by containing the character (may match percent-encoded or unicode names)
                if (str_contains($f, $char) || str_contains($f, rawurlencode($char)) || str_contains(strtolower($f), strtolower($key))) {
                    $path = '/storage/signs/letters/' . $f;
                    if (self::publicFileExists($path)) return $path;
                }
            }
        }

        return null;
    }

    /**
     * Try to resolve a src path directly from a character when DB lacks a row
     */
    private static function tryResolveSrcFromChar(string $char, string $lang): ?string
    {
        $encoded = rawurlencode($char);
        $candidates = [];
        if ($lang === 'ar') {
            $candidates[] = "/storage/signs/letters/ar_{$encoded}.png";
            $candidates[] = "/storage/signs/letters/ar_{$char}.png";
            $candidates[] = "/storage/signs/letters/{$encoded}.png";
            $candidates[] = "/storage/signs/letters/{$char}.png";
        } else {
            $c = strtolower($char);
            $candidates[] = "/storage/signs/letters/en_{$c}.png";
            $candidates[] = "/storage/signs/letters/{$c}.png";
        }

        foreach ($candidates as $cand) {
            if (self::publicFileExists($cand)) return $cand;
        }

        return null;
    }

    /**
     * Normalize stored src to a valid web path.
     * - If src already starts with /storage or http(s)://, return as-is.
     * - Otherwise prefix with /storage/ and trim any leading slashes.
     */
    private static function normalizeSrc(string $src): string
    {
        $trimmed = trim($src);
        if ($trimmed === '') return '/storage/signs/placeholder.png';

        // If already an absolute storage path or external URL, leave it
        if (str_starts_with($trimmed, '/storage/') || preg_match('#^https?://#i', $trimmed)) {
            return $trimmed;
        }

        // If it already starts with 'storage/', add leading slash
        $trimmed = ltrim($trimmed, '/');
        return '/storage/' . $trimmed;
    }

    /**
     * Enhance sequence with metadata
     */
    private static function enhanceSequenceMetadata(array $sequence, string $originalText, string $lang): array
    {
        foreach ($sequence as $index => &$sign) {
            $sign['order'] = $index;
            $sign['language'] = $lang;
            $sign['original_text'] = $originalText;
            $sign['timestamp'] = now()->toISOString();
            
            // Add difficulty assessment
            $sign['difficulty'] = self::assessSignDifficulty($sign);
            
            // Add learning tips if available
            $sign['learning_tip'] = self::getSignLearningTip($sign['key'] ?? null, $lang);
        }
        
        return $sequence;
    }

    /**
     * Assess sign difficulty for learning purposes
     */
    private static function assessSignDifficulty(array $sign): string
    {
        // Simple difficulty assessment based on sign characteristics
        if ($sign['match_type'] === 'placeholder') {
            return 'unknown';
        }
        
        if ($sign['match_type'] === 'character') {
            return 'easy';
        }
        
        if ($sign['match_type'] === 'direct_word') {
            return 'medium';
        }
        
        return 'easy';
    }

    /**
     * Get learning tip for a sign
     */
    private static function getSignLearningTip(string $key = null, string $lang): ?string
    {
        if (!$key) return null;
        
        // Common learning tips in Arabic and English
        $tips = [
            'ar' => [
                'مرحبا' => 'ارفع يدك وحرك أصابعك للترحيب',
                'شكرا' => 'ضع يدك على صدرك ثم حركها للأمام',
                'نعم' => 'حرك رأسك للأعلى والأسفل',
                'لا' => 'حرك رأسك يميناً ويساراً'
            ],
            'en' => [
                'hello' => 'Raise your hand and wave your fingers',
                'thank' => 'Place hand on chest then move forward',
                'yes' => 'Nod your head up and down',
                'no' => 'Shake your head left and right'
            ]
        ];
        
        return $tips[$lang][$key] ?? null;
    }

    /**
     * Update usage statistics for signs
     */
    private static function updateUsageStatistics(array $sequence): void
    {
        try {
            $keys = array_column($sequence, 'key');
            $keys = array_filter($keys, fn($key) => $key !== 'placeholder');
            
            if (!empty($keys)) {
                self::whereIn('key', $keys)
                    ->increment('usage_count');
            }
        } catch (\Exception $e) {
            Log::warning('Failed to update usage statistics', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Get fallback sequence for failed mappings
     */
    private static function getFallbackSequence(string $text, string $lang): array
    {
        $words = explode(' ', $text);
        $fallbackSequence = [];
        
        foreach ($words as $index => $word) {
            $fallbackSequence[] = [
                'type' => 'image',
                'src' => '/storage/signs/placeholder.png',
                'text' => $word,
                'key' => 'fallback_' . $index,
                'category' => 'unknown',
                'confidence' => 0.1,
                'match_type' => 'fallback',
                'order' => $index,
                'language' => $lang,
                'difficulty' => 'unknown'
            ];
        }
        
        return $fallbackSequence;
    }

    /**
     * Get popular signs for recommendations
     */
    public static function getPopularSigns(string $lang = 'ar', int $limit = 10): array
    {
        return self::where('language', $lang)
                   ->where('active', true)
                   ->orderBy('usage_count', 'desc')
                   ->limit($limit)
                   ->get()
                   ->toArray();
    }

    /**
     * Search signs by text or category
     */
    public static function searchSigns(string $query, string $lang = 'ar', int $limit = 20): array
    {
        return self::where('language', $lang)
                   ->where('active', true)
                   ->where(function($q) use ($query) {
                       $q->where('text', 'like', "%{$query}%")
                         ->orWhere('key', 'like', "%{$query}%")
                         ->orWhere('category', 'like', "%{$query}%")
                         ->orWhere('description', 'like', "%{$query}%");
                   })
                   ->orderBy('usage_count', 'desc')
                   ->limit($limit)
                   ->get()
                   ->toArray();
    }

    /**
     * Get signs by category
     */
    public static function getSignsByCategory(string $category, string $lang = 'ar'): array
    {
        return self::where('language', $lang)
                   ->where('category', $category)
                   ->where('active', true)
                   ->orderBy('text')
                   ->get()
                   ->toArray();
    }

    /**
     * Get available categories
     */
    public static function getCategories(string $lang = 'ar'): array
    {
        return self::where('language', $lang)
                   ->where('active', true)
                   ->whereNotNull('category')
                   ->distinct()
                   ->pluck('category')
                   ->toArray();
    }
}
