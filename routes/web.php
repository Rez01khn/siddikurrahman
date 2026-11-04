<?php

use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CareerRecordController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\HomepageSectionController;
use App\Http\Controllers\Admin\LegacyImportController;
use App\Http\Controllers\Admin\ModuleController;
use App\Http\Controllers\Admin\NewsCategoryController;
use App\Http\Controllers\Admin\PhotoAlbumController;
use App\Http\Controllers\Admin\PhotoController;
use App\Http\Controllers\Admin\SocialWorkController;
use App\Http\Controllers\Admin\SocialWorkImageController;
use App\Http\Controllers\Admin\SponsorController;
use App\Http\Controllers\Admin\TournamentResultController;
use App\Http\Controllers\Admin\TrainingContentController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VideoController;
use App\Http\Controllers\Admin\WebsiteSettingController;
use App\Http\Controllers\BiographyController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\SeoController;
use App\Http\Controllers\TournamentController;
use App\Http\Controllers\TrainingController;
use Illuminate\Support\Facades\Route;

Route::get('/in-the-bag', [TrainingController::class, 'equipment'])->name('in-the-bag');
Route::get('/coaching-tips', [TrainingController::class, 'tips'])->name('coaching-tips.index');
Route::get('/coaching-tips/{tip:slug}', [TrainingController::class, 'tip'])->name('coaching-tips.show');
Route::get('/contact', [ContactController::class, 'create'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:contact')->name('contact.store');
Route::get('/robots.txt', [SeoController::class, 'robots'])->name('robots');
Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');
Route::get('/', HomeController::class)->name('home');
Route::get('/biography', BiographyController::class)->name('biography');
Route::get('/sponsors', [CommunityController::class, 'sponsors'])->name('sponsors.index');
Route::get('/social-works', [CommunityController::class, 'projects'])->name('social-works.index');
Route::get('/social-works/{social_work:slug}', [CommunityController::class, 'project'])->name('social-works.show');
Route::get('/photos', [GalleryController::class, 'photos'])->name('photos.index');
Route::get('/photos/{album:slug}', [GalleryController::class, 'album'])->name('photos.show');
Route::get('/videos', [GalleryController::class, 'videos'])->name('videos.index');
Route::get('/videos/{video:slug}', [GalleryController::class, 'video'])->name('videos.show');
Route::get('/news', [NewsController::class, 'index'])->name('news.index');
Route::get('/news/{article:slug}', [NewsController::class, 'show'])->name('news.show');
Route::get('/tournaments', [TournamentController::class, 'index'])->name('tournaments.index');
Route::get('/tournaments/{tournament}', [TournamentController::class, 'show'])->whereNumber('tournament')->name('tournaments.show');

Route::prefix('admin')->name('admin.')->group(function (): void {
    Route::middleware('guest:web')->group(function (): void {
        Route::get('/login', [AuthController::class, 'create'])->name('login');
        Route::post('/login', [AuthController::class, 'store'])
            ->middleware('throttle:admin-login')->name('login.store');
    });

    Route::middleware(['auth:web', 'admin', 'auth.session'])->group(function (): void {
        foreach (['equipment', 'coaching'] as $type) {
            Route::prefix($type)->name($type.'.')->group(function () use ($type): void {
                $c = TrainingContentController::class;
                Route::get('/', [$c, 'index'])->defaults('type', $type)->name('index');
                Route::get('/create', [$c, 'create'])->defaults('type', $type)->name('create');
                Route::post('/', [$c, 'store'])->defaults('type', $type)->name('store');
                Route::get('/{record}/edit', [$c, 'edit'])->whereNumber('record')->defaults('type', $type)->name('edit');
                Route::put('/{record}', [$c, 'update'])->whereNumber('record')->defaults('type', $type)->name('update');
                Route::delete('/{record}', [$c, 'destroy'])->whereNumber('record')->defaults('type', $type)->name('destroy');
            });
        }
        Route::get('/settings', [WebsiteSettingController::class, 'edit'])->name('settings.edit');
        Route::put('/settings', [WebsiteSettingController::class, 'update'])->name('settings.update');
        Route::resource('messages', ContactMessageController::class)->only(['index', 'show', 'update', 'destroy']);
        Route::resource('users', UserController::class)->except('show');
        Route::get('/legacy-imports', [LegacyImportController::class, 'index'])->name('legacy-imports.index');
        Route::get('/legacy-imports/{batch}', [LegacyImportController::class, 'show'])->name('legacy-imports.show');
        Route::get('/legacy-imports/{batch}/download', [LegacyImportController::class, 'download'])->name('legacy-imports.download');
        Route::get('/', DashboardController::class)->name('dashboard');
        Route::resource('sponsors', SponsorController::class)->except('show');
        Route::resource('social-works', SocialWorkController::class)->except('show');
        Route::patch('/sponsors/{sponsor}/toggle', [SponsorController::class, 'toggle'])->name('sponsors.toggle');
        Route::patch('/social-works/{social_work}/toggle', [SocialWorkController::class, 'toggle'])->name('social-works.toggle');
        Route::scopeBindings()->prefix('social-works/{social_work}/images')->name('social-work-images.')->group(function (): void {
            $controller = SocialWorkImageController::class;
            Route::get('/{image}/edit', [$controller, 'edit'])->name('edit');
            Route::put('/{image}', [$controller, 'update'])->name('update');
            Route::delete('/{image}', [$controller, 'destroy'])->name('destroy');
        });
        Route::resource('photo-albums', PhotoAlbumController::class)->except('show');
        Route::resource('videos', VideoController::class)->except('show');
        Route::get('/photos', [PhotoController::class, 'index'])->name('photos.index');
        Route::scopeBindings()->prefix('photo-albums/{album}/photos')->name('photos.')->group(function (): void {
            $controller = PhotoController::class;
            Route::get('/create', [$controller, 'create'])->name('create');
            Route::post('/', [$controller, 'store'])->name('store');
            Route::get('/{photo}/edit', [$controller, 'edit'])->name('edit');
            Route::put('/{photo}', [$controller, 'update'])->name('update');
            Route::delete('/{photo}', [$controller, 'destroy'])->name('destroy');
        });
        Route::resource('news-categories', NewsCategoryController::class)->except('show');
        Route::resource('news', ArticleController::class)->parameters(['news' => 'article'])->except('show');
        Route::get('/news/{article}/preview', [ArticleController::class, 'preview'])->name('news.preview');
        Route::resource('tournaments', App\Http\Controllers\Admin\TournamentController::class);
        Route::patch('/tournaments/{tournament}/toggle', [App\Http\Controllers\Admin\TournamentController::class, 'toggle'])->name('tournaments.toggle');
        Route::get('/tournament-results', [TournamentResultController::class, 'index'])->name('tournament-results.index');
        Route::prefix('tournaments/{tournament}/result')->name('tournaments.result.')->group(function (): void {
            $controller = TournamentResultController::class;
            Route::get('/', [$controller, 'edit'])->name('edit');
            Route::post('/', [$controller, 'store'])->name('store');
            Route::put('/', [$controller, 'update'])->name('update');
            Route::delete('/', [$controller, 'destroy'])->name('destroy');
            Route::patch('/toggle', [$controller, 'toggle'])->name('toggle');
        });
        Route::get('/biography', [App\Http\Controllers\Admin\BiographyController::class, 'edit'])->name('biography.edit');
        Route::put('/biography', [App\Http\Controllers\Admin\BiographyController::class, 'update'])->name('biography.update');
        foreach (['milestones', 'achievements', 'statistics'] as $type) {
            Route::prefix($type)->name($type.'.')->group(function () use ($type): void {
                $controller = CareerRecordController::class;
                Route::get('/', [$controller, 'index'])->defaults('type', $type)->name('index');
                Route::get('/create', [$controller, 'create'])->defaults('type', $type)->name('create');
                Route::post('/', [$controller, 'store'])->defaults('type', $type)->name('store');
                Route::get('/{record}/edit', [$controller, 'edit'])->whereNumber('record')->defaults('type', $type)->name('edit');
                Route::put('/{record}', [$controller, 'update'])->whereNumber('record')->defaults('type', $type)->name('update');
                Route::patch('/{record}/toggle', [$controller, 'toggle'])->whereNumber('record')->defaults('type', $type)->name('toggle');
                Route::delete('/{record}', [$controller, 'destroy'])->whereNumber('record')->defaults('type', $type)->name('destroy');
            });
        }
        Route::resource('homepage', HomepageSectionController::class)
            ->parameters(['homepage' => 'homepageSection'])->except('show');
        Route::patch('/homepage/{homepageSection}/toggle', [HomepageSectionController::class, 'toggle'])
            ->name('homepage.toggle');
        Route::get('/modules/{module}', ModuleController::class)->name('modules.show');
        Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
    });
});
