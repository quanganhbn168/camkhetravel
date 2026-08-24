<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ContactRequest;
use App\Models\Landing;
use App\Support\Seo\FrontendSeoBuilder;
use App\Support\Localization\LocalizedUrl;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function __construct(private readonly FrontendSeoBuilder $seo) {}

    public function index(Request $request): View
    {
        return view('frontend.contact', [
            'services' => Landing::query()->published()->orderBy('sort_order')->get(['id', 'title']),
            'seo' => $this->seo->listing(
                'Liên hệ | '.$this->seo->siteName(),
                'Liên hệ để trao đổi nhu cầu truyền thông, sản xuất nội dung và sự kiện.',
                LocalizedUrl::route('contact'),
            ),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:32'],
            'company' => ['nullable', 'string', 'max:255'],
            'landing_id' => ['nullable', 'exists:landings,id'],
            'budget' => ['nullable', 'string', 'max:255'],
            'timeline' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        ContactRequest::query()->create($data);

        return redirect()->to(LocalizedUrl::route('contact'))->with('success', __('site.contact_success'));
    }
}
