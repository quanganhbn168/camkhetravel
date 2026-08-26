<?php

use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\Frontend\AboutController;
use App\Http\Controllers\Frontend\BniHandoverController;
use App\Http\Controllers\Frontend\BniArticleController;
use App\Http\Controllers\Frontend\BniInteractionController;
use App\Http\Controllers\Frontend\BniInvitationController;
use App\Http\Controllers\Frontend\BniMemberAuthController;
use App\Http\Controllers\Frontend\BniPickleballController;
use App\Http\Controllers\Frontend\CommentController;
use App\Http\Controllers\Frontend\ContactController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\LandingController;
use App\Http\Controllers\Frontend\PostController;
use App\Http\Controllers\Frontend\PricingController;
use App\Http\Controllers\Frontend\ProjectController;
use App\Http\Controllers\Frontend\PublicSlugController;
use App\Http\Controllers\Frontend\SearchController;
use App\Http\Controllers\LegacyContentController;
use App\Http\Controllers\SeoController;
use App\Http\Middleware\SetFrontendLocale;
use App\Support\Localization\LanguageCatalog;
use App\Support\Localization\LocalizedUrl;
use App\Settings\WebsiteSettings;
use App\Support\Media\MediaUrl;
use Awcodes\Curator\Models\Media;
use Illuminate\Support\Facades\Route;

$localizedLocalePattern = app(LanguageCatalog::class)
    ->active()
    ->reject(fn ($language): bool => $language->is_default)
    ->pluck('code')
    ->map(fn (string $code): string => preg_quote($code, '/'))
    ->implode('|');

Route::get('/favicon.ico', function (WebsiteSettings $website) {
    $mediaId = $website->favicon_media_id;
    $faviconUrl = MediaUrl::versioned(Media::query()->find($mediaId));

    if (! $faviconUrl) {
        return response()->file(public_path('favicon.ico'), [
            'Cache-Control' => 'public, max-age=604800',
        ]);
    }

    return redirect()->away($faviconUrl, 302, [
        'Cache-Control' => 'no-store, max-age=0',
    ]);
});

Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('seo.sitemap');
Route::get('/robots.txt', [SeoController::class, 'robots'])->name('seo.robots');

Route::middleware(SetFrontendLocale::class)->group(function (): void {
    Route::get('/', HomeController::class)->name('home');
    Route::get('/dich-vu', [LandingController::class, 'index'])->name('services.index');
    Route::get('/dich-vu/danh-muc/{category:slug}', [LandingController::class, 'category'])->name('services.category');
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
    Route::post('/binh-luan/dich-vu/{landing:id}', [CommentController::class, 'storeLanding'])->middleware('throttle:frontend-comment')->name('landings.comments.store');
    Route::post('/binh-luan/du-an/{project:id}', [CommentController::class, 'storeProject'])->middleware('throttle:frontend-comment')->name('projects.comments.store');
    Route::get('/tin-tuc', [PostController::class, 'index'])->name('posts.index');
    Route::get('/tin-tuc/danh-muc/{slug}', [PostController::class, 'categoryBySlug'])->name('posts.category');
    Route::get('/tin-tuc/{slug}', [PostController::class, 'showBySlug'])->name('posts.show');
});

Route::prefix('{locale}')
    ->where(['locale' => $localizedLocalePattern ?: '(?!)'])
    ->middleware(SetFrontendLocale::class)
    ->name('localized.')
    ->group(function (): void {
        Route::get('/', HomeController::class)->name('home');
        Route::get('/dich-vu', [LandingController::class, 'index'])->name('services.index');
        Route::get('/dich-vu/danh-muc/{category:slug}', [LandingController::class, 'category'])->name('services.category');
        Route::get('/tim-kiem', SearchController::class)->name('search');
        Route::get('/du-an', [ProjectController::class, 'index'])->name('projects.index');
        Route::get('/du-an/danh-muc/{slug}', [ProjectController::class, 'categoryBySlug'])->name('projects.category');
        Route::get('/du-an/{slug}', [ProjectController::class, 'showBySlug'])->name('projects.show');
        Route::get('/bang-gia', [PricingController::class, 'index'])->name('pricing.index');
        Route::get('/gioi-thieu', AboutController::class)->name('about');
        Route::get('/le-chuyen-giao-bni', fn (string $locale) => redirect()->route('localized.bni.handover', ['locale' => $locale], 301));
        Route::get('/le-chuyen-giao', BniHandoverController::class)->name('bni.handover');
        Route::get('/le-chuyen-giao/pickleball', [BniPickleballController::class, 'index'])->name('bni.pickleball');
        Route::post('/le-chuyen-giao/pickleball/dang-ky', [BniPickleballController::class, 'store'])->middleware('throttle:frontend-contact')->name('bni.pickleball.register');
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
        Route::post('/binh-luan/dich-vu/{landing:id}', [CommentController::class, 'storeLanding'])->middleware('throttle:frontend-comment')->name('landings.comments.store');
        Route::post('/binh-luan/du-an/{project:id}', [CommentController::class, 'storeProject'])->middleware('throttle:frontend-comment')->name('projects.comments.store');
        Route::get('/tin-tuc', [PostController::class, 'index'])->name('posts.index');
        Route::get('/tin-tuc/danh-muc/{slug}', [PostController::class, 'categoryBySlug'])->name('posts.category');
        Route::get('/tin-tuc/{slug}', [PostController::class, 'showBySlug'])->name('posts.show');
        Route::get('/{slug}', [PublicSlugController::class, 'localized'])->where('slug', '[^/]+')->name('slug.show');
    });

Route::get('/404-not-found', [LegacyContentController::class, 'show'])
    ->defaults('path', '404-not-found');
Route::get('/search', [LegacyContentController::class, 'show'])
    ->defaults('path', 'search');
Route::get('/under-construction', [LegacyContentController::class, 'show'])
    ->defaults('path', 'under-construction');
Route::get('/test', [LegacyContentController::class, 'show'])
    ->defaults('path', 'test');
Route::get('/blog/{pagination?}', function (?string $pagination = null) {
    $target = LocalizedUrl::route('posts.index');

    if ($pagination && preg_match('#^page/([1-9][0-9]*)$#', $pagination, $matches)) {
        $target .= '?page='.$matches[1];
    }

    return redirect()->to($target, 301);
})
    ->where('pagination', 'page/[1-9][0-9]*')
    ->name('legacy.archive.blog');
Route::get('/danh-muc-dich-vu/{term}', [ArchiveController::class, 'service'])
    ->where('term', 'dich-vu-va-bang-gia')
    ->name('legacy.archive.service');
Route::get('/danh-muc-du-an/{term}', [ArchiveController::class, 'portfolio'])
    ->where('term', 'anh|phong-su-cuoi|tvc-cua-hang|tvc-doanh-nghiep|video-highlight|video')
    ->name('legacy.archive.portfolio');
Route::get('/landing-cate/{term}', [ArchiveController::class, 'landing'])
    ->where('term', 'dao-tao|dich-vu-media|truyen-thong-quang-cao')
    ->name('legacy.archive.landing');
Route::get('/service/{slug}', [LandingController::class, 'legacyService'])
    ->middleware(SetFrontendLocale::class)
    ->where('slug', '[^/]+')
    ->name('legacy.service.show');

Route::get('/{slug}', PublicSlugController::class)
    ->middleware(SetFrontendLocale::class)
    ->where('slug', '[^/]+')
    ->name('slug.show');

Route::get('/{path}', [LegacyContentController::class, 'show'])
    ->where('path', '^(?!admin(?:/|$)|livewire(?:/|$)|(?:dich-vu|du-an|tin-tuc|service)(?:/|$)).+')
    ->name('legacy.content');
