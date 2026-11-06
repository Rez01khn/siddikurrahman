<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BiographyController;
use App\Http\Controllers\Admin\CareerRecordController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\HomepageSectionController;
use App\Http\Controllers\Admin\PageManagementController;
use App\Http\Controllers\Admin\PhotoAlbumController;
use App\Http\Controllers\Admin\PhotoController;
use App\Http\Controllers\Admin\SponsorController;
use App\Http\Controllers\Admin\TournamentController;
use App\Http\Controllers\Admin\TrainingContentController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VideoController;
use App\Http\Controllers\Admin\WebsiteSettingController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SeoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Frontend Single-Page Portfolio Routes
|--------------------------------------------------------------------------
*/
Route::get('/', HomeController::class)->name('home');
Route::get('/about', AboutController::class)->name('about');
Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:contact')->name('contact.store');
Route::get('/robots.txt', [SeoController::class, 'robots'])->name('robots');
Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');

/*
|--------------------------------------------------------------------------
| Admin Panel Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function (): void {
    Route::middleware('guest:web')->group(function (): void {
        Route::get('/login', [AuthController::class, 'create'])->name('login');
        Route::post('/login', [AuthController::class, 'store'])
            ->middleware('throttle:admin-login')->name('login.store');
    });

    Route::middleware(['auth:web', 'admin', 'auth.session'])->group(function (): void {
        Route::get('/', DashboardController::class)->name('dashboard');

        // Page Manager Hubs (Home & About)
        Route::get('/pages/home', [PageManagementController::class, 'home'])->name('pages.home');
        Route::get('/pages/about', [PageManagementController::class, 'about'])->name('pages.about');

        // Hero & Banners Management
        Route::resource('homepage', HomepageSectionController::class)
            ->parameters(['homepage' => 'homepageSection'])->except('show');
        Route::patch('/homepage/{homepageSection}/toggle', [HomepageSectionController::class, 'toggle'])
            ->name('homepage.toggle');

        // Biography & About Management
        Route::get('/biography', [BiographyController::class, 'edit'])->name('biography.edit');
        Route::put('/biography', [BiographyController::class, 'update'])->name('biography.update');

        // Key Achievements Management
        Route::prefix('achievements')->name('achievements.')->group(function (): void {
            $controller = CareerRecordController::class;
            Route::get('/', [$controller, 'index'])->defaults('type', 'achievements')->name('index');
            Route::get('/create', [$controller, 'create'])->defaults('type', 'achievements')->name('create');
            Route::post('/', [$controller, 'store'])->defaults('type', 'achievements')->name('store');
            Route::get('/{record}/edit', [$controller, 'edit'])->whereNumber('record')->defaults('type', 'achievements')->name('edit');
            Route::put('/{record}', [$controller, 'update'])->whereNumber('record')->defaults('type', 'achievements')->name('update');
            Route::patch('/{record}/toggle', [$controller, 'toggle'])->whereNumber('record')->defaults('type', 'achievements')->name('toggle');
            Route::delete('/{record}', [$controller, 'destroy'])->whereNumber('record')->defaults('type', 'achievements')->name('destroy');
        });

        // Tournament Schedule
        Route::resource('tournaments', TournamentController::class)->except('show');
        Route::patch('/tournaments/{tournament}/toggle', [TournamentController::class, 'toggle'])->name('tournaments.toggle');

        // Coaching & Tips
        Route::prefix('coaching')->name('coaching.')->group(function (): void {
            $c = TrainingContentController::class;
            Route::get('/', [$c, 'index'])->defaults('type', 'coaching')->name('index');
            Route::get('/create', [$c, 'create'])->defaults('type', 'coaching')->name('create');
            Route::post('/', [$c, 'store'])->defaults('type', 'coaching')->name('store');
            Route::get('/{record}/edit', [$c, 'edit'])->whereNumber('record')->defaults('type', 'coaching')->name('edit');
            Route::put('/{record}', [$c, 'update'])->whereNumber('record')->defaults('type', 'coaching')->name('update');
            Route::delete('/{record}', [$c, 'destroy'])->whereNumber('record')->defaults('type', 'coaching')->name('destroy');
        });

        // Photo Gallery
        Route::get('/photos', [PhotoController::class, 'index'])->name('photos.index');
        Route::resource('photo-albums', PhotoAlbumController::class)->except('show');
        Route::scopeBindings()->prefix('photo-albums/{album}/photos')->name('photos.')->group(function (): void {
            $controller = PhotoController::class;
            Route::get('/create', [$controller, 'create'])->name('create');
            Route::post('/', [$controller, 'store'])->name('store');
            Route::get('/{photo}/edit', [$controller, 'edit'])->name('edit');
            Route::put('/{photo}', [$controller, 'update'])->name('update');
            Route::delete('/{photo}', [$controller, 'destroy'])->name('destroy');
        });

        // Video Gallery
        Route::resource('videos', VideoController::class)->except('show');

        // Sponsors & Partners
        Route::resource('sponsors', SponsorController::class)->except('show');
        Route::patch('/sponsors/{sponsor}/toggle', [SponsorController::class, 'toggle'])->name('sponsors.toggle');

        // Contact Messages Inbox
        Route::resource('messages', ContactMessageController::class)->only(['index', 'show', 'update', 'destroy']);

        // Website Settings
        Route::get('/settings', [WebsiteSettingController::class, 'edit'])->name('settings.edit');
        Route::put('/settings', [WebsiteSettingController::class, 'update'])->name('settings.update');

        // Admin User Management
        Route::resource('users', UserController::class)->except('show');

        // Logout
        Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
    });
});
