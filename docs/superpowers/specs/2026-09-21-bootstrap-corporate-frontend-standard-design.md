# Bootstrap Corporate Frontend Standard Design

## Objective

Create a reusable Vietnamese corporate-site frontend standard for services,
projects, solutions, and products. The public frontend uses Bootstrap 5 with a
small framework-neutral design layer. The existing Laravel, Filament, Curator,
comments, ratings, URL, and CMS contracts remain unchanged.

## Scope

This work changes public frontend assets, Blade markup, frontend tests, and
removes three unneeded inherited subsystems: Landing pages, pricing, and
tracking snippets. Landing pages include their public slug renderer, admin
resource, builder views, models, relations, policies, tests and tables. Pricing
includes the public `/bang-gia` route and service/package price catalogue. The
tracking subsystem includes its settings, provider, injected snippets and
admin form. Curator media, comments, ratings, the remaining public URLs and
their CMS content remain in place.

The current comments and ratings remain intact. They provide the approved
rating data needed for a later, separately-scoped AggregateRating structured
data implementation. This frontend standard does not add or alter SEO rating
schema.

## Naming standard

No asset path, CSS custom property, CSS class, JavaScript global, component, or
Vite entry may include a project, company, customer, brand, or domain name.
Project identity belongs in CMS data: logo, copy, configured colors, media, and
settings values.

Use neutral, semantic names:

- CSS tokens: `--color-primary`, `--color-primary-hover`, `--color-ink`,
  `--color-surface`, `--color-muted`, `--font-sans`, `--font-display`.
- Shared components: `.site-header`, `.site-footer`, `.site-container`,
  `.content-card`, `.page-hero`, `.section-heading`, `.form-status`.
- Page scopes: `.home-page`, `.service-page`, `.project-page`,
  `.product-page`, `.article-page`.
- Asset entries: `app`, `home`, `resource-detail`, `article`, `gallery`.

Do not use names such as `dv-*`, `dvtec-*`, a client abbreviation, or a domain
prefix. A project may override only the values of neutral tokens through its
configured settings or a site-specific token file.

## Public asset architecture

`resources/css/app.css` is the shared public CSS entry. It imports Bootstrap,
font declarations, neutral tokens, reset/base rules, shared shell layout,
header, footer, forms, cards, buttons, and accessibility rules. It must not
import public Tailwind utilities or legacy theme CSS.

`resources/js/app.js` is the shared public JavaScript entry. It owns only the
shared shell and generic lead-form behavior. Existing forms use `fetch`; Axios
is removed only after source verification confirms no caller remains.

Dedicated page and feature entries use predictable neutral paths:

- `resources/css/pages/home.css` and `resources/js/pages/home.js`
- `resources/css/pages/resource-detail.css` and `resources/js/pages/gallery.js`
- `resources/css/pages/article.css` and `resources/js/pages/article.js`

Each entry is loaded only by its owning page through the existing Blade asset
stack. The Vite configuration lists each real entry and has no generic
catch-all frontend bundle.

## Component and dependency rules

Public markup uses Bootstrap grid, responsive utilities, forms, tabs,
collapse, carousel, toast, and modal components before custom code. Custom CSS
exists only for visual identity and layouts Bootstrap cannot express clearly.

The migration moves shared header, footer, floating actions, forms, cards,
listing pages, and detail pages from Tailwind/legacy classes to Bootstrap plus
neutral component classes. It removes a public dependency only after every
caller has moved:

- Keep Swiper for hero, post and testimonial sliders; it already provides the
  needed responsive, keyboard and navigation behaviour. Bootstrap Carousel is
  acceptable only for a new simple single-purpose carousel that is demonstrably
  simpler than the existing Swiper implementation.
- Prefer Bootstrap Toast over SweetAlert for standard lead-form feedback.
- Prefer CSS transitions with reduced-motion handling over AOS.
- Replace public Alpine interactions with Bootstrap or small page-local
  JavaScript where practical.
- Keep GLightbox page-local only when Bootstrap Modal cannot deliver the
  existing gallery behavior.

Tailwind remains limited to the separate Filament theme, where the admin and
Curator require it. It is not included in the public application bundle.

## Page ownership and data flow

Controllers and existing presenters prepare all content data. Blade templates
render the prepared data and do not construct business lists or static content
arrays. Existing relational content is reused for services, projects, products,
posts, hero slides, FAQ, partners, testimonials, comments, and ratings.

The homepage stays a compact corporate presentation: hero, service/solution
overview, projects, products, proof points, FAQ, partners, and consultation.
Any static block that remains after the migration is either a neutral component
or explicit CMS-managed data; it is not an inline project-specific PHP array.

## Compatibility guarantees

- Existing public URLs, global slugs, form endpoints, comments, approved
  ratings, Curator media URLs, and CMS content remain unchanged.
- The shared site shell works at desktop and mobile widths without horizontal
  overflow or JavaScript errors.
- Every public page receives only its shared assets plus its own page assets.
- The standard can be copied to another corporate project by changing token
  values and CMS content rather than renaming implementation code.

## Verification

Each frontend phase includes focused feature tests, public-route smoke tests,
`php artisan view:cache`, `pnpm run build`, and `git diff --check`. The full
test suite must be green. The stale homepage test is updated to test the
current page data contract instead of removed legacy copy. Desktop and mobile
browser checks cover the shared shell, navigation, form submission feedback,
comments/rating display, and gallery behavior.
