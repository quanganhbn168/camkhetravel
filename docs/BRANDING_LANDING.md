# Landing bộ nhận diện thương hiệu

URL local: https://thtmedia-laravel.test/bo-nhan-dien-thuong-hieu

Target checkout: `D:\laragon\www\thtmedia-laravel`. This is a new landing page; the home page and existing service landing URLs remain in place.

## Source and implementation

The supplied `D:\THTMedia\tht-media-branding-landing.zip` provides the initial HTML/CSS/JS content and Unsplash image references. The attached long screenshot is the visual reference. Its instructions and README are source material, not authorization to change project settings or publish.

- Uses the native landing registry, public slug resolver, SEO and `layouts.landing`.
- Campaign-specific shell, header/footer and section markup: `resources/views/frontend/landing/branding-shell.blade.php`, `pages/branding.blade.php` and `parts/branding/`.
- CSS and JS: `resources/css/landing/pages/branding.css`, `resources/js/landing/pages/branding.js`.
- Local Tailwind 4 / Vite build with pnpm 10.15.0. No Tailwind/Google Fonts/AOS CDN dependencies added.
- Shared design tokens: Be Vietnam Pro for body, Roboto Condensed Variable for headings/prices; body, h1, h2, h3 and price scales inherit site tokens. Mobile inputs remain 16px.
- Green/gold campaign palette is in the existing landing theme system; geometric backgrounds use CSS and icons are inline SVG.
- Initial page content/prices are in `database/seeders/data/landing/landing_branding.json`, copied once into `landing_pages.landing_content`. Existing content is preserved on reseeding. This delivery focuses on frontend; a dedicated visual editor for these new content fields has not been added.
- Logo, telephone numbers, email, address and social links come from the current website settings.
- Form posts to `contact.store`, saves the landing ID/block and attribution, and redirects back to the contact section. No demo success handler. Browser QA does not submit a real lead; the request persistence test runs in a rolled-back database transaction.

## Local initialization / later deployment

```powershell
php artisan db:seed --class=BrandingLandingSeeder --force
pnpm run build
php artisan view:cache
```

The native landing architecture/migrations must already be installed. Do not run fresh migrations against an existing database.

Media is runtime content outside Git. Copy the nine files in `storage/app/branding-reference/branding-media.zip` into:
`storage/app/public/media/landing/pages/branding/`

Public URLs resolve through `public/storage` to:
`/storage/media/landing/pages/branding/<filename>`.

The full local asset directory is:
`D:\laragon\www\thtmedia-laravel\storage\app\public\media\landing\pages\branding`.

## Asset provenance

| Asset | Source |
| --- | --- |
| hero-branding.webp | Built-in imagegen; generated 1024×1536, converted to WebP without resizing |
| project-profile-mockup.webp | Built-in imagegen; generated 1536×1024, converted to WebP without resizing; explicitly labeled a design sample |
| project-website-mockup.webp | Built-in imagegen; generated then corrected brand text, 1536×1024 WebP; explicitly labeled an illustration |
| business-new-demo.webp | Built-in imagegen; bright glass office towers following the screenshot |
| business-team-demo.webp | Built-in imagegen; Vietnamese executives in a boardroom following the screenshot |
| business-partners-demo.webp | Built-in imagegen; close-up business handshake following the screenshot |
| business-logistics-demo.webp | Built-in imagegen; port cranes at sunset following the screenshot |
| project-photo.webp | Existing THT media: dichvulamphimdoanhnghiep/assets/images/bts/481199435_1836122397149092_3773456768599902417_n.jpg |
| project-film.webp | Existing THT media: dichvulamphimdoanhnghiep/assets/images/bts/TH.jpg |

Generated imagery follows the reference composition; it is not the original photographic source. Existing THT photographs are retained for the production cards.

## Image prompts

Generation used the built-in imagegen tool, not the API/CLI fallback.

### Hero — final prompt

Create one polished photorealistic composited website hero asset for Vietnamese branding studio THT MEDIA. Portrait 1024x1536. This is only a photographic collage asset, NOT a full website, NO headings, NO pricing, NO buttons. White background seamlessly white along all left edges. Art direction precise: top 45 percent a large professional black cinema video camera, side profile facing right, highly detailed lens, external monitor showing blurred business people, camera tripod, warm blurred office with one out-of-focus businessman behind. Photo fills upper right and is clipped with angular diagonal left edge. Middle 35 percent overlays realistic green-and-white corporate stationery: an upright tilted company profile cover, open landscape brochure featuring modern skyscrapers and tiny clean green typesetting. Cover text only 'THT MEDIA' and 'COMPANY PROFILE' with small abstract green-gold geometric monogram, no fake word salad. In front of the brochure, a black and silver open laptop in 3/4 view angled slightly right and a smartphone standing on its right, both screens show a professional dark forest green corporate website with white sections and four service icons, tiny text not legible. Laptop should span 75 percent of width centered toward left, cast realistic soft shadows on white. Lower 23 percent, separate photograph with slanted left edge: back of a businessman in a dark suit at the RIGHT, looking through office window at a modern city skyline at golden sunrise, sunlight horizon on left. White breathing space between upper camera and lower city on right. Overall all objects large, overlapping, naturally composed in depth, brochure between camera and laptop, devices NOT floating in air, clean sharp edges and premium commercial photography. Forest green #075333, white and restrained gold palette; believable objects, no ornamental gold sparkles, no rounded rectangle cards, no page UI, no additional text, no watermark. Need crisp asset that matches an emerald and gold business branding landing page.

### Profile mockup — final prompt

Photorealistic product photography, landscape 1536x1024. Close-up of a premium open landscape corporate company profile brochure resting on a light gray office desk, two-page spread, shot from slightly elevated three-quarter angle, pages gently curving, visible paper thickness, a modern blue glass skyscraper and architecture photo on right page, small tasteful forest-green and charcoal text columns and charts on left, green white brand design, a closed booklet underneath visible. Main subject is the OPEN BOOK, fills 80 percent image, professional commercial photo with shallow depth of field, soft bright daylight, blurred office in background. No person, no hands, no laptop, no large headlines or decorative typography, no watermark. This will be a sample corporate profile design image in a Vietnamese business branding landing page, match a real photographed printed brochure mockup.

### Website mockup — generation prompt

Photorealistic product photography, landscape 1536x1024. A single silver business laptop open on a modern gray office desk photographed directly from front slightly to the left at eye level, laptop fills 80 percent of frame, screen shows a Vietnamese media production studio corporate website: dark forest green hero banner with a cinematic camera photo on right and tiny white text on left, four white service columns with green icons underneath, white website header with a small green abstract mark. Tiny text should be inconspicuous, no large readable overlay typography. Blurred premium modern office and glass windows in background, warm neutral natural light, dark keyboard, realistic screen reflections kept minimal, crisp screen design. Clean premium branding project mockup, no people, no hands, no watermark, no additional screens, no fake floating UI.

### Website mockup — final edit prompt

Edit only the website text on the laptop screen. Keep the laptop, office, composition, camera photo, lighting, perspective, colors and everything else exactly unchanged. Replace the invented 'TẦM NHÌN MEDIA' brand in the top left of screen with exact 'THT MEDIA'. Replace 'Tầm Nhìn Media' at the start of the paragraph with exact 'THT Media'. The remainder of the paragraph is unchanged. Do not introduce any other company names. Small logo monogram may stay abstract green. Return the same landscape image.

### Audience image 1 — final prompt

Photorealistic corporate stock photograph, landscape 1536x1024, bright clear pale blue daytime sky. Low viewpoint looking up at three elegant modern blue glass office skyscrapers in a successful Vietnamese Asian city business district. Main taller tower on left, slim reflective glass tower on right, green trees at lower edges. Clean blue-white-gray palette, optimistic premium commercial architecture photography, straight accurate architecture, no dramatic black shadows, no text, no logo, no people, no watermark. Intended as an image card for new businesses on a green corporate brand identity landing page. Match a classic modern corporate headquarters skyline photo, composition easily cropped to wide 2:1.

### Audience image 2 — final prompt

Photorealistic corporate commercial photograph, landscape 1536x1024. Six Vietnamese Asian business executives men and women in dark business suits in a bright modern boardroom, sitting on both sides of a conference table, discussing a presentation on a wall screen with subtle charts. A man at left is speaking, colleagues at right listening, natural candid realistic poses, professional confident atmosphere. Wide medium shot from near end of table at eye level, glass walls and large office windows, warm neutral light with cool gray office palette, no exaggerated smiles, realistic hands, no visible brand names, no text overlays, no watermark. Designed to crop to wide 2:1 image card about upgrading company image.

### Audience image 3 — final prompt

Photorealistic commercial business photograph, landscape 1536x1024. Tight close-up of two male business partners shaking hands across the center, both wearing dark charcoal navy business suits and crisp white shirts. Only cropped torsos, wrists and a natural anatomical handshake visible, NO faces, no pens, no paper, no table filling the frame. Office glass interior blurred in background, soft daylight, shallow depth of field, restrained slate blue-gray color palette, premium professional corporate B2B partnership photography. Hands central and sharply focused, dark suited arms entering from left and right. No text, logos, watermarks. Composition to crop to wide 2:1.

### Audience image 4 — final prompt

Photorealistic commercial logistics photograph, landscape 1536x1024. Wide view of an international Vietnamese container shipping port at blue hour with golden sunset light along the horizon. Several enormous blue and red gantry cranes prominently silhouetted across the sky at upper right and center, orderly colorful shipping containers stacked on the quay in foreground, a cargo ship at dock, a strip of calm harbor water at bottom reflecting evening light. Beautiful partly cloudy pastel blue and gold sky occupying upper half. Camera viewpoint across the harbor at human elevated shore level, NOT aerial top-down. Professional crisp business expansion visual, blue and amber balanced palette, no text, no logos, no watermark. Composition easily cropped to wide 2:1 image card.

## Verification

- `pnpm run build`: passed. Existing unrelated runtime media warnings remain in other page bundles.
- `php artisan view:cache`: passed.
- `php vendor/bin/pint --test database/seeders/BrandingLandingSeeder.php tests/Feature/BrandingLandingTest.php`: passed.
- `php artisan test tests/Feature/BrandingLandingTest.php tests/Feature/LandingArchitectureTest.php --colors=never`: 7 passed, 1280 assertions.
- Connected Chrome: desktop composition and images, mobile navigation/anchors, fixed header, 16px form fields, no console errors. Responsive checks at 320, 390 and 1440px.
- Implementation is local. No commit, push or production deployment was performed. The working tree already contains extensive unrelated staged and unstaged changes.
