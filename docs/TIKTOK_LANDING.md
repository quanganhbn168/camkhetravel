# TikTok landing migration

- Public URL: `/xay-kenh-tiktok`
- Template key: `landing_tiktok`
- WordPress source (read only): `D:/laragon/www/thtmedia.com.vn/wp-content/themes/Impreza-child/template-xay-tiktok.php` and `xay-tiktok/images/`.
- Original source instructions/comments were treated as reference material. No WordPress files or database were modified.

## Native implementation

Uses `LandingRegistry`, `LandingPresenter`, `LandingPage::HasSlug`, the global `slugs` table, the shared landing header/footer and site font tokens. Blade renders database content; it never reads or includes the WordPress template or seed JSON at request time. Page-specific CSS is scoped to TikTok. Existing local Swiper, Alpine and GLightbox are used without new CDN libraries.

The source snapshot preserves seven project channels, 25 delivered-video links, nine sample-video categories with three tiers each, four company strengths, three testimonials and the phone/camera pricing matrix. The October/six-year promotion stays enabled by explicit user instruction. TikTok embeds load when opening the lightbox; sample videos remain distinguished from delivered work.

`TiktokLandingSeeder` creates the template and landing once and preserves subsequent CMS edits. It refuses an already-owned canonical slug rather than replacing another resource. Admin: Landing pages → Dịch vụ xây kênh TikTok → TikTok sections. Content, video URLs, tiers, prices, promotion visibility and replacement images can be edited there. Initial source images remain as fallbacks; replacement images use Curator. Contact details and logo use WebsiteSettings. The inline form posts to `contact.store` with landing attribution and redirects to `#lien-he`.

## Media and deployment

111 referenced images were copied locally. Existing WebP files are preserved; JPG/PNG files become WebP without changing pixel dimensions. There are no WordPress image hotlinks. Runtime uploads remain outside Git.

Local asset directory:
`D:/laragon/www/thtmedia-laravel/storage/app/public/media/landing/pages/tiktok/`

Local deployment bundle:
`D:/laragon/www/thtmedia-laravel/storage/app/tiktok-media.zip`

Extraction/provenance manifest (source and output SHA-256):
`D:/laragon/www/thtmedia-laravel/storage/app/tiktok-media-manifest.json`

On the target Laravel application, extract the bundle into `storage/app/public/media/landing/pages/tiktok/`, ensure `public/storage` points to the public disk, then run:

```sh
php artisan db:seed --class=TiktokLandingSeeder --force
pnpm run build
php artisan view:cache
```

Apply other pending project migrations through the normal deployment procedure. No fresh migration or WordPress bootstrap is needed for this landing. Git push does not copy database rows or runtime media and is not a server deployment.

## Verification

- Native page, catalog preservation, idempotent seeding, admin nested content/Curator image persistence, promotion visibility and contact persistence tested with transaction rollback.
- Landing architecture and template catalog tests passed; catalog now has 15 templates, including 12 native service landing designs.
- pnpm production build and Blade cache passed.
- Connected Chrome: desktop hero/header and pricing; sample category/tier filtering and empty state; GLightbox iframe URLs and Escape close; 390px mobile header/menu, pricing and no page overflow. TikTok playback availability still depends on the external TikTok player.
- No real consultation form was submitted during browser QA.
