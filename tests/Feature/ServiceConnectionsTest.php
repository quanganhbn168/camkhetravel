<?php

namespace Tests\Feature;

use App\Models\PricingPackage;
use App\Models\ServicePricing;
use App\Models\Project;
use App\Models\Service;
use Awcodes\Curator\Models\Media;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ServiceConnectionsTest extends TestCase
{
    use DatabaseTransactions;

    public function test_service_page_connects_its_pricing_and_backstage_projects(): void
    {
        $service = Service::query()->published()->firstOrFail();
        $linkedProject = Project::query()->published()->firstOrFail();
        $otherProject = Project::query()->published()->whereKeyNot($linkedProject->id)->firstOrFail();
        $service->backstageProjects()->sync([$linkedProject->id]);

        $this->get('/'.$service->slug)
            ->assertOk()
            ->assertSee('resource-detail-hero', false)
            ->assertSee($linkedProject->title)
            ->assertSee('/du-an?service='.$service->id, false);

        $this->get('/du-an?service='.$service->id)
            ->assertOk()
            ->assertSee('Dự án: '.$service->title)
            ->assertSee($linkedProject->title)
            ->assertDontSee($otherProject->title);
    }

    public function test_pricing_page_filters_plans_for_the_selected_service(): void
    {
        $service = Service::query()->published()->firstOrFail();
        $otherService = Service::query()->published()->whereKeyNot($service->id)->firstOrFail();
        $visiblePricing = ServicePricing::query()->firstOrCreate([
            'service_id' => $service->id,
        ], [
            'title' => 'Bảng giá '.$service->title,
        ]);
        $otherPricing = ServicePricing::query()->firstOrCreate([
            'service_id' => $otherService->id,
        ], [
            'title' => 'Bảng giá '.$otherService->title,
        ]);
        $visiblePlan = PricingPackage::query()->create([
            'service_pricing_id' => $visiblePricing->id,
            'name' => 'Gói bảng giá theo dịch vụ',
            'is_active' => true,
            'sort_order' => 1,
        ]);
        $otherPlan = PricingPackage::query()->create([
            'service_pricing_id' => $otherPricing->id,
            'name' => 'Gói bảng giá dịch vụ khác',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $this->get('/bang-gia?service='.$service->id)
            ->assertOk()
            ->assertSee('Bảng giá '.$service->title)
            ->assertSee($visiblePlan->name)
            ->assertDontSee($otherPlan->name);
    }

    public function test_service_page_displays_its_separate_backstage_gallery(): void
    {
        $service = Service::query()->published()->firstOrFail();
        $media = Media::query()->limit(2)->get();
        $backstageImage = $media->firstOrFail();
        $referenceImage = $media->get(1) ?? $backstageImage;
        $service->update([
            'gallery' => [$referenceImage->id],
            'backstage_gallery' => [$backstageImage->id],
            'reference_videos' => [[
                'title' => 'Video tham khảo kiểm thử',
                'url' => 'https://www.youtube.com/watch?v=reference-test',
                'description' => 'Mô tả video tham khảo kiểm thử',
            ]],
        ]);

        $response = $this->get('/'.$service->slug)
            ->assertOk()
            ->assertSee('HÌNH ẢNH HẬU TRƯỜNG')
            ->assertSee($backstageImage->url, false)
            ->assertSee('Các dự án nổi bật')
            ->assertSee('service-reference__tabs', false)
            ->assertSee('data-type="video"', false)
            ->assertSee('service-reference-images-'.$service->id, false)
            ->assertSee('service-backstage-images-'.$service->id, false);

        $html = $response->getContent();
        $referencePosition = strpos($html, 'service-reference-images-'.$service->id);
        $backstagePosition = strpos($html, 'service-backstage-images-'.$service->id);

        $this->assertNotFalse($referencePosition);
        $this->assertNotFalse($backstagePosition);
        $this->assertLessThan($backstagePosition, $referencePosition);
    }

    public function test_service_reference_hides_tabs_when_only_one_media_type_exists(): void
    {
        $service = Service::query()->published()->firstOrFail();
        $referenceImage = Media::query()->firstOrFail();
        $service->update([
            'gallery' => [$referenceImage->id],
            'reference_videos' => [],
        ]);

        $this->get('/'.$service->slug)
            ->assertOk()
            ->assertSee('Các dự án nổi bật')
            ->assertDontSee('service-reference__tabs', false)
            ->assertSee('service-reference-images-'.$service->id, false);
    }

    public function test_service_page_uses_the_standard_native_service_sections(): void
    {
        $service = Service::query()
            ->whereHas('slugs', fn ($query) => $query->where('slug', 'san-xuat-phim-doanh-nghiep'))
            ->firstOrFail();

        $response = $this->get('/'.$service->slug)
            ->assertOk()
            ->assertSee('Lợi ích của việc quay TVC quảng cáo cho doanh nghiệp')
            ->assertSee('Các dự án nổi bật')
            ->assertSee('service-pricing-table', false)
            ->assertSee('Quy trình')
            ->assertSee('Cam kết của THT MEDIA')
            ->assertSee('Khách hàng nói về chúng tôi')
            ->assertDontSee('Giải pháp được xây dựng từ mục tiêu thực tế')
            ->assertDontSee('resource-detail-aside', false);

        $html = $response->getContent();
        $this->assertLessThan(strpos($html, 'id="du-an-noi-bat"'), strpos($html, 'id="tai-lieu-tham-khao"'));
        $this->assertLessThan(strpos($html, 'id="noi-dung-chi-tiet"'), strpos($html, 'id="cam-ket"'));
    }

    public function test_service_page_places_the_legacy_pricing_media_at_the_top_when_configured(): void
    {
        $service = Service::query()->published()->firstOrFail();
        $pricingImage = Media::query()->where('type', 'like', 'image/%')->firstOrFail();
        $pricing = ServicePricing::query()->firstOrCreate([
            'service_id' => $service->id,
        ], [
            'title' => 'Bảng giá '.$service->title,
        ]);
        $pricing->update(['source_media_id' => $pricingImage->id]);

        $response = $this->get('/'.$service->slug)
            ->assertOk()
            ->assertSee('id="tai-lieu-bang-gia"', false)
            ->assertSee($pricingImage->url, false);

        $html = $response->getContent();
        $this->assertLessThan(strpos($html, 'id="noi-dung-dich-vu"'), strpos($html, 'id="tai-lieu-bang-gia"'));
    }
}
