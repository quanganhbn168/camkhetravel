<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\ServiceCategory;
use App\Settings\HomepageSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePartnerSectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_services_render_as_a_service_grid_and_partner_section_follows_cms(): void
    {
        $category = ServiceCategory::create(['name' => 'Nhóm QA', 'is_active' => true]);
        Service::create(['service_category_id' => $category->id, 'title' => 'Dịch vụ lưới QA', 'status' => 'published', 'is_home' => true]);

        $settings = app(HomepageSettings::class);
        $settings->section_content = ['partner_title' => 'Tiêu đề hợp tác QA', 'partner_languages' => '企业员工接送'];
        $settings->partner_benefits = [['title' => 'Lợi ích QA', 'description' => 'Mô tả lợi ích']];
        $settings->save();

        $this->get('/')->assertOk()
            ->assertSee('class="service-card"', false)
            ->assertSee('Dịch vụ lưới QA')
            ->assertSee('id="doi-tac"', false)
            ->assertSee('Tiêu đề hợp tác QA')
            ->assertSee('企业员工接送')
            ->assertSee('Lợi ích QA');
    }
}
