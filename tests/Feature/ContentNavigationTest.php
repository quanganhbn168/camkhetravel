<?php

namespace Tests\Feature;

use App\Filament\Resources\ContactRequests\ContactRequestResource;
use Tests\TestCase;

class ContentNavigationTest extends TestCase
{
    public function test_content_resources_have_distinct_order_and_contact_is_in_customer_group(): void
    {
        $resources = ['Intros\\Intro', 'Services\\Service', 'ServiceCategories\\ServiceCategory', 'Solutions\\Solution', 'Projects\\Project', 'ProjectCategories\\ProjectCategory', 'Products\\Product', 'ProductCategories\\ProductCategory', 'Posts\\Post', 'PostCategories\\PostCategory', 'Tags\\Tag', 'Faqs\\Faq'];
        foreach ($resources as $index => $name) {
            $class = 'App\\Filament\\Resources\\'.$name.'Resource';
            $this->assertSame('Nội dung website', $class::getNavigationGroup());
            $this->assertSame($index + 1, $class::getNavigationSort());
        }
        $this->assertSame('Khách hàng', ContactRequestResource::getNavigationGroup());
    }
}
