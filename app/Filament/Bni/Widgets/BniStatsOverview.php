<?php

namespace App\Filament\Bni\Widgets;

use App\Models\BniGalleryItem;
use App\Models\BniInvitation;
use App\Models\BniRegistration;
use App\Models\Comment;
use App\Support\Bni\BniPanelAccess;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;

class BniStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $invitations = BniPanelAccess::scopeChapter(BniInvitation::query())->count();
        $registrations = BniPanelAccess::scopeChapter(BniRegistration::query())->count();
        $pendingPhotos = BniPanelAccess::scopeChapter(BniGalleryItem::query())
            ->where('status', BniGalleryItem::STATUS_PENDING)
            ->count();
        $pendingComments = Comment::query()
            ->where('commentable_type', (new BniGalleryItem)->getMorphClass())
            ->where('status', Comment::STATUS_PENDING)
            ->when(
                BniPanelAccess::isChapterManager() && ! BniPanelAccess::canManageEverything(),
                fn (Builder $query): Builder => $query->whereHasMorph(
                    'commentable',
                    [BniGalleryItem::class],
                    fn (Builder $galleryQuery): Builder => $galleryQuery->where('bni_chapter_id', BniPanelAccess::chapterId() ?? 0),
                ),
            )
            ->count();

        return [
            Stat::make('Khách mời', $invitations)
                ->description('Thư mời riêng theo chapter')
                ->descriptionIcon('heroicon-o-envelope')
                ->color('primary'),
            Stat::make('RSVP & đăng ký', $registrations)
                ->description('Lễ chuyển giao và Pickleball')
                ->descriptionIcon('heroicon-o-ticket')
                ->color('info'),
            Stat::make('Ảnh chờ duyệt', $pendingPhotos)
                ->description('Ảnh do khách tham dự gửi')
                ->descriptionIcon('heroicon-o-photo')
                ->color($pendingPhotos > 0 ? 'warning' : 'success'),
            Stat::make('Bình luận chờ duyệt', $pendingComments)
                ->description('Bình luận trong thư viện ảnh')
                ->descriptionIcon('heroicon-o-chat-bubble-left-right')
                ->color($pendingComments > 0 ? 'warning' : 'success'),
        ];
    }
}
