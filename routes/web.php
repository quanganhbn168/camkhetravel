<?php

use App\Http\Controllers\Frontend\AboutController;
use App\Http\Controllers\Frontend\BniArticleController;
use App\Http\Controllers\Frontend\BniHandoverController;
use App\Http\Controllers\Frontend\BniGalleryController;
use App\Http\Controllers\Frontend\BniInteractionController;
use App\Http\Controllers\Frontend\BniInvitationController;
use App\Http\Controllers\Frontend\BniMemberAuthController;
use App\Http\Controllers\Frontend\BniManifestController;
use App\Http\Controllers\Frontend\BniPickleballController;
use App\Http\Controllers\Frontend\CommentController;
use App\Http\Controllers\Frontend\ContactController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\LandingController;
use App\Http\Controllers\Frontend\LandingTrackingController;
use App\Http\Controllers\Frontend\PostController;
use App\Http\Controllers\Frontend\PricingController;
use App\Http\Controllers\Frontend\ProjectController;
use App\Http\Controllers\Frontend\PublicSlugController;
use App\Http\Controllers\Frontend\SearchController;
use App\Http\Controllers\Frontend\ServiceController;
use App\Http\Controllers\SeoController;
use App\Http\Middleware\SetFrontendLocale;
use App\Settings\WebsiteSettings;
use App\Support\Branding\FaviconService;
use Awcodes\Curator\Models\Media;
use Illuminate\Support\Facades\Route;

Route::get('/favicon.ico', function (WebsiteSettings $website, FaviconService $favicons) {
    $mediaId = $website->favicon_media_id;
    $favicon = Media::query()->find($mediaId);

    return response()->file($favicons->primaryPath($favicon), [
        'Content-Type' => $favicons->primaryMimeType($favicon),
        'Cache-Control' => 'public, max-age=604800',
    ]);
});

Route::get('/bni-manifest', BniManifestController::class)->name('bni.manifest');

Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('seo.sitemap');
Route::get('/robots.txt', [SeoController::class, 'robots'])->name('seo.robots');
Route::post('/landing-page/{landingPage:id}/track', LandingTrackingController::class)
    ->middleware('throttle:landing-tracking')
    ->name('landing-pages.track');

Route::middleware(SetFrontendLocale::class)->group(function (): void {
    Route::get('/', HomeController::class)->name('home');
    Route::get('/dich-vu', [ServiceController::class, 'index'])->name('services.index');
    Route::get('/dich-vu/danh-muc/{category:slug}', [ServiceController::class, 'category'])->name('services.category');
    Route::get('/tim-kiem', SearchController::class)->name('search');
    Route::get('/du-an', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('/du-an/danh-muc/{slug}', [ProjectController::class, 'categoryBySlug'])->name('projects.category');
    Route::get('/du-an/{slug}', [ProjectController::class, 'showBySlug'])->name('projects.show');
    Route::get('/bang-gia', [PricingController::class, 'index'])->name('pricing.index');
    Route::get('/gioi-thieu', AboutController::class)->name('about');
    Route::get('/le-chuyen-giao-bni', fn () => redirect()->route('bni.handover', status: 301));
    Route::get('/le-chuyen-giao', BniHandoverController::class)->name('bni.handover');
    Route::get('/le-chuyen-giao/pickleball', [BniPickleballController::class, 'index'])->name('bni.pickleball');
    Route::post('/le-chuyen-giao/pickleball/dang-ky', [BniPickleballController::class, 'store'])->middleware('throttle:frontend-contact')->name('bni.pickleball.register');
    Route::get('/le-chuyen-giao/thu-vien-anh', [BniGalleryController::class, 'index'])->name('bni.gallery.index');
    Route::post('/le-chuyen-giao/thu-vien-anh/gui-anh', [BniGalleryController::class, 'store'])->middleware('throttle:frontend-contact')->name('bni.gallery.store');
    Route::get('/le-chuyen-giao/thu-vien-anh/{galleryItem}', [BniGalleryController::class, 'show'])->name('bni.gallery.show');
    Route::post('/le-chuyen-giao/thu-vien-anh/{galleryItem}/binh-luan', [BniGalleryController::class, 'comment'])->middleware('throttle:frontend-comment')->name('bni.gallery.comments.store');
    Route::get('/le-chuyen-giao/thu-moi', [BniInvitationController::class, 'template'])->name('bni.invitations.template');
    Route::post('/le-chuyen-giao/thu-moi/rsvp', [BniInvitationController::class, 'templateRsvp'])->middleware('throttle:frontend-contact')->name('bni.invitations.template.rsvp');
    Route::get('/le-chuyen-giao/thu-moi/{invitation}', [BniInvitationController::class, 'show'])->name('bni.invitations.show');
    Route::post('/le-chuyen-giao/thu-moi/{invitation}/rsvp', [BniInvitationController::class, 'rsvp'])->middleware('throttle:frontend-contact')->name('bni.invitations.rsvp');
    Route::get('/le-chuyen-giao/tin-tuc/{article}', [BniArticleController::class, 'show'])->name('bni.articles.show');
    Route::post('/le-chuyen-giao/tin-tuc/{article}/binh-luan', [BniInteractionController::class, 'comment'])->middleware(['auth', 'throttle:frontend-comment'])->name('bni.articles.comments.store');
    Route::post('/le-chuyen-giao/tin-tuc/{article}/cam-xuc', [BniInteractionController::class, 'react'])->middleware(['auth', 'throttle:frontend-comment'])->name('bni.articles.reactions.store');
    Route::middleware('guest')->group(function (): void {
        Route::get('/le-chuyen-giao/dang-nhap', [BniMemberAuthController::class, 'create'])->name('bni.member.login');
        Route::post('/le-chuyen-giao/dang-nhap', [BniMemberAuthController::class, 'store'])->middleware('throttle:login')->name('bni.member.login.store');
    });
    Route::post('/le-chuyen-giao/dang-xuat', [BniMemberAuthController::class, 'destroy'])->middleware('auth')->name('bni.member.logout');
    Route::get('/lien-he', [ContactController::class, 'index'])->name('contact');
    Route::post('/lien-he', [ContactController::class, 'store'])->middleware('throttle:frontend-contact')->name('contact.store');
    Route::post('/binh-luan/{post:id}', [CommentController::class, 'store'])->middleware('throttle:frontend-comment')->name('comments.store');
    Route::post('/binh-luan/dich-vu/{service:id}', [CommentController::class, 'storeService'])->middleware('throttle:frontend-comment')->name('services.comments.store');
    Route::post('/binh-luan/landing-page/{landingPage:id}', [CommentController::class, 'storeLandingPage'])->middleware('throttle:frontend-comment')->name('landing-pages.comments.store');
    Route::post('/binh-luan/du-an/{project:id}', [CommentController::class, 'storeProject'])->middleware('throttle:frontend-comment')->name('projects.comments.store');
    Route::get('/tin-tuc', [PostController::class, 'index'])->name('posts.index');
    Route::get('/tin-tuc/danh-muc/{slug}', [PostController::class, 'categoryBySlug'])->name('posts.category');
    Route::get('/tin-tuc/{slug}', [PostController::class, 'showBySlug'])->name('posts.show');
});

Route::get('/{slug}', PublicSlugController::class)
    ->middleware(SetFrontendLocale::class)
    ->where('slug', '[^/]+')
    ->name('slug.show');
