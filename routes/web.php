<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EpaperController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\UserManagementController;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

/*
|--------------------------------------------------------------------------
| Test Image Route
|--------------------------------------------------------------------------
*/

Route::get('/test-image', function () {
    $manager = new ImageManager(new Driver());
    $image = $manager->create(200, 200)->fill('#ff0000');
    
    return response($image->toPng(), 200, [
        'Content-Type' => 'image/png',
    ]);
});

/*
|--------------------------------------------------------------------------
| Public Epaper Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [EpaperController::class, 'index'])->name('epaper.index');
Route::get('/epaper', [EpaperController::class, 'index'])->name('epaper.home');
Route::get('/epaper/page', [EpaperController::class, 'getPage'])->name('epaper.getPage');
Route::get('/epaper/download', [EpaperController::class, 'downloadPdf'])->name('epaper.download');
Route::get('/epaper/archive', [EpaperController::class, 'archive'])->name('epaper.archive');
Route::get('/epaper/{city}/{date?}', [EpaperController::class, 'index'])->name('epaper.view');
Route::get('/epaper/{city}/{date}/page/{page}', [EpaperController::class, 'archive'])->name('epaper.viewPage');

/*
|--------------------------------------------------------------------------
| Admin Authentication Routes (Public - No Middleware)
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
    Route::get('register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('register', [AuthController::class, 'register']);
});

/*
|--------------------------------------------------------------------------
| Protected Admin Routes (Require Authentication)
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->middleware(['admin.auth'])->group(function () {
    
    // Logout route
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    
    // Dashboard
    Route::get('/users', [AdminController::class, 'index'])->name('dashboard');
    
    /*
    |--------------------------------------------------------------------------
    | E-Paper Management Routes
    | Access: Super Admin, Admin, Editor
    |--------------------------------------------------------------------------
    */
    Route::middleware(['admin.role:super_admin,admin,editor'])->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('index');
        Route::get('/create', [AdminController::class, 'create'])->name('create');
        Route::post('/epaper', [AdminController::class, 'store'])->name('store');
        Route::get('/epaper/{epaper}/edit', [AdminController::class, 'edit'])->name('edit');
        Route::put('/epaper/{epaper}', [AdminController::class, 'update'])->name('update');
        
        // PDF Preview (keeping from your original routes)
        Route::post('/preview-pdf', [AdminController::class, 'previewPdf'])->name('preview-pdf');
    });
    
    /*
    |--------------------------------------------------------------------------
    | E-Paper Delete Routes
    | Access: Super Admin, Admin only
    |--------------------------------------------------------------------------
    */
    Route::middleware(['admin.role:super_admin,admin'])->group(function () {
        Route::delete('/epaper/{epaper}', [AdminController::class, 'destroy'])->name('destroy');
    });
    
    /*
    |--------------------------------------------------------------------------
    | User Management Routes
    | Access: Super Admin only
    |--------------------------------------------------------------------------
    */
    Route::middleware(['super.admin'])->prefix('users')->name('users.')->group(function () {
        Route::get('/users', [UserManagementController::class, 'index'])->name('index');
        Route::get('/create', [UserManagementController::class, 'create'])->name('create');
        Route::post('/', [UserManagementController::class, 'store'])->name('store');
        Route::get('/{user}/edit', [UserManagementController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserManagementController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserManagementController::class, 'destroy'])->name('destroy');
        Route::patch('/{user}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('toggle-status');
    });
});