<?php

namespace App\Support\Landing;

use App\Models\LandingPage;
use Awcodes\Curator\Models\Media;
use Illuminate\Support\Collection;

/**
 * Prepares the database-owned blocks used by the single landing-page builder.
 *
 * Keeping this normalization outside Blade makes the public view a renderer
 * only and gives every block the same safe-link and media rules.
 */
class LandingPageBlocks
{
    /** @var array<string, string> */
    private const VIEWS = [
        'hero' => 'frontend.landing.blocks.hero',
        'content_grid' => 'frontend.landing.blocks.content-grid',
        'process' => 'frontend.landing.blocks.process',
        'stats' => 'frontend.landing.blocks.stats',
        'testimonials' => 'frontend.landing.blocks.testimonials',
        'projects' => 'frontend.landing.blocks.projects',
        'service_categories' => 'frontend.landing.blocks.service-categories',
        'services' => 'frontend.landing.blocks.services',
        'posts' => 'frontend.landing.blocks.posts',
        'pricing' => 'frontend.landing.blocks.pricing',
        'rich_text' => 'frontend.landing.blocks.rich-text',
        'gallery' => 'frontend.landing.blocks.gallery',
        'faqs' => 'frontend.landing.blocks.faqs',
        'lead_form' => 'frontend.landing.blocks.lead-form',
        'cta' => 'frontend.landing.blocks.cta',
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
                    'content_grid', 'process', 'stats', 'testimonials' => $prepared + [
                        'items' => collect($data['items'] ?? [])
                            ->filter(fn (mixed $item): bool => is_array($item)
                                && filled($item['title'] ?? $item['label'] ?? $item['quote'] ?? $item['question'] ?? $item['author'] ?? null))
                            ->values(),
                    ],
                    'projects' => $prepared + [
                        'projects' => $landingPage->projects->take($this->limit($data['limit'] ?? 6)),
                    ],
                    'service_categories' => $prepared + [
                        'categories' => $landingPage->serviceCategories->take($this->limit($data['limit'] ?? 6)),
                    ],
                    'services' => $prepared + [
                        'services' => $landingPage->services->take($this->limit($data['limit'] ?? 6)),
                    ],
                    'posts' => $prepared + [
                        'posts' => $landingPage->posts->take($this->limit($data['limit'] ?? 6)),
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
                        'items' => $this->faqItems($landingPage->faqs),
                    ],
                    default => $prepared,
                };
            })
            ->all();
    }

    /** @return array{primary: string, accent: string, surface: string, ink: string} */
    public function theme(): array
    {
        return [
            'primary' => '#ee6b2d',
            'accent' => '#f97316',
            'surface' => '#f5f7fa',
            'ink' => '#10233e',
        ];
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

    private function limit(mixed $value): int
    {
        return max(1, min(12, (int) $value));
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
    private function faqItems(iterable $items): Collection
    {
        return collect($items)
            ->map(fn ($item): array => [
                'question' => trim((string) $item->question),
                'answer' => trim((string) $item->answer),
            ])
            ->filter(fn (array $item): bool => $item['question'] !== '' && $item['answer'] !== '')
            ->values();
    }
}
