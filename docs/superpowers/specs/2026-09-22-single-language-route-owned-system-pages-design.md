# Single-Language Route-Owned System Pages Design

**Date:** 2026-09-22

**Status:** Proposed for user review

## Intent

DVTEC will remain a Vietnamese-only website. Public system pages are owned by explicit Laravel named routes, while each controller remains responsible for collecting and presenting that page's business content.

The implementation will not introduce a `Page` model, `pages` table, generic page controller, or database-driven route dispatcher. A small fixed settings profile will hold only the presentation metadata needed by each agreed system route: SEO title, meta description, share image, and page banner.

## Success Criteria

- Laravel named routes are the only source of truth for system-page URLs.
- `LocalizedUrl`, `LanguageCatalog`, `SetFrontendLocale`, and the website's multilingual data structures are removed.
- The public website has no language selector, locale-prefixed route, locale fallback, or translatable content wrapper.
- The `slugs` table stores one globally unique slug per sluggable record and has no `locale` column.
- The six agreed route profiles are Trang chủ, Giới thiệu, Dịch vụ, Giải pháp, Liên hệ, and Dự án.
- Trang chủ continues to use Hero Slides and does not render a separate page banner.
- The five internal route profiles can select an independent banner, with the existing website banner as fallback.
- Every route profile can select a dedicated Open Graph image; that image remains independent from its banner and visible content images.
- Menus reference system pages by route name.
- Only new migrations are added. Existing migration history is not edited.
- `php artisan migrate:fresh --seed` produces a working Vietnamese site with baseline settings, menus, route profiles, and the existing basic domain seed data.

## Non-Goals

- No generic page builder or arbitrary administrator-created pages.
- No database-editable system-page paths.
- No runtime language switching or future-language abstraction.
- No replacement of controller-owned content with JSON page bodies.
- No replacement of Homepage Hero Slides with a single banner.
- No redesign of the six public pages beyond wiring their route profile, SEO metadata, and banner.
- No removal of Vietnamese translations required by Filament, Curator, Laravel validation, or other administrative packages.

## Route Ownership

The following named routes are the canonical owners of the agreed system pages:

| Route name | URI | Controller | Profile fields |
| --- | --- | --- | --- |
| `home` | `/` | `HomeController` | SEO title, meta description, share image |
| `about` | `/gioi-thieu` | `AboutController` | SEO title, meta description, share image, banner |
| `services.index` | `/dich-vu` | `ServiceController@index` | SEO title, meta description, share image, banner |
| `solutions.index` | `/giai-phap` | `SolutionsController` | SEO title, meta description, share image, banner |
| `contact` | `/lien-he` | `ContactController@index` | SEO title, meta description, share image, banner |
| `projects.index` | `/du-an` | `ProjectController@index` | SEO title, meta description, share image, banner |

Existing product, blog, search, category, detail, form submission, robots, sitemap, and favicon routes remain explicit Laravel routes. They are not converted into route profiles by this change.

`SolutionsController` is a normal dedicated controller. It composes the solution overview from existing published service categories, services, and related CMS data. It does not read a generic page body and does not duplicate service records.

## Fixed Route Registry

A code-owned registry defines the six supported profile keys, labels, route names, default SEO text, whether a banner is supported, sitemap frequency, sitemap priority, and reserved root slug.

The registry is configuration, not a content model. Administrators cannot add or remove route definitions. The same registry is consumed by:

- the settings form;
- the route-profile resolver;
- the menu source picker;
- the sitemap builder;
- reserved-slug validation;
- seeders and tests.

This prevents the route list from drifting across several hard-coded arrays while preserving named routes as the URL authority.

## Route Profile Settings

`WebsiteSettings` receives a `public_pages` array keyed by route name. Each fixed entry contains:

```php
[
    'seo_title' => '',
    'seo_description' => '',
    'seo_image_media_id' => null,
    'banner_media_id' => null,
]
```

The `home` profile omits or ignores `banner_media_id` because the homepage hero remains owned by `HeroSlide` records.

The admin settings screen renders fixed sections or tabs from the route registry. It does not use a repeater and does not permit arbitrary keys. Media fields use Curator IDs and validate that selected records are images.

A focused resolver accepts an allowed route name and returns normalized metadata plus resolved media URLs. Controllers request their own profile and remain responsible for all non-profile data.

Fallback rules are deterministic:

1. Profile SEO title, description, and share image.
2. Existing controller fallback title or description where applicable.
3. Website-wide SEO title, description, and share image.

For banners:

1. Profile banner for one of the five internal routes.
2. Existing website-wide `banner_media_id`.
3. Existing CSS/background fallback when neither image exists.

The share image never falls back to the page banner. It falls back only through the SEO image chain.

## Controller and View Data Flow

Each system-page controller asks the profile resolver for its own route profile, builds its current domain data, builds SEO through `FrontendSeoBuilder`, and passes `pageBannerUrl` to the view when the route supports a banner.

- `HomeController` keeps Hero Slide, homepage settings, services, projects, posts, FAQ, and schema composition.
- `AboutController` keeps `AboutSettings`, media, history, services, statistics, and video composition.
- `ServiceController@index` keeps service listing, categories, process data, projects, sorting, and pagination.
- `SolutionsController` composes its overview from existing service-domain data.
- `ContactController@index` keeps contact settings, maps, form, and branch data.
- `ProjectController@index` keeps project listing, categories, partners, sorting, and pagination.

Views render the supplied banner URL and never query settings or media records directly.

## Removing `LocalizedUrl`

All `LocalizedUrl` consumers are migrated before the class is deleted.

- System-page links use `route('name')`.
- Global single-segment content uses `route('slug.show', ['slug' => $model->slug])`.
- Projects, products, posts, and categories use their existing named routes with explicit slug parameters.
- POST actions use their existing named routes with IDs where required by the route contract.
- Menu preview links, Filament actions, canonical URLs, breadcrumbs, cards, header, footer, forms, and sitemap use the same named route contracts.

Laravel's URL generator is configured to use the `APP_URL` origin for absolute public URLs. This preserves stable canonical and sitemap hosts even when a request arrives with a different `Host` header.

The class is deleted only after a repository search shows no remaining imports, Blade `@use` statements, static calls, or tests referencing it.

## Removing Website Multilingual Support

The website becomes structurally single-language, not merely configured with one active language.

Remove:

- `LanguageCatalog`;
- `SetFrontendLocale`;
- `config/locales.php`;
- provider registration for the language catalog;
- locale middleware from public routes;
- locale-aware branches in `HasSlug`, `SlugObserver`, `PublicSlugController`, `FrontendSeoBuilder`, and `AboutController`;
- frontend `__('site.*')` lookups and the application-owned `lang/en`, `lang/ko`, `lang/zh`, and `lang/vi/site.php` dictionaries;
- application tests whose only purpose is language selection or locale-prefixed routing.

Keep:

- Laravel's required application locale configured as the fixed value `vi`;
- the fixed HTML language tag `vi`;
- the fixed Open Graph locale `vi_VN`;
- Vietnamese vendor translation files used by Filament and Curator;
- framework translation behavior needed for Vietnamese validation and administrative messages.

`AboutSettings` scalar text properties change from localized arrays to strings. Nested timeline `title` and `description` values also change from localized arrays to strings. Form paths change from fields such as `page_title.vi` to `page_title`.

## Slug Schema and Reserved Routes

Only a new Laravel database migration is added; `2026_09_11_004940_create_slugs_table.php` remains untouched.

The new migration:

1. inspects existing slug rows;
2. prefers the `vi` row when one sluggable has more than one locale row;
3. retains one deterministic row for a sluggable that has no `vi` row;
4. resolves any cross-locale slug collision deterministically before adding the new unique key;
5. removes the old locale-aware unique indexes;
6. drops the `locale` column;
7. adds a unique index on `slug`;
8. adds a unique index on `sluggable_type, sluggable_id`;
9. converts localized settings values to their Vietnamese scalar values;
10. initializes the `website.public_pages` setting when absent.

Before deployment, the migration reports or tests the collision cases against a copy of production data. It must not silently discard the preferred Vietnamese slug.

After migration, `HasSlug` resolves one related slug, `SlugObserver` creates or updates one related slug, and `PublicSlugController` resolves by `slug` alone.

The route registry supplies reserved one-segment paths. At minimum this includes all explicit public root paths that could otherwise be shadowed by `/{slug}`, including `gioi-thieu`, `dich-vu`, `giai-phap`, `lien-he`, `du-an`, `san-pham`, `blog`, and `tim-kiem`. The observer applies a numeric suffix when generated content collides with a reserved path or an existing slug.

## Menu Behavior

System-page menu items remain `native_route` entries whose `url` column stores a route name. The route profile does not participate in link generation.

The menu source picker obtains its system-page options from the fixed route registry and includes `solutions.index`. Invalid or missing route names resolve to `#` rather than a guessed URL.

Dynamic menu items keep their linked model IDs and generate links with the appropriate named route. The unused `native_page` and `pageLink()` branches are removed.

The baseline header menu is seeded in this order:

1. Trang chủ
2. Giới thiệu
3. Dịch vụ
4. Giải pháp
5. Dự án
6. Sản phẩm
7. Kiến thức
8. Liên hệ

## SEO, Open Graph, and Sitemap

`FrontendSeoBuilder` no longer depends on `LanguageCatalog` or `LocalizedUrl`.

- Canonical and breadcrumb URLs use named routes.
- `og:locale` is the fixed string `vi_VN`.
- The document language is the fixed string `vi`.
- The profile share image feeds both `og:image` and `twitter:image`.
- Existing detail-level share-image behavior for services, projects, products, posts, and categories remains unchanged.
- The system-page profile metadata overrides only its matching listing/system route.
- Sitemap generation uses named routes and adds `solutions.index` with its configured priority and frequency.
- Search and form POST routes are not added to the sitemap.

## Seeder Contract

Seeders remain normal Laravel seeders and are called explicitly from `DatabaseSeeder` in dependency order.

The settings seeders write the complete declared property set so a fresh install never fails because a setting is missing:

- `WebsiteSettingsSeeder` seeds company identity, website defaults, empty media references, and all six `public_pages` profiles.
- `HomepageSettingsSeeder` seeds usable Vietnamese homepage copy.
- `CompanySettingsSeeder` seeds safe company placeholders.
- `AboutSettingsSeeder` seeds scalar Vietnamese fields and correctly shaped timeline/media arrays.
- `MenuSeeder` seeds the header menu with named routes, including `solutions.index`.
- Existing media, taxonomy, service, project, product, post, and Hero Slide seeders continue to provide baseline demonstration data.

Seeders do not create locale arrays, locale columns, translated variants, or language records. They must be deterministic enough for `migrate:fresh --seed` and test databases.

## Migration and Deployment Safety

The worktree already contains unrelated and unfinished changes, so implementation must stage only files belonging to this feature and must not rewrite or revert user work.

Deployment order:

1. Back up the production database.
2. Inspect current slug locale counts and conflicting slug values.
3. Deploy code containing the compatibility-aware migration and new single-language implementation.
4. Run `php artisan migrate --force`.
5. Clear settings, configuration, route, and view caches.
6. Verify canonical URLs, menus, system-page banners, SEO tags, and representative dynamic slugs.

Normal production deployment does not run the foundation seeders over managed content. `migrate:fresh --seed` is for fresh/local/test installation, not an upgrade command for production.

## Test Strategy

Automated coverage must prove:

- all six named routes resolve and emit canonical URLs on the configured `APP_URL` origin;
- `/giai-phap` renders through `SolutionsController`;
- system-page menu items generate URLs through route names;
- invalid route-name menu items return `#`;
- every dynamic sluggable has one slug and the database rejects duplicate global slugs;
- generated slugs avoid all reserved root paths;
- `PublicSlugController` resolves each supported dynamic type without locale filtering;
- old locale-prefixed public URLs remain unavailable;
- About settings and timeline values are scalar after migration;
- the five internal pages render a selected banner and correctly fall back to the website banner;
- the homepage keeps its Hero Slides and does not render the internal-page banner;
- profile OG images are independent from banner images and fall back to the website share image;
- `og:locale` is `vi_VN` and `<html lang>` is `vi`;
- the sitemap includes the six agreed routes plus existing eligible content and contains no duplicate URL;
- a repository scan finds no `LocalizedUrl`, `LanguageCatalog`, `SetFrontendLocale`, locale-aware slug code, or application-owned non-Vietnamese dictionaries;
- `php artisan migrate:fresh --seed` completes and the essential settings, menu, route profiles, and baseline domain records exist;
- Blade compilation, the focused feature suite, the full test suite, frontend build, and `git diff --check` pass.

## Acceptance Boundary

The feature is complete only when the application has no Page model or page table, system routes own their URLs, the application-level multilingual layer has been removed, one new migration safely converts existing data, fresh seeding recreates the baseline site, and browser checks confirm the route menu, five internal banners, homepage Hero Slides, canonical metadata, and OG image behavior.
