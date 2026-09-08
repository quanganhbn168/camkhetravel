# Landing architecture refactor checklist

- [x] Inventory current routes, database templates, shared layout, components, page assets, and legacy layers.
- [x] Keep Academy V2 as a supported page while moving it onto the shared typography and shared header/footer markup.
- [x] Rename runtime classes, template keys, views, assets, data, CSS namespaces, and media namespace from `Landing07` to `Landing`.
- [x] Consolidate runtime routing, page metadata, asset resolution, and presentation behind the canonical Landing module.
- [x] Move landing content snapshots into database-owned `landing_content`; seed files must not be read during requests.
- [x] Remove the duplicate plain/communications runtime and obsolete source templates.
- [x] Apply the public typography system consistently: Be Vietnam Pro for body/UI and Roboto Condensed Variable for headings/display.
- [x] Remove page-level Georgia, Plus Jakarta Sans, and public Inter overrides from landing pages.
- [x] Preserve all public canonical slugs and aliases.
- [x] Migrate the local database without losing landing content, relations, pricing, or media references.
- [x] Confirm no obsolete `Landing07`, `landing07`, `landing-07`, or `tht07` references remain in runtime code/assets/tests (historical migration mappings excepted).
- [x] Pass PHP syntax checks, focused tests, full test suite, Vite production build, Blade cache, and `git diff --check`.
- [x] Verify all landing routes in a browser at desktop and mobile widths, including scroll, header/footer, modal, typography, and Academy V2.

## Verification evidence

- Registry/database: 10 native templates, 0 legacy templates, 6 `LandingPage` content records, and 4 `Service` content records.
- Routes: 19 canonical and alias URLs returned HTTP 200.
- Media: 222 relative media references checked, 0 missing.
- Tests: 18 focused tests / 1,435 assertions and 120 full-suite tests / 2,255 assertions passed.
- Frontend: Vite production build, Blade cache, PHP syntax checks, Node syntax check, and `git diff --check` passed.
- Browser: 20/20 canonical route checks passed at 1536×900 and 390×844; 6/6 representative header/footer visual checks passed.
