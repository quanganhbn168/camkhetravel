<?php

namespace Tests\Feature;

use App\Models\PricingPlan;
use App\Models\Project;
use App\Models\Landing;
use Awcodes\Curator\Models\Media;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ServiceConnectionsTest extends TestCase
{
    use DatabaseTransactions;

    public function test_service_page_connects_its_pricing_and_backstage_projects(): void
    {
        $service = Landing::query()->published()->firstOrFail();
        $linkedProject = Project::query()->published()->firstOrFail();
        $otherProject = Project::query()->published()->whereKeyNot($linkedProject->id)->firstOrFail();
        $service->backstageProjects()->sync([$linkedProject->id]);

        $this->get('/'.$service->slug)
            ->assertOk()
            ->assertDontSee('resource-detail-hero', false)
            ->assertSee($linkedProject->title)
            ->assertSee('/du-an?landing='.$service->id, false);

        $this->get('/du-an?landing='.$service->id)
            ->assertOk()
            ->assertSee('Hậu trường dịch vụ')
            ->assertSee($linkedProject->title)
            ->assertDontSee($otherProject->title);
    }

    public function test_pricing_page_filters_plans_for_the_selected_service(): void
    {
        $service = Landing::query()->published()->firstOrFail();
        $otherService = Landing::query()->published()->whereKeyNot($service->id)->firstOrFail();
        $visiblePlan = PricingPlan::query()->create([
            'landing_id' => $service->id,
            'name' => 'Gói bảng giá theo dịch vụ',
            'is_active' => true,
            'sort_order' => 1,
        ]);
        $otherPlan = PricingPlan::query()->create([
            'landing_id' => $otherService->id,
            'name' => 'Gói bảng giá dịch vụ khác',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $this->get('/bang-gia?landing='.$service->id)
            ->assertOk()
            ->assertSee('Bảng giá '.$service->title)
            ->assertSee($visiblePlan->name)
            ->assertDontSee($otherPlan->name);
    }

    public function test_service_page_displays_its_separate_backstage_gallery(): void
    {
        $service = Landing::query()->published()->firstOrFail();
        $backstageImage = Media::query()->firstOrFail();
        $service->update(['backstage_gallery' => [$backstageImage->id]]);

        $this->get('/'.$service->slug)
            ->assertOk()
            ->assertSee('HÌNH ẢNH HẬU TRƯỜNG')
            ->assertSee($backstageImage->url, false);
    }
}
