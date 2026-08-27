<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ContactRequest;
use App\Models\Landing;
use App\Settings\WebsiteSettings;
use App\Support\Media\MediaUrl;
use App\Support\Maps\GoogleMapsUrl;
use App\Support\Seo\FrontendSeoBuilder;
use App\Support\Localization\LocalizedUrl;
use Awcodes\Curator\Models\Media;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function __construct(
        private readonly FrontendSeoBuilder $seo,
        private readonly WebsiteSettings $website,
    ) {}

    public function index(Request $request): View
    {
        $googleMapsEmbedUrl = GoogleMapsUrl::normalizeEmbed($this->website->google_maps_embed_url);

        if ($googleMapsEmbedUrl === null && filled($this->website->address)) {
            $googleMapsEmbedUrl = 'https://www.google.com/maps?q='.rawurlencode($this->website->address).'&output=embed';
        }

        return view('frontend.contact', [
            'services' => Landing::query()->published()->orderBy('sort_order')->get(['id', 'title']),
            'contactHeroImageUrl' => MediaUrl::versioned(
                Media::query()->find($this->website->contact_image_media_id),
            ),
            'googleMapsUrl' => filled($this->website->google_maps_url)
                ? trim($this->website->google_maps_url)
                : null,
            'googleMapsEmbedUrl' => $googleMapsEmbedUrl,
            'seo' => $this->seo->listing(
                'Liên hệ | '.$this->seo->siteName(),
                'Liên hệ để trao đổi nhu cầu truyền thông, sản xuất nội dung và sự kiện.',
                LocalizedUrl::route('contact'),
            ),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $isLandingSubmission = $request->boolean('from_landing');
        $data = $request->validate([
            'name' => [$isLandingSubmission ? 'nullable' : 'required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => [$isLandingSubmission ? 'required' : 'nullable', 'string', 'max:32'],
            'company' => ['nullable', 'string', 'max:255'],
            'landing_id' => ['nullable', 'exists:landings,id'],
            'budget' => ['nullable', 'string', 'max:255'],
            'timeline' => ['nullable', 'string', 'max:255'],
            'message' => [$isLandingSubmission ? 'nullable' : 'required', 'string', 'max:5000'],
        ]);

        if ($isLandingSubmission) {
            $data['name'] = filled($data['name'] ?? null) ? $data['name'] : 'Chưa cung cấp';
            $data['message'] = filled($data['message'] ?? null) ? $data['message'] : 'Chưa cung cấp';
        }

        ContactRequest::query()->create($data);

        return redirect()->to($this->safeReturnPath($request->input('return_to')) ?? LocalizedUrl::route('contact'))
            ->with('success', __('site.contact_success'));
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
