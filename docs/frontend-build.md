# DVTEC frontend build

## Asset ownership

- The public website loads `resources/scss/frontend.scss` and `resources/js/app.js` through Vite.
- Bootstrap 5.3.8 owns public components. Native component/page styles preserve the existing design; public templates no longer require Tailwind utilities or its compiler directives.
- Page-only styles live in `resources/css/frontend/pages/` and are requested by the matching Blade template, not every visit.
- Filament keeps its own Tailwind theme at `resources/css/filament/admin/theme.css`. Do not import that theme into a public layout or remove the Tailwind build dependencies.
- The old public `resources/css/app.css` entry has been removed, including its obsolete imports. All three public layouts and the landing stylesheet helper use the new entry.

## Local development and production assets

Use the repository package manager, pnpm 10.15.0, and a Node version supported by the locked Vite release (CI uses Node 22).

```bash
pnpm install --frozen-lockfile
pnpm dev
```

Production verification:

```bash
pnpm test:frontend
pnpm test:sass
pnpm build
php artisan view:cache
```

`pnpm build` generates the Bootstrap module, compiles Vite entries, and checks the resulting asset graph. `public/build` is intentionally not tracked. Deploy its complete contents together, including `manifest.json`, when the server does not build Node assets. Never deploy `public/hot`, a local `.env`, or a test database. `php artisan view:clear` clears old compiled views after deploying changed Blade templates.

## Sass warnings fixed at source

Bootstrap 5.3.8's official SCSS still contains deprecated Sass APIs. The website does not directly import that legacy SCSS.

`scripts/prepare-bootstrap.mjs` creates `.generated/bootstrap/_bootstrap.scss` deterministically:

1. Flatten the pinned upstream import graph, preserving its order and MIT license.
2. Run the official pinned Sass migrator's module, color, and if-function migrations.
3. Preserve Bootstrap's same-module dynamic function lookup and legacy integer RGB-channel semantics.
4. Cache the result using source/script/output hashes. Never modify `node_modules`.

`frontend.scss` imports the generated module with `@use ... with (...)`. Generation runs before both `dev` and `build`; it does not rely on a lifecycle install script or a machine-specific generated file.

`pnpm test:sass` compares compiled upstream and migrated CSS under the default palette, DVTEC red palette, and a separate customized configuration. Output must be identical after removing comments, and migrated source may emit no warnings. The legacy source is compiled only as a comparison oracle; its warnings are counted in the test report.

The actual Vite build treats `import`, `global-builtin`, `color-functions`, and `if-function` deprecations as errors. There is no `quietDeps`, `silenceDeprecations`, warning logger suppression, or Sass downgrade.

Do not edit `.generated`. Before upgrading Bootstrap or sass-migrator, update the explicit version guard and run the equivalence suite. Review output changes rather than disabling the guard. This conversion layer can be removed when the selected upstream release itself passes the same warning-free build and compatibility tests.

## Theme and fonts

Build-time defaults: `resources/scss/_tokens.scss`; primary `#d71920`, hover `#ad1117`, dark `#071620`.

Existing administration design settings remain active through `SiteDesignTokens` and `BrandPalette`. They coordinate Bootstrap RGB utilities, links, controls, and button contrast with the selected website palette. This does not change the Filament admin palette.

Fonts are self-hosted WOFF2, with Latin/Vietnamese subsets only. The public website uses Be Vietnam Pro and Roboto Condensed; the admin uses Inter. Font Awesome is loaded only on public pages containing its icon classes.

## JavaScript

The public initial entry contains Alpine and the Bootstrap modules actually used: Collapse, Dropdown, Modal, Offcanvas, and Tab. There is one public Alpine initialization.

Swiper, GLightbox, AOS, Font Awesome styles, and SweetAlert are separate dynamic modules. Hero slide data and playback behavior remain; dependencies are loaded when their matching elements or form interactions exist. The admin does not load this public entry.

The header uses a desktop dropdown, mobile offcanvas with nested collapse, and an accessible search modal. Navigation links remain separate from submenu toggle controls. The footer, article layouts, pagination, contact forms, and landing blocks retain their server-side data flow.

## Tests and safety

The frontend CI workflow builds assets, verifies their ownership, runs the Sass equivalence suite, compiles Blade, runs PHP tests, and exercises seeded public routes/interactions in desktop/mobile Chromium.

`tests/Browser/seed.php` is ONLY for a disposable testing database. It requires both `APP_ENV=testing` and `DVTEC_BROWSER_FIXTURES=1`. Never run it on development/customer/production data. Browser fixtures are explicitly synthetic and are not client testimonials or project references.

Reports and screenshots are stored as CI artifacts; generated assets, screenshots, fixtures, and test reports are not application source. Production deployment is not performed by the test workflow.
