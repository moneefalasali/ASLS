<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SignController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Health check endpoint
Route::get('/health', [SignController::class, 'healthCheck']);

// Core conversion endpoints (legacy compatibility)
Route::post('/convert-text', [SignController::class, 'convertText']);
Route::post('/convert-audio', [SignController::class, 'convertAudio']);

// Identify a sign image (returns associated text and metadata)
Route::post('/signs/identify', [SignController::class, 'identifySign']);
Route::post('/signs/generate-tts', [SignController::class, 'generateTTS']);

// Enhanced API routes
Route::prefix('v1')->group(function () {
    
    // Core conversion endpoints
    Route::post('/convert-text', [SignController::class, 'convertText']);
    Route::post('/convert-audio', [SignController::class, 'convertAudio']);
    
    // Signs library endpoints
    Route::get('/signs', [SignController::class, 'getSignsLibrary']);
    Route::get('/signs/categories', function() {
        return response()->json([
            'success' => true,
            'categories' => \App\Models\SignAsset::getCategories(request('language', 'ar'))
        ]);
    });
    
    Route::get('/signs/popular', function() {
        return response()->json([
            'success' => true,
            'signs' => \App\Models\SignAsset::getPopularSigns(
                request('language', 'ar'),
                request('limit', 10)
            )
        ]);
    });
    
    Route::get('/signs/search', function() {
        $query = request('q', '');
        $language = request('language', 'ar');
        $limit = request('limit', 20);
        
        if (empty($query)) {
            return response()->json([
                'success' => false,
                'error' => 'Search query is required'
            ], 400);
        }
        
        return response()->json([
            'success' => true,
            'results' => \App\Models\SignAsset::searchSigns($query, $language, $limit)
        ]);
    });

    // Folder-based signs (reads images directly from storage/signs/words)
    Route::get('/signs/from-folder/words', [\App\Http\Controllers\Api\SignController::class, 'getWordsFromFolder']);
});

// Protected routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    
    // User profile endpoint
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    // User preferences
    Route::get('/preferences', function (Request $request) {
        $user = $request->user();
        return response()->json([
            'success' => true,
            'preferences' => [
                'language' => $user->preferred_language ?? 'ar',
                'theme' => $user->theme ?? 'light',
                'notifications' => $user->notifications_enabled ?? true,
                'auto_translate' => $user->auto_translate ?? false
            ]
        ]);
    });
    
    Route::post('/preferences', function (Request $request) {
        $request->validate([
            'language' => 'sometimes|string|in:ar,en',
            'theme' => 'sometimes|string|in:light,dark',
            'notifications' => 'sometimes|boolean',
            'auto_translate' => 'sometimes|boolean'
        ]);
        
        $user = $request->user();
        $user->update([
            'preferred_language' => $request->input('language', $user->preferred_language),
            'theme' => $request->input('theme', $user->theme),
            'notifications_enabled' => $request->input('notifications', $user->notifications_enabled),
            'auto_translate' => $request->input('auto_translate', $user->auto_translate)
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Preferences updated successfully'
        ]);
    });
});

// Analytics endpoints
Route::middleware(['throttle:60,1'])->group(function () {
    
    Route::post('/analytics/usage', function (Request $request) {
        $request->validate([
            'action' => 'required|string',
            'data' => 'sometimes|array'
        ]);
        
        \Illuminate\Support\Facades\Log::info('Usage analytics', [
            'action' => $request->input('action'),
            'data' => $request->input('data', []),
            'user_agent' => $request->userAgent(),
            'ip' => $request->ip(),
            'timestamp' => now()
        ]);
        
        return response()->json(['success' => true]);
    });
});

// Development endpoints (only in non-production)
if (app()->environment(['local', 'testing'])) {
    Route::prefix('dev')->group(function () {
        
        Route::post('/generate-test-signs', function () {
            $testSigns = [
                ['key' => 'مرحبا', 'text' => 'مرحبا', 'language' => 'ar', 'category' => 'greetings'],
                ['key' => 'شكرا', 'text' => 'شكرا', 'language' => 'ar', 'category' => 'greetings'],
                ['key' => 'hello', 'text' => 'hello', 'language' => 'en', 'category' => 'greetings'],
                ['key' => 'thank', 'text' => 'thank you', 'language' => 'en', 'category' => 'greetings']
            ];
            
            foreach ($testSigns as $sign) {
                \App\Models\SignAsset::updateOrCreate(
                    ['key' => $sign['key'], 'language' => $sign['language']],
                    array_merge($sign, [
                        'type' => 'image',
                        'src' => '/storage/signs/placeholder.png',
                        'active' => true
                    ])
                );
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Test signs generated successfully'
            ]);
        });
        
        Route::get('/db-status', function () {
            try {
                $signCount = \App\Models\SignAsset::count();
                $uploadCount = \App\Models\Upload::count();
                
                return response()->json([
                    'success' => true,
                    'database' => [
                        'signs_count' => $signCount,
                        'uploads_count' => $uploadCount,
                        'connection' => 'healthy'
                    ]
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'error' => 'Database connection failed',
                    'message' => $e->getMessage()
                ], 500);
            }
        });
    });
}
