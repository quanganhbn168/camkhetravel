<?php

namespace Tests\Feature;

use App\Filament\Resources\ProductCategories\Pages\EditProductCategory;
use App\Filament\Resources\Products\Pages\CreateProduct;
use App\Models\PostCategory;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProjectCategory;
use App\Models\ServiceCategory;
use App\Models\User;
use App\Support\Categories\CategoryTree;
use Awcodes\Curator\Models\Media;
use Database\Seeders\MediaSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CategoryHierarchyTest extends TestCase
{
    use RefreshDatabase;

    public function test_invalid_choices_are_absent_from_forms_and_forged_parent_is_rejected(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate('super_admin'));
        $this->actingAs($user);
        $root = ProductCategory::create(['name' => 'Cha']);
        $leaf = ProductCategory::create(['name' => 'Lá', 'parent_id' => $root->id]);
        $grandchild = ProductCategory::create(['name' => 'Cháu', 'parent_id' => $leaf->id]);
        $occupied = ProductCategory::create(['name' => 'Đang chứa sản phẩm']);
        Product::create(['title' => 'Có sẵn', 'product_category_id' => $occupied->id]);
        $form = Livewire::test(EditProductCategory::class, ['record' => $root->id]);
        $this->assertSame([], $form->instance()->form->getFlatComponents()['parent_id']->getOptions());
        $form->set('data.parent_id', $grandchild->id)->call('save')->assertHasFormErrors(['parent_id']);
        $this->assertNull($root->fresh()->parent_id);
        $productForm = Livewire::test(CreateProduct::class);
        $this->assertSame([$grandchild->id => 'Cha > Lá > Cháu', $occupied->id => 'Đang chứa sản phẩm'], $productForm->instance()->form->getFlatComponents()['product_category_id']->getOptions());
    }

    public function test_display_tree_keeps_children_below_parent_and_aggregates_counts(): void
    {
        $root = ProductCategory::create(['name' => 'Cha', 'sort_order' => 20]);
        $leaf = ProductCategory::create(['name' => 'Lá', 'parent_id' => $root->id, 'sort_order' => 1]);
        Product::create(['title' => 'Một sản phẩm', 'product_category_id' => $leaf->id]);
        $display = CategoryTree::forDisplay(ProductCategory::query()->orderBy('sort_order')->withCount('products')->get(), 'products_count');
        $this->assertSame([$root->id, $leaf->id], $display->pluck('id')->all());
        $this->assertSame(['Cha', 'Cha > Lá'], $display->pluck('tree_label')->all());
        $this->assertSame([1, 1], $display->pluck('products_count')->all());
    }

    public function test_admin_forms_save_parent_body_and_independent_images_for_every_category(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate('super_admin'));
        $this->actingAs($user);
        $cover = MediaSeeder::id('equipment');
        $banner = MediaSeeder::id('facility');
        foreach (['Product', 'Service', 'Project', 'Post'] as $type) {
            $model = 'App\\Models\\'.$type.'Category';
            $page = 'App\\Filament\\Resources\\'.$type.'Categories\\Pages\\Edit'.$type.'Category';
            $root = $model::create(['name' => 'Cha '.$type]);
            $record = $model::create(['name' => 'Con '.$type]);
            Livewire::test($page, ['record' => $record->id])
                ->fillForm(['parent_id' => $root->id, 'body' => '<p>Nội dung riêng</p>'])
                ->set('data.curator_media_id', [Media::findOrFail($cover)->toArray()])
                ->set('data.banner_media_id', [Media::findOrFail($banner)->toArray()])
                ->call('save')->assertHasNoFormErrors();
            $record->refresh();
            $this->assertEquals($root->id, $record->parent_id);
            $this->assertEquals($cover, $record->curator_media_id);
            $this->assertEquals($banner, $record->banner_media_id);
            $this->assertStringContainsString('Nội dung riêng', $record->body);
        }
    }

    public function test_parent_archive_includes_descendant_content_and_uses_only_its_own_banner(): void
    {
        foreach (['Product' => ['products.category', 'slug', 'products'], 'Project' => ['projects.category', 'slug', 'projects'], 'Service' => ['services.category', 'category', 'services'], 'Post' => ['posts.category', 'slug', 'posts']] as $type => [$route, $parameter, $relation]) {
            $model = 'App\\Models\\'.$type.'Category';
            $root = $model::create(['name' => 'Gốc '.$type, 'seo_title' => 'SEO riêng '.$type, 'seo_description' => 'Mô tả SEO riêng '.$type, 'body' => '<p>Nội dung gốc '.$type.'</p>', 'banner_media_id' => MediaSeeder::id('facility')]);
            $leaf = $model::create(['name' => 'Lá '.$type, 'parent_id' => $root->id]);
            $leaf->{$relation}()->create(['title' => 'Bài thuộc lá '.$type, 'status' => 'published']);
            $url = route($route, [$parameter => $root->slug]);
            $this->get($url)->assertOk()->assertSee('Bài thuộc lá '.$type)->assertSee('Nội dung gốc '.$type)
                ->assertSee('<title>SEO riêng '.$type.'</title>', false)->assertSee('content="Mô tả SEO riêng '.$type.'"', false)
                ->assertViewHas('pageBannerUrl', $root->banner_url);
            $root->update(['banner_media_id' => null]);
            $this->get($url)->assertOk()->assertViewHas('pageBannerUrl', null)->assertDontSee('data-page-banner-image', false);
        }
    }

    public function test_all_categories_support_hierarchy_content_and_separate_images(): void
    {
        foreach ([ProductCategory::class, ServiceCategory::class, ProjectCategory::class, PostCategory::class] as $model) {
            $this->assertTrue(Schema::hasColumns((new $model)->getTable(), ['parent_id', 'body', 'curator_media_id', 'banner_media_id']));
            $root = $model::create(['name' => 'Gốc']);
            $child = $model::create(['name' => 'Con', 'parent_id' => $root->id]);
            $leaf = $model::create(['name' => 'Cháu', 'parent_id' => $child->id]);
            $other = $model::create(['name' => 'Nhánh khác']);
            $this->assertNull($root->fresh()->parent_id);
            $this->assertSame([$root->id, $child->id, $leaf->id], $root->subtreeIds());
            $this->assertSame([$other->id => 'Nhánh khác'], CategoryTree::parentOptions($model, $root));
            $this->assertSame('Gốc > Con > Cháu', CategoryTree::options($model)[$leaf->id]);
            try {
                $root->update(['parent_id' => $leaf->id]);
                $this->fail('A descendant cannot become its ancestor\'s parent.');
            } catch (ValidationException $exception) {
                $this->assertArrayHasKey('parent_id', $exception->errors());
            }
            $this->assertNull($root->fresh()->parent_id);
        }
    }

    public function test_product_picker_excludes_branches_and_parent_picker_excludes_occupied_categories(): void
    {
        $root = ProductCategory::create(['name' => 'Thiết bị']);
        $leaf = ProductCategory::create(['name' => 'Đầu báo', 'parent_id' => $root->id]);
        $occupied = ProductCategory::create(['name' => 'Bình chữa cháy']);
        Product::create(['title' => 'Bình ABC', 'product_category_id' => $occupied->id]);
        $this->assertSame([$leaf->id => 'Thiết bị > Đầu báo', $occupied->id => 'Bình chữa cháy'], CategoryTree::leafOptions(ProductCategory::class));
        $this->assertArrayNotHasKey($occupied->id, CategoryTree::parentOptions(ProductCategory::class));
        $this->expectException(ValidationException::class);
        Product::create(['title' => 'Không hợp lệ', 'product_category_id' => $root->id]);
    }

    public function test_occupied_product_category_cannot_gain_children(): void
    {
        $category = ProductCategory::create(['name' => 'Có sản phẩm']);
        Product::create(['title' => 'Sản phẩm', 'product_category_id' => $category->id]);
        $this->expectException(ValidationException::class);
        ProductCategory::create(['name' => 'Con không hợp lệ', 'parent_id' => $category->id]);
    }

    public function test_category_cannot_be_its_own_parent_or_be_deleted_with_children(): void
    {
        $root = ProductCategory::create(['name' => 'Gốc']);
        try {
            $root->update(['parent_id' => $root->id]);
            $this->fail('Self parenting must fail.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('parent_id', $exception->errors());
        }
        $root->refresh();
        ProductCategory::create(['name' => 'Con', 'parent_id' => $root->id]);
        $this->expectException(ValidationException::class);
        $root->delete();
    }
}
