# DVTEC Bootstrap Frontend and CMS Simplification Design

## Objective

Turn DVTEC into a lean Vietnamese corporate website for services, projects,
solutions, and products. The public frontend uses Bootstrap 5 only. The admin
keeps only the content operations required to run that site. Public URLs and
the global slug system remain stable.

## Approved scope

Keep:

- Services, projects, products, posts, categories, FAQ, partners, hero slides,
  about/team content, contact requests, menus, SEO metadata, and Curator media.
- Public comments, their forms, moderation resource, request validation, and
  `frontend-comment` rate limit.
- The global `SlugObserver` and global slug routes.
- Curator as the single media library. The current composer dependencies do not
  contain Spatie Media Library or its Filament media plugin; neither will be
  introduced.
- Spatie Settings and Permission, because website settings and Filament Shield
  depend on them. They are not media packages.

Remove:

- Landing-page builder, landing-page blocks, landing-page pricing, landing-page
  relations, routes, controller, resource, models, tests, settings, and media
  references.
- Service pricing and public pricing page, including packages, package items,
  importer, admin resources, routes, controller, tests, and schema.
- Internal landing tracking, including script injection, settings, provider,
  form fields, partials, tests, and related schema.
- Deprecated home featured-services partial/CSS and the three obsolete Filament
  redirect pages that only forward to `ManageSettings`.
- `ContentSeoFallbackObserver` and `AssignNextOrderObserver`. SEO fallbacks are
  already provided at display time; ordering remains an explicit admin field.

No destructive schema reset is allowed. Removal uses forward migrations that
drop only approved, empty feature tables and foreign keys after verifying the
current production-equivalent data state.

## Public frontend architecture

`resources/css/app.css` becomes the public shared entry point. It imports
Bootstrap CSS, shared DVTEC variables, typography, layout shell, header,
footer, buttons, forms, cards, and responsive defaults. It must not import
Tailwind or PCCC CSS.

`resources/js/app.js` becomes the public shared entry point. It contains only
the JavaScript needed by the shared shell and contact forms. Axios is removed:
the existing lead form uses `fetch` and no source code uses Axios.

Each page or feature gets a named Vite entry only when it needs dedicated CSS
or JavaScript. Entries use clear paths, for example:

- `resources/css/pages/home.css` and `resources/js/pages/home.js`
- `resources/css/pages/resource-detail.css` and `resources/js/pages/gallery.js`
- `resources/css/pages/article.css` and `resources/js/pages/article.js`

Page entries are included only by the page that uses them through the existing
Blade asset stack. The Vite configuration registers every real entry; it does
not register a generic `frontend.scss` bundle.

The public shell, header, footer, floating actions, contact form, listings,
and details migrate from Tailwind/Alpine/PCCC classes to Bootstrap markup plus
small DVTEC component classes. Bootstrap components replace simple tabs,
collapse, carousel, toast, and modal behavior. Swiper, AOS, SweetAlert, and
Alpine are removed from public assets when their final callers have migrated.
GLightbox may remain as a page-only gallery asset if Bootstrap's modal is not
suitable for the existing galleries.

Tailwind remains only in the Filament Vite theme, where Filament and Curator
currently require it. It is not a public-frontend dependency after the
migration.

## Homepage ownership

The homepage remains a compact business presentation: hero, service/solution
overview, projects, products, proof points, FAQ, partners, and consultation.
Business copy and list data must not be assembled in `home.blade.php`.

Existing relational data is preferred for services, projects, products, hero,
FAQ, partners, and posts. A small, explicit `HomepageSettings` schema may own
only the few curated static blocks that cannot be derived from those models.
The controller or a dedicated presenter prepares all data; Blade renders it.

## Admin information architecture

The Filament navigation has five groups:

1. Content: services, service categories, projects, project categories,
   products, product categories, posts, post categories, FAQ, and about/team.
2. Homepage: hero slides, partners, testimonials, and homepage settings.
3. Contacts: consultation requests and comments.
4. Website: one settings page, menus, and Curator media.
5. System: roles and permissions.

Landing, pricing, tracking, language-management, tag-management, and redirect
administration are removed or hidden according to the approved feature cleanup.
There is no support ticketing, CRM, or duplicate settings screen. `ManageSettings`
is the one website settings page.

## Data and URL guarantees

- `/dich-vu`, `/du-an`, `/san-pham`, `/blog`, `/gioi-thieu`, `/lien-he`, their
  existing item URLs, and comments remain public.
- The `slugs` table and `SlugObserver` remain. A service URL remains owned by
  the service, never redirected to unrelated content.
- Curator data and its media files remain untouched.
- Before each migration, the implementation verifies that each to-be-dropped
  feature table has no production content and checks foreign-key dependencies.

## Verification

Each implementation phase includes focused feature tests, public-route smoke
tests, `php artisan view:cache`, `pnpm run build`, and `git diff --check`.
The full test suite must be green. The existing
`HomeFeaturedServicesTest` is migrated to assert the current Bootstrap
homepage's data contract instead of deleted PCCC copy. Desktop and mobile
browser checks confirm the shared Bootstrap shell, navigation, forms, and
gallery behavior.
