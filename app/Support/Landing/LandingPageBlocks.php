<?php

namespace App\Support\Landing;

use App\Models\LandingPage;
use Awcodes\Curator\Models\Media;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Throwable;

class LandingPageBlocks
{
    /** @var array<string, string> */
    private const VIEWS = [
        'hero' => 'frontend.landing-pages.blocks.hero',
        'countdown' => 'frontend.landing-pages.blocks.countdown',
        'benefits' => 'frontend.landing-pages.blocks.benefits',
        'content_grid' => 'frontend.landing-pages.blocks.content-grid',
        'process' => 'frontend.landing-pages.blocks.process',
        'stats' => 'frontend.landing-pages.blocks.stats',
        'testimonials' => 'frontend.landing-pages.blocks.testimonials',
        'projects' => 'frontend.landing-pages.blocks.projects',
        'service_categories' => 'frontend.landing-pages.blocks.service-categories',
        'services' => 'frontend.landing-pages.blocks.services',
        'posts' => 'frontend.landing-pages.blocks.posts',
        'pricing' => 'frontend.landing-pages.blocks.pricing',
        'rich_text' => 'frontend.landing-pages.blocks.rich-text',
        'gallery' => 'frontend.landing-pages.blocks.gallery',
        'faqs' => 'frontend.landing-pages.blocks.faqs',
        'lead_form' => 'frontend.landing-pages.blocks.lead-form',
        'cta' => 'frontend.landing-pages.blocks.cta',
    ];

    /** @return list<array<string, mixed>> */
    public function prepare(LandingPage $landingPage): array
    {
        $sections = collect($landingPage->sections ?? [])
            ->filter(fn (mixed $section): bool => is_array($section) && isset(self::VIEWS[$section['type'] ?? '']))
            ->values();
        $media = $this->mediaFor($landingPage, $sections);

        return $sections
            ->map(function (array $section, int $index) use ($landingPage, $media): array {
                $type = (string) $section['type'];
                $data = is_array($section['data'] ?? null) ? $section['data'] : [];
                foreach (['cta_url', 'secondary_url'] as $linkField) {
                    if (array_key_exists($linkField, $data)) {
                        $data[$linkField] = $this->safeLink($data[$linkField]);
                    }
                }
                $blockId = filled($data['block_id'] ?? null)
                    ? (string) $data['block_id']
                    : $type.'-'.($index + 1);

                $prepared = [
                    'id' => $blockId,
                    'type' => $type,
                    'view' => self::VIEWS[$type],
                    'data' => $data,
                ];

                return match ($type) {
                    'hero' => $prepared + [
                        'media' => $media->get((int) ($data['media_id'] ?? 0)) ?: $landingPage->curatorMedia,
                    ],
                    'countdown' => $prepared + [
                        'starts_at' => $this->dateValue($data['starts_at'] ?? null, $landingPage->campaign_starts_at),
                        'ends_at' => $this->dateValue($data['ends_at'] ?? null, $landingPage->campaign_ends_at),
                    ],
                    'benefits' => $prepared + [
                        'items' => collect($data['items'] ?? [])->filter(fn (mixed $item): bool => is_array($item) && filled($item['title'] ?? null))->values(),
                    ],
                    'content_grid', 'process', 'stats', 'testimonials' => $prepared + [
                        'items' => collect($data['items'] ?? [])
                            ->filter(fn (mixed $item): bool => is_array($item) && filled($item['title'] ?? $item['label'] ?? null))
                            ->values(),
                    ],
                    'projects' => $prepared + [
                        'projects' => $landingPage->projects->take(max(1, min(12, (int) ($data['limit'] ?? 6)))),
                    ],
                    'service_categories' => $prepared + [
                        'categories' => $landingPage->serviceCategories->take(max(1, min(12, (int) ($data['limit'] ?? 6)))),
                    ],
                    'services' => $prepared + [
                        'services' => $landingPage->services->take(max(1, min(12, (int) ($data['limit'] ?? 6)))),
                    ],
                    'posts' => $prepared + [
                        'posts' => $landingPage->posts->take(max(1, min(12, (int) ($data['limit'] ?? 6)))),
                    ],
                    'pricing' => $prepared + [
                        'plans' => $landingPage->pricingPlans->where('is_active', true)->sortBy('sort_order')->values(),
                    ],
                    'gallery' => $prepared + [
                        'media_items' => collect($data['media_ids'] ?? [])
                            ->map(fn (mixed $id): ?Media => is_numeric($id) ? $media->get((int) $id) : null)
                            ->filter()
                            ->values(),
                    ],
                    'faqs' => $prepared + [
                        'items' => $this->faqItems($data['items'] ?? $landingPage->faq_items ?? []),
                    ],
                    default => $prepared,
                };
            })
            ->all();
    }

    /** @return array{primary: string, accent: string, surface: string, ink: string} */
    public function theme(LandingPage $landingPage): array
    {
        $settings = $landingPage->theme_settings ?? [];
        $palette = LandingTemplateRegistry::palette($landingPage->template_key);

        return [
            'primary' => $this->color($settings['primary'] ?? null, $palette['primary']),
            'accent' => $this->color($settings['accent'] ?? null, $palette['accent']),
            'surface' => $this->color($settings['surface'] ?? null, $palette['surface']),
            'ink' => $this->color($settings['ink'] ?? null, $palette['ink']),
        ];
    }

    public function templateView(LandingPage $landingPage): string
    {
        if ($landingPage->layout_mode === 'custom_template') {
            return LandingTemplateRegistry::find($landingPage->template_key)['view']
                ?? 'frontend.landing-pages.builder';
        }

        return 'frontend.landing-pages.builder';
    }

    /** @return array<string, mixed>|null */
    public function templateDefinition(LandingPage $landingPage): ?array
    {
        return $landingPage->layout_mode === 'custom_template'
            ? LandingTemplateRegistry::find($landingPage->template_key)
            : null;
    }

    /** @return array<string, mixed> */
    public function templateSettings(LandingPage $landingPage): array
    {
        $settings = array_replace(
            LandingTemplateRegistry::defaultSettings($landingPage->template_key),
            array_filter(
                is_array($landingPage->template_settings) ? $landingPage->template_settings : [],
                fn (mixed $value): bool => is_array($value)
                    ? $value !== []
                    : is_scalar($value) && filled((string) $value),
            ),
        );

        foreach ($settings as $key => $value) {
            if (str_ends_with((string) $key, '_url')) {
                $settings[$key] = filled($value) ? $this->safeLink($value, '') : '';
            }
        }

        return $settings;
    }

    /** @return array<string, Media> */
    public function templateMedia(LandingPage $landingPage): array
    {
        $settings = is_array($landingPage->template_settings) ? $landingPage->template_settings : [];
        $fields = collect(LandingTemplateRegistry::settingsSchema($landingPage->template_key)['fields'] ?? [])
            ->filter(fn (mixed $field): bool => is_array($field)
                && ($field['type'] ?? null) === 'media'
                && filled($field['key'] ?? null))
            ->mapWithKeys(fn (array $field): array => [
                (string) ($field['media_role'] ?? $field['key']) => (string) $field['key'],
            ])
            ->all();
        $ids = collect($fields)
            ->map(fn (string $field): mixed => $settings[$field] ?? null)
            ->filter(fn (mixed $id): bool => is_numeric($id))
            ->map(fn (mixed $id): int => (int) $id)
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            return [];
        }

        $media = Media::query()->whereKey($ids->all())->get()->keyBy('id');

        return collect($fields)
            ->mapWithKeys(function (string $field, string $name) use ($settings, $media): array {
                $id = $settings[$field] ?? null;
                $item = is_numeric($id) ? $media->get((int) $id) : null;

                return $item instanceof Media ? [$name => $item] : [];
            })
            ->all();
    }

    public function campaignState(LandingPage $landingPage): string
    {
        $now = now();

        if ($landingPage->campaign_starts_at?->isFuture()) {
            return 'upcoming';
        }

        if ($landingPage->campaign_ends_at?->isPast()) {
            return 'expired';
        }

        return 'active';
    }

    /** @param Collection<int, array<string, mixed>> $sections */
    private function mediaFor(LandingPage $landingPage, Collection $sections): Collection
    {
        $ids = $sections
            ->flatMap(function (array $section): array {
                $data = is_array($section['data'] ?? null) ? $section['data'] : [];

                return [
                    $data['media_id'] ?? null,
                    ...(is_array($data['media_ids'] ?? null) ? $data['media_ids'] : []),
                ];
            })
            ->push($landingPage->curator_media_id)
            ->filter(fn (mixed $id): bool => is_numeric($id))
            ->map(fn (mixed $id): int => (int) $id)
            ->unique()
            ->values();

        return Media::query()->whereKey($ids->all())->get()->keyBy('id');
    }

    private function dateValue(mixed $value, ?CarbonInterface $fallback): ?CarbonImmutable
    {
        if (blank($value)) {
            return $fallback ? CarbonImmutable::instance($fallback) : null;
        }

        try {
            return CarbonImmutable::parse((string) $value, (string) config('app.timezone'));
        } catch (Throwable) {
            return $fallback ? CarbonImmutable::instance($fallback) : null;
        }
    }

    private function color(mixed $value, string $fallback): string
    {
        return is_string($value) && preg_match('/^#[0-9a-fA-F]{6}$/', $value) === 1
            ? strtolower($value)
            : $fallback;
    }

    private function safeLink(mixed $value, string $fallback = '#tu-van'): string
    {
        $link = trim((string) $value);

        if ($link === '') {
            return $fallback;
        }

        return preg_match('~^(?:(?:https?://|tel:|mailto:)[^\s<>]+|/[^\s<>]*|\#[a-zA-Z0-9_-]+)$~', $link) === 1
            ? $link
            : $fallback;
    }

    /** @return Collection<int, array{question: string, answer: string}> */
    private function faqItems(mixed $items): Collection
    {
        return collect(is_array($items) ? $items : [])
            ->map(fn (mixed $item): array => [
                'question' => trim((string) (is_array($item) ? ($item['question'] ?? '') : '')),
                'answer' => trim((string) (is_array($item) ? ($item['answer'] ?? '') : '')),
            ])
            ->filter(fn (array $item): bool => $item['question'] !== '' && $item['answer'] !== '')
            ->values();
    }
}
