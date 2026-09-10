<?php

use App\Http\Controllers\Frontend\AboutController;
use App\Http\Controllers\Frontend\CommentController;
use App\Http\Controllers\Frontend\ContactController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\PostController;
use App\Http\Controllers\Frontend\PricingController;
use App\Http\Controllers\Frontend\ProjectController;
use App\Http\Controllers\Frontend\PublicSlugController;
use App\Http\Controllers\Frontend\SearchController;
use App\Http\Controllers\Frontend\ServiceController;
use App\Http\Controllers\IntroController;
use App\Http\Controllers\SeoController;
use App\Http\Middleware\SetFrontendLocale;
use App\Support\Branding\FaviconService;
use Illuminate\Support\Facades\Route;

Route::get('/bai-gioi-thieu/{slug}', IntroController::class)->name('intros.show');

Route::get('/favicon.ico', function (FaviconService $favicons) {
    return response()->file($favicons->primaryPath(), [
        'Content-Type' => $favicons->primaryMimeType(),
        'Cache-Control' => 'public, max-age=604800',
    ]);
});

Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('seo.sitemap');
Route::get('/robots.txt', [SeoController::class, 'robots'])->name('seo.robots');

Route::middleware(SetFrontendLocale::class)->group(function (): void {
    Route::get('/', HomeController::class)->name('home');
    Route::get('/dich-vu', [ServiceController::class, 'index'])->name('services.index');
    Route::get('/dich-vu/danh-muc/{category:slug}', [ServiceController::class, 'redirectCategory']);
    Route::get('/dich-vu/{category:slug}', [ServiceController::class, 'category'])->name('services.category');
    Route::get('/tim-kiem', SearchController::class)->name('search');
    Route::get('/du-an', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('/du-an/danh-muc/{slug}', [ProjectController::class, 'categoryBySlug'])->name('projects.category');
    Route::get('/du-an/{slug}', [ProjectController::class, 'showBySlug'])->name('projects.show');
    Route::get('/bang-gia', [PricingController::class, 'index'])->name('pricing.index');
    Route::get('/gioi-thieu', AboutController::class)->name('about');
    Route::get('/lien-he', [ContactController::class, 'index'])->name('contact');
    Route::post('/lien-he', [ContactController::class, 'store'])->middleware('throttle:frontend-contact')->name('contact.store');
    Route::post('/binh-luan/{post:id}', [CommentController::class, 'store'])->middleware('throttle:frontend-comment')->name('comments.store');
    Route::post('/binh-luan/dich-vu/{service:id}', [CommentController::class, 'storeService'])->middleware('throttle:frontend-comment')->name('services.comments.store');
    Route::post('/binh-luan/landing-page/{landingPage:id}', [CommentController::class, 'storeLandingPage'])->middleware('throttle:frontend-comment')->name('landing-pages.comments.store');
    Route::post('/binh-luan/du-an/{project:id}', [CommentController::class, 'storeProject'])->middleware('throttle:frontend-comment')->name('projects.comments.store');
    Route::get('/tin-tuc', fn () => redirect()->route('posts.index', status: 301));
    Route::get('/tin-tuc/danh-muc/{slug}', fn (string $slug) => redirect()->route('posts.category', ['slug' => $slug], status: 301));
    Route::get('/tin-tuc/{slug}', [PostController::class, 'showBySlug']);
    Route::get('/blog', [PostController::class, 'index'])->name('posts.index');
    Route::get('/blog/danh-muc/{slug}', [PostController::class, 'categoryBySlug'])->name('posts.category');
    Route::get('/blog/{slug}', [PostController::class, 'showBySlug'])->name('posts.show');
});

Route::get('/{slug}', PublicSlugController::class)
    ->middleware(SetFrontendLocale::class)
    ->where('slug', '[^/]+')
    ->name('slug.show');
