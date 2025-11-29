<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\SignAssetController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\UploadController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Welcome page - always public
Route::get('/', function () {
    // Always serve the welcome page as the main landing page
    return view('welcome');
})->name('home');



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Welcome page - always public
Route::get('/', function () {
    // Always serve the welcome page as the main landing page
    return view('welcome');
})->name('home');

// Public features - accessible without authentication
Route::get('/word-keyboard', function () {
    return view('word_keyboard');
})->name('word.keyboard');

Route::get('/signs', function () {
    return view('signs');
})->name('signs');

Route::get('/conversations', function () {
    return view('conversations');
})->name('conversations');

Route::get('/dashboard', function () {
    return view('home'); // Use the new mobile home view
})->name('dashboard');

// Protected routes - require authentication
Route::middleware(['auth'])->group(function () {
    // Profile management
    Route::get('/profile', function () {
        return view('profile');
    })->name('profile.show');
    
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin routes
Route::middleware(['auth','admin'])->prefix('admin')->name('admin.')->group(function(){
    Route::resource('signs', SignAssetController::class)->parameters(['signs'=>'sign']);
    Route::get('users', [UserController::class,'index'])->name('users.index');
    Route::post('users/{user}/toggle', [UserController::class,'toggle'])->name('users.toggle');
    Route::get('uploads', [UploadController::class,'index'])->name('uploads.index');
    Route::get('uploads/{upload}', [UploadController::class,'show'])->name('uploads.show');
    // Icon and indexing tools
    Route::get('icon-tools', [\App\Http\Controllers\Admin\IconController::class, 'index'])->name('icon.tools');
    Route::post('icon-tools/reindex', [\App\Http\Controllers\Admin\IconController::class, 'reindex'])->name('icon.reindex');
    Route::post('icon-tools/purge-sw', [\App\Http\Controllers\Admin\IconController::class, 'purgeSW'])->name('icon.purge_sw');
    Route::post('icon-tools/upload', [\App\Http\Controllers\Admin\IconController::class, 'upload'])->name('icon.upload');
});


// Protected routes - require authentication
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', function () {
        return view('profile');
    })->name('profile.show');
    
    // Basic profile management
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    
    // Advanced profile management (require verification)
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
