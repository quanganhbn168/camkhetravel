<?php

namespace App\Support\Landing;

use App\Models\LandingPage;
use App\Models\Service;
use App\Settings\WebsiteSettings;
use App\Support\Media\MediaUrl;
use Awcodes\Curator\Models\Media;

/** Builds the view model used by every native Landing page. */
final class LandingPresenter
{
    public function __construct(private readonly WebsiteSettings $website) {}

    /** @return array<string, mixed> */
    public function present(string $templateKey, ?LandingPage $landingPage = null, ?Service $service = null): array
    {
        $definition = LandingRegistry::find($templateKey) ?? [];
        $storedContent = $landingPage?->landing_content ?? $service?->landing_content ?? [];
        $content = $this->mapMedia(is_array($storedContent) ? $storedContent : []);
        $contact = array_replace([
            'hotline_1' => $this->website->hotline ?: $this->website->contact_phone,
            'hotline_display' => $this->website->hotline ?: $this->website->contact_phone,
            'email' => $this->website->contact_email,
            'address_1' => $this->website->address,
            'facebook_url' => $this->website->facebook_url,
            'zalo_url' => $this->website->zalo_url,
        ], (array) ($content['contact'] ?? []));
        $contact['website'] = url('/');
        $content['contact'] = $contact;

        if ($service) {
            $content['managed_process_items'] = array_values(array_filter(
                (array) $service->process_items,
                static fn (mixed $item): bool => is_array($item),
            ));
            $content['managed_pricing_plans'] = $service->pricingCatalog?->packages
                ?->map(static fn ($package): array => [
                    'name' => (string) $package->name,
                    'list_price' => $package->list_price !== null ? (int) $package->list_price : null,
                    'price_label' => $package->price_label,
                ])->values()->all() ?? [];
        }

        $hero = (array) ($content['hero'] ?? []);

        return [
            'template_key' => $templateKey,
            'slug' => $landingPage?->slug ?? $service?->slug ?? ($definition['slug'] ?? ''),
            'view' => $definition['view'] ?? null,
            'content' => $content,
            'hero' => $hero,
            'title' => (string) ($hero['title'] ?? $landingPage?->title ?? $service?->title ?? 'THT Media'),
            'excerpt' => (string) ($landingPage?->excerpt ?? $service?->excerpt ?? $content['seo']['description'] ?? ''),
            'brand' => (string) (($content['brand']['name'] ?? null) ?: 'THT MEDIA'),
            'navigation' => (array) ($content['navigation'] ?? []),
            'contact' => $contact,
            'logo_url' => $this->logoUrl() ?: $this->pageLogo($content),
            'services' => LandingRegistry::footerLinks(),
        ];
    }

    /** @param array<string, mixed> $content @return array<string, mixed> */
    private function mapMedia(array $content): array
    {
        foreach ($content as $key => $value) {
            if (is_array($value)) {
                $content[$key] = array_is_list($value)
                    ? array_map(fn (mixed $item): mixed => is_array($item) ? $this->mapMedia($item) : (is_string($item) ? $this->mapMediaString($item) : $item), $value)
                    : $this->mapMedia($value);
            } elseif (is_string($value)) {
                $content[$key] = $this->mapMediaString($value);
            }
        }

        return $content;
    }

    private function mapMediaString(string $value): string
    {
        $trimmed = trim($value);
        if ($trimmed === '' || preg_match('#^(?:https?:)?//#i', $trimmed) || in_array($trimmed[0] ?? '', ['#', '/'], true) || preg_match('#^(?:data:|mailto:|tel:)#i', $trimmed)) {
            return $value;
        }

        if (! preg_match('#\.(?:jpe?g|png|gif|webp|svg|avif|mp4|webm|mov|m4v)(?:\?.*)?$#i', $trimmed)) {
            return $value;
        }

        return LandingRegistry::assetUrl($trimmed);
    }

    /** @param array<string, mixed> $content */
    private function pageLogo(array $content): ?string
    {
        $logo = $content['brand']['logo'] ?? $content['brand']['logo_url'] ?? $content['logo'] ?? null;

        return is_string($logo) && $logo !== '' ? LandingRegistry::assetUrl($logo) : null;
    }

    private function logoUrl(): ?string
    {
        if (! is_numeric($this->website->logo_media_id)) {
            return null;
        }

        return MediaUrl::versioned(Media::query()->find((int) $this->website->logo_media_id));
    }
}
