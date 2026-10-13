<?php

namespace App\Filament;

use App\Filament\Pages\AdminDashboard;
use App\Filament\Pages\ManageSettings;
use App\Filament\Pages\ManageWebsiteSettings;
use App\Filament\Resources\Comments\CommentResource;
use App\Filament\Resources\ContactRequests\ContactRequestResource;
use App\Filament\Resources\Faqs\FaqResource;
use App\Filament\Resources\HeroSlides\HeroSlideResource;
use App\Filament\Resources\Intros\IntroResource;
use App\Filament\Resources\Menus\MenuResource;
use App\Filament\Resources\Page\PageResource;
use App\Filament\Resources\Partners\PartnerResource;
use App\Filament\Resources\PostCategories\PostCategoryResource;
use App\Filament\Resources\Posts\PostResource;
use App\Filament\Resources\ProductCategories\ProductCategoryResource;
use App\Filament\Resources\Products\ProductResource;
use App\Filament\Resources\ServiceCategories\ServiceCategoryResource;
use App\Filament\Resources\Services\ServiceResource;
use App\Filament\Resources\SolutionCategories\SolutionCategoryResource;
use App\Filament\Resources\Solutions\SolutionResource;
use App\Filament\Resources\Tags\TagResource;
use App\Filament\Resources\Testimonials\TestimonialResource;
use Awcodes\Curator\Resources\Media\MediaResource;
use BezhanSalleh\FilamentShield\Resources\Roles\RoleResource;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Support\Icons\Heroicon;

/**
 * Sidebar quản trị: thứ tự, nhóm, tên và icon được quyết định ở đây (một chỗ duy nhất),
 * theo luồng làm việc: nội dung → dịch vụ/giải pháp → sản phẩm → trang chủ → khách hàng → cài đặt.
 * Mục nào người dùng không có quyền sẽ tự bị ẩn.
 */
final class AdminNavigation
{
    /**
     * Mỗi nhóm: [tên nhóm, thu gọn mặc định, [lớp => [tên hiển thị, icon]]].
     *
     * @return list<array{0: string, 1: bool, 2: array<class-string, array{0: string, 1: Heroicon}>}>
     */
    private static function groups(): array
    {
        return [
            ['Nội dung', false, [
                PageResource::class => ['Trang', Heroicon::OutlinedDocumentDuplicate],
                PostResource::class => ['Bài viết', Heroicon::OutlinedNewspaper],
                PostCategoryResource::class => ['Danh mục bài viết', Heroicon::OutlinedFolder],
                IntroResource::class => ['Bài giới thiệu', Heroicon::OutlinedDocumentText],
                TagResource::class => ['Thẻ', Heroicon::OutlinedHashtag],
                FaqResource::class => ['Câu hỏi thường gặp', Heroicon::OutlinedQuestionMarkCircle],
            ]],
            ['Dịch vụ & giải pháp', false, [
                ServiceResource::class => ['Dịch vụ', Heroicon::OutlinedBriefcase],
                ServiceCategoryResource::class => ['Danh mục dịch vụ', Heroicon::OutlinedFolder],
                SolutionResource::class => ['Giải pháp', Heroicon::OutlinedLightBulb],
                SolutionCategoryResource::class => ['Danh mục giải pháp', Heroicon::OutlinedFolder],
            ]],
            ['Sản phẩm', false, [
                ProductResource::class => ['Sản phẩm', Heroicon::OutlinedCube],
                ProductCategoryResource::class => ['Danh mục sản phẩm', Heroicon::OutlinedFolder],
            ]],
            ['Trang chủ', true, [
                HeroSlideResource::class => ['Banner trang chủ', Heroicon::OutlinedPhoto],
                PartnerResource::class => ['Đối tác', Heroicon::OutlinedBuildingOffice2],
                TestimonialResource::class => ['Đánh giá khách hàng', Heroicon::OutlinedStar],
            ]],
            ['Khách hàng', false, [
                ContactRequestResource::class => ['Yêu cầu tư vấn', Heroicon::OutlinedChatBubbleLeftRight],
                CommentResource::class => ['Bình luận', Heroicon::OutlinedChatBubbleBottomCenterText],
            ]],
            ['Cài đặt', true, [
                ManageSettings::class => ['Cài đặt website', Heroicon::OutlinedCog6Tooth],
                ManageWebsiteSettings::class => ['Cài đặt chung', Heroicon::OutlinedAdjustmentsHorizontal],
                MenuResource::class => ['Menu', Heroicon::OutlinedBars3],
                MediaResource::class => ['Thư viện media', Heroicon::OutlinedPhoto],
                RoleResource::class => ['Vai trò & quyền', Heroicon::OutlinedShieldCheck],
            ]],
        ];
    }

    public static function build(NavigationBuilder $builder): NavigationBuilder
    {
        $groups = [];

        foreach (self::groups() as [$label, $collapsed, $entries]) {
            $items = self::items($entries);

            if ($items === []) {
                continue;
            }

            $groups[] = NavigationGroup::make($label)->items($items)->collapsed($collapsed);
        }

        return $builder
            ->items(self::items([AdminDashboard::class => ['Tổng quan', Heroicon::OutlinedHome]]))
            ->groups($groups);
    }

    /**
     * @param  array<class-string, array{0: string, 1: Heroicon}>  $entries
     * @return list<NavigationItem>
     */
    private static function items(array $entries): array
    {
        $items = [];

        foreach ($entries as $class => [$label, $icon]) {
            if (! class_exists($class) || ! $class::shouldRegisterNavigation() || ! $class::canAccess()) {
                continue;
            }

            foreach ($class::getNavigationItems() as $item) {
                $items[] = $item->label($label)->icon($icon)->activeIcon($icon);
            }
        }

        return $items;
    }
}
