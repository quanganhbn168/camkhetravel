<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Posts\PostResource;
use App\Filament\Resources\Projects\ProjectResource;
use App\Filament\Resources\Services\ServiceResource;
use App\Models\Post;
use App\Models\Project;
use App\Models\Service;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Support\Icons\Heroicon;

class WebsiteStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $month = now();
        $postsCreatedThisMonth = Post::query()
            ->whereBetween('created_at', [$month->copy()->startOfMonth(), $month->copy()->endOfMonth()])
            ->count();

        return [
            Stat::make('Dịch vụ đang hiển thị', Service::query()->published()->count())
                ->description('Nội dung dịch vụ công khai')
                ->descriptionIcon(Heroicon::OutlinedBriefcase)
                ->color('info')
                ->url(ServiceResource::getUrl('index')),
            Stat::make('Dự án đang hiển thị', Project::query()->published()->count())
                ->description('Hồ sơ năng lực công khai')
                ->descriptionIcon(Heroicon::OutlinedPhoto)
                ->color('success')
                ->url(ProjectResource::getUrl('index')),
            Stat::make('Bài viết đang hiển thị', Post::query()->published()->count())
                ->description('Nội dung bài viết công khai')
                ->descriptionIcon(Heroicon::OutlinedNewspaper)
                ->color('primary')
                ->url(PostResource::getUrl('index')),
            Stat::make('Bài viết tạo trong tháng này', $postsCreatedThisMonth)
                ->description('created_at · tháng '.$month->format('m/Y'))
                ->descriptionIcon(Heroicon::OutlinedCalendarDays)
                ->color('warning')
                ->url(PostResource::getUrl('index')),
        ];
    }
}
