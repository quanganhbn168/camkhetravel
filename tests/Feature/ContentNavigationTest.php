<?php

namespace Tests\Feature;

use App\Filament\Resources\ContactRequests\ContactRequestResource;
use App\Filament\Resources\SolutionCategories\SolutionCategoryResource;
use Tests\TestCase;

class ContentNavigationTest extends TestCase
{
    public function test_existing_content_order_is_preserved_and_solution_category_is_adjacent(): void
    {
        $resources = ['Intros\\Intro', 'Services\\Service', 'ServiceCategories\\ServiceCategory', 'Solutions\\Solution', 'Projects\\Project', 'ProjectCategories\\ProjectCategory', 'Products\\Product', 'ProductCategories\\ProductCategory', 'Posts\\Post', 'PostCategories\\PostCategory', 'Tags\\Tag', 'Faqs\\Faq'];
        foreach ($resources as $index => $name) {
            $class = 'App\\Filament\\Resources\\'.$name.'Resource';
            $this->assertSame('Nội dung website', $class::getNavigationGroup());
            $this->assertSame($index + 1, $class::getNavigationSort());
        }
        $this->assertSame('Nội dung website', SolutionCategoryResource::getNavigationGroup());
        $this->assertSame(4, SolutionCategoryResource::getNavigationSort());
        $this->assertSame('Khách hàng', ContactRequestResource::getNavigationGroup());
    }
}
