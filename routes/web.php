<?php

use App\Http\Controllers\Frontend\AboutController;
use App\Http\Controllers\Frontend\CommentController;
use App\Http\Controllers\Frontend\ContactController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\PostController;
use App\Http\Controllers\Frontend\ProductController;
use App\Http\Controllers\Frontend\PublicSlugController;
use App\Http\Controllers\Frontend\SearchController;
use App\Http\Controllers\Frontend\ServiceController;
use App\Http\Controllers\Frontend\SolutionsController;
use App\Http\Controllers\IntroController;
use App\Http\Controllers\SeoController;
use Illuminate\Support\Facades\Route;

Route::get('/bai-gioi-thieu/{slug}', IntroController::class)->name('intros.show');

Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('seo.sitemap');
Route::get('/robots.txt', [SeoController::class, 'robots'])->name('seo.robots');

Route::group([], function (): void {
    Route::get('/', HomeController::class)->name('home');
    Route::get('/dich-vu', [ServiceController::class, 'index'])->name('services.index');
    Route::get('/giai-phap', SolutionsController::class)->name('solutions.index');
    Route::get('/giai-phap/{solution}', [SolutionsController::class, 'show'])->name('solutions.show');
    Route::get('/dich-vu/danh-muc/{category:slug}', [ServiceController::class, 'redirectCategory']);
    Route::get('/dich-vu/{category:slug}', [ServiceController::class, 'category'])->name('services.category');
    Route::get('/tim-kiem', SearchController::class)->name('search');
    Route::get('/san-pham', [ProductController::class, 'index'])->name('products.index');
    Route::get('/san-pham/danh-muc/{slug}', [ProductController::class, 'categoryBySlug'])->name('products.category');
    Route::get('/san-pham/{slug}', [ProductController::class, 'showBySlug'])->name('products.show');
    Route::get('/gioi-thieu', AboutController::class)->name('about');
    Route::get('/lien-he', [ContactController::class, 'index'])->name('contact');
    Route::post('/lien-he', [ContactController::class, 'store'])->middleware('throttle:frontend-contact')->name('contact.store');
    Route::post('/binh-luan/{post:id}', [CommentController::class, 'store'])->middleware('throttle:frontend-comment')->name('comments.store');
    Route::post('/binh-luan/dich-vu/{service:id}', [CommentController::class, 'storeService'])->middleware('throttle:frontend-comment')->name('services.comments.store');
    Route::get('/tin-tuc', fn () => redirect()->route('posts.index', status: 301));
    Route::get('/tin-tuc/danh-muc/{slug}', fn (string $slug) => redirect()->route('posts.category', ['slug' => $slug], status: 301));
    Route::get('/tin-tuc/{slug}', [PostController::class, 'showBySlug']);
    Route::get('/blog', [PostController::class, 'index'])->name('posts.index');
    Route::get('/blog/danh-muc/{slug}', [PostController::class, 'categoryBySlug'])->name('posts.category');
    Route::get('/blog/{slug}', [PostController::class, 'showBySlug'])->name('posts.show');
});

Route::get('/{slug}', PublicSlugController::class)
    ->where('slug', '[^/]+')
    ->name('slug.show');
