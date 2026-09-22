# Ledger — docs/superpowers/plans/2026-09-22-giai-phap-va-noi-dung-website.md

- User approved inline execution and revised homepage: centered title/description, horizontal building tabs, per-solution background and detail link.
- Ruling: no Git/worktree/scripts that invoke Git, per explicit project restriction. Preserve About work. No fresh local.
- Ruling: current task is Solutions; service image/task1 remains separate, do not bundle unrelated service edits.
- Ruling: use seo_image_media_id (existing HasSeoImage/SeoFields convention) instead of proposed og_image_media_id; independent from banner and cover.
- Pre-flight: Solution model → controller/resource/seeder shared contract: is_active/is_home, short_title, highlights (list of strings), curatorMedia/bannerMedia/seoImageMedia, HasSlug, published().
- Pre-flight: canonical is solutions.show with solution slug; index fixed system page remains unchanged.
- Started data, frontend, admin tests before implementation.
- Data/frontend/admin/menu complete: 69 tests/568 assertions passed before final review. Local targeted create migration and SolutionSeeder/SolutionPermissionsSeeder applied; no fresh.
- User revised visual layout via screenshot: vertical left navigation, cover-image background center with copy at bottom, white highlights/link column right. Overrides centered horizontal layout.
- Review important: raw rich-editor HTML; reproduction test failed, sanitizer added in controller.
- Review mobile long labels: wrapping included as part of new vertical layout.
- Deferred minor: draft edit page preview still opens public 404; public drafts remain intentionally inaccessible.
- Final important fix verified: HTML sanitizer RED→GREEN; entire suite 71 passed / 575 assertions. Build exit 0 (existing Sass deprecation warnings).
- Browser: all five solution tabs have distinct cover URLs and correct detail links; detail opened successfully. Revised vertical screenshot layout verified with Kho bãi image and white right column. Admin list/menu/form 2:1 verified, title copy action produced success notice. No Git used.
