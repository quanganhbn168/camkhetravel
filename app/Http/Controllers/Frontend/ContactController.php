<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ContactRequest;
use App\Models\Service;
use App\Settings\WebsiteSettings;
use App\Support\Maps\GoogleMapsUrl;
use App\Support\Media\MediaUrl;
use App\Support\Pages\SystemPageProfileResolver;
use App\Support\Seo\FrontendSeoBuilder;
use Awcodes\Curator\Models\Media;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function __construct(
        private readonly FrontendSeoBuilder $seo,
        private readonly WebsiteSettings $website,
        private readonly SystemPageProfileResolver $systemPages,
    ) {}

    public function index(Request $request): View
    {
        $page = $this->systemPages->require('contact');
        $googleMapsEmbedUrl = GoogleMapsUrl::normalizeEmbed($this->website->google_maps_embed_url);

        if ($googleMapsEmbedUrl === null && filled($this->website->address)) {
            $googleMapsEmbedUrl = 'https://www.google.com/maps?q='.rawurlencode($this->website->address).'&output=embed';
        }

        return view('frontend.contact', [
            'prefilledMessage' => '',
            'services' => Service::query()->published()->orderBy('sort_order')->get(['id', 'title']),
            'contactPhones' => $this->contactPhones(),
            'contactBranches' => $this->contactBranches(),
            'page' => $page,
            'pageBannerUrl' => $page['banner_url'],
            'googleMapsUrl' => filled($this->website->google_maps_url)
                ? trim($this->website->google_maps_url)
                : null,
            'googleMapsEmbedUrl' => $googleMapsEmbedUrl,
            'seo' => $this->seo->systemPage($page, 'contact'),
        ]);
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:32'],
            'company' => ['nullable', 'string', 'max:255'],
            'service_id' => ['nullable', 'exists:services,id'],
            'budget' => ['nullable', 'string', 'max:255'],
            'timeline' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $contactRequest = ContactRequest::query()->create($data);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Cảm ơn bạn đã liên hệ. Chúng tôi sẽ phản hồi sớm nhất có thể.',
            ]);
        }

        return redirect()->to($this->safeReturnPath($request->input('return_to')) ?? route('contact'))
            ->with('success', 'Cảm ơn bạn đã liên hệ. Chúng tôi sẽ phản hồi sớm nhất có thể.');
    }

    /** @return Collection<int, array{label: string, href: string}> */
    private function contactPhones(): Collection
    {
        $phones = collect($this->website->phones ?? [])
            ->filter(fn ($phone): bool => is_array($phone) && filled($phone['number'] ?? null))
            ->values();

        if ($phones->isEmpty()) {
            $phones = collect([
                ['number' => $this->website->hotline],
                ['number' => $this->website->contact_phone],
            ])->filter(fn (array $phone): bool => filled($phone['number'] ?? null))->values();
        }

        return $phones->map(fn (array $phone): array => [
            'label' => trim((string) $phone['number']),
            'href' => 'tel:'.preg_replace('/\s+/', '', (string) $phone['number']),
        ])->unique('href')->values();
    }

    /** @return Collection<int, array{name: string, address: string}> */
    private function contactBranches(): Collection
    {
        return collect($this->website->branches ?? [])
            ->filter(fn ($branch): bool => is_array($branch)
                && ($branch['is_active'] ?? true)
                && filled($branch['address'] ?? null))
            ->map(fn (array $branch): array => [
                'name' => (string) ($branch['name'] ?? 'Địa chỉ'),
                'address' => trim((string) $branch['address']),
            ])
            ->values();
    }

    private function safeReturnPath(mixed $returnTo): ?string
    {
        if (! is_string($returnTo)) {
            return null;
        }

        $returnTo = trim($returnTo);

        return str_starts_with($returnTo, '/') && ! str_starts_with($returnTo, '//')
            ? $returnTo
            : null;
    }
}
