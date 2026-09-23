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
        if ($request->expectsJson() && $request->exists('type')) {
            return $this->storeQuoteRequest($request);
        }

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

        ContactRequest::query()->create($data + ['request_type' => 'contact']);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cảm ơn bạn đã liên hệ. Chúng tôi sẽ phản hồi sớm nhất có thể.',
            ]);
        }

        return redirect()->to($this->safeReturnPath($request->input('return_to')) ?? route('contact'))
            ->with('success', 'Cảm ơn bạn đã liên hệ. Chúng tôi sẽ phản hồi sớm nhất có thể.');
    }

    private function storeQuoteRequest(Request $request): JsonResponse
    {
        if ($request->filled('website')) {
            return response()->json([
                'success' => true,
                'message' => 'Yêu cầu đã được tiếp nhận.',
            ], 202);
        }

        $request->merge(['phone' => $this->normalizePhone($request->input('phone'))]);
        $data = $request->validate([
            'type' => ['required', 'string', 'in:trip,partner,wedding,shared'],
            'service_id' => ['nullable', 'integer', 'exists:services,id'],
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'phone' => ['required', 'string', 'regex:/^0[35789]\d{8}$/'],
            'email' => ['nullable', 'email', 'max:255'],
            'company' => ['required_if:type,partner', 'nullable', 'string', 'max:180'],
            'pickup' => ['nullable', 'string', 'max:180'],
            'destination' => ['nullable', 'string', 'max:180'],
            'departure' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:today', 'required_with:returnDate'],
            'returnDate' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:departure'],
            'vehicle' => ['nullable', 'string', 'max:100'],
            'passengers' => ['nullable', 'string', 'max:80'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'consent' => ['required', 'accepted'],
            'website' => ['nullable', 'string', 'max:255'],
        ]);

        $service = filled($data['service_id'] ?? null)
            ? Service::query()->published()->find($data['service_id'])
            : null;

        if (filled($data['service_id'] ?? null) && $service === null) {
            abort(422, 'Dịch vụ đã chọn không còn khả dụng.');
        }

        $typeLabels = [
            'trip' => 'Bao xe / Du lịch / Đi tỉnh',
            'partner' => 'Hợp tác cung cấp xe du lịch',
            'wedding' => 'Xe cưới – Xe dâu',
            'shared' => 'Xe ghép Hà Nội – Cẩm Khê / Yên Lập',
        ];
        $details = array_filter([
            'service' => $service?->title,
            'pickup' => $data['pickup'] ?? null,
            'destination' => $data['destination'] ?? null,
            'departure_date' => $data['departure'] ?? null,
            'return_date' => $data['returnDate'] ?? null,
            'vehicle' => $data['vehicle'] ?? null,
            'passengers' => $data['passengers'] ?? null,
            'notes' => $data['notes'] ?? null,
            'source' => 'camkhetravel_homepage',
        ], fn (mixed $value): bool => filled($value));

        $message = collect([
            'Loại yêu cầu: '.($typeLabels[$data['type']] ?? $data['type']),
            filled($service?->title) ? 'Dịch vụ: '.$service->title : null,
            filled($data['pickup'] ?? null) ? 'Điểm đón: '.$data['pickup'] : null,
            filled($data['destination'] ?? null) ? 'Điểm đến: '.$data['destination'] : null,
            filled($data['departure'] ?? null) ? 'Ngày đi: '.$data['departure'] : null,
            filled($data['returnDate'] ?? null) ? 'Ngày về: '.$data['returnDate'] : null,
            filled($data['vehicle'] ?? null) ? 'Loại xe: '.$data['vehicle'] : null,
            filled($data['passengers'] ?? null) ? 'Quy mô đoàn: '.$data['passengers'] : null,
            filled($data['notes'] ?? null) ? 'Ghi chú: '.$data['notes'] : null,
        ])->filter()->implode("\n");

        ContactRequest::query()->create([
            'request_type' => $data['type'],
            'service_id' => $service?->getKey(),
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'],
            'company' => $data['company'] ?? null,
            'message' => $message,
            'details' => $details,
            'privacy_consent_at' => now(),
            'status' => 'new',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'CamKheTravel đã tiếp nhận yêu cầu tư vấn. Đây chưa phải xác nhận đặt xe.',
        ], 201);
    }

    private function normalizePhone(mixed $phone): string
    {
        $normalized = preg_replace('/\D+/', '', (string) $phone) ?: '';

        if (str_starts_with($normalized, '0084')) {
            return '0'.substr($normalized, 4);
        }

        if (str_starts_with($normalized, '84')) {
            return '0'.substr($normalized, 2);
        }

        return $normalized;
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
