<?php

namespace App\Filament\Pages;

use App\Filament\Resources\Comments\CommentResource;
use App\Filament\Resources\ContactRequests\ContactRequestResource;
use App\Filament\Resources\LandingPages\LandingPageResource;
use App\Filament\Resources\Posts\PostResource;
use App\Filament\Resources\Projects\ProjectResource;
use App\Filament\Resources\Services\ServiceResource;
use App\Filament\Widgets\WebsiteStatsOverview;
use App\Models\Comment;
use App\Models\ContactRequest;
use App\Models\LandingPage;
use App\Models\LandingTemplate;
use App\Models\Post;
use App\Models\Project;
use App\Models\Service;
use Awcodes\Curator\Models\Media;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Panel;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use UnitEnum;

class AdminDashboard extends Page
{
    protected static ?string $slug = 'dashboard';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    protected static ?string $navigationLabel = 'Tổng quan';

    protected static ?string $title = 'Tổng quan website';

    protected static string|UnitEnum|null $navigationGroup = null;

    protected static ?int $navigationSort = -2;

    protected static bool $shouldRegisterNavigation = true;

    protected string $view = 'filament.pages.admin-dashboard';

    public static function getRoutePath(Panel $panel): string
    {
        return '/';
    }

    /** @return array<int, class-string> */
    protected function getHeaderWidgets(): array
    {
        return [WebsiteStatsOverview::class];
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        return 1;
    }

    /** @return array<int, Action> */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('content')
                ->label('Quản lý nội dung')
                ->icon(Heroicon::OutlinedDocumentText)
                ->url(ServiceResource::getUrl('index')),
            Action::make('landings')
                ->label('Quản lý landing page')
                ->icon(Heroicon::OutlinedRectangleGroup)
                ->url(LandingPageResource::getUrl('index')),
            Action::make('tracking')
                ->label('Xem tracking')
                ->icon(Heroicon::OutlinedChartBarSquare)
                ->color('gray')
                ->url(LandingTrackingDashboard::getUrl()),
        ];
    }

    /**
     * @return array{
     *     operations: list<array{label: string, value: int, hint: string, url: string}>,
     *     recent_leads: \Illuminate\Database\Eloquent\Collection<int, ContactRequest>,
     *     recent_content: Collection<int, array{label: string, title: string, status: string, status_label: string, updated_at: mixed, url: string}>,
     *     template_count: int,
     *     active_template_count: int,
     * }
     */
    public function getWebsiteOverviewData(): array
    {
        $serviceCount = Service::query()->count();
        $projectCount = Project::query()->count();
        $postCount = Post::query()->count();
        $landingCount = LandingPage::query()->count();

        return [
            'operations' => [
                [
                    'label' => 'Yêu cầu tư vấn mới',
                    'value' => ContactRequest::query()->where('status', 'new')->count(),
                    'hint' => 'Lead cần được xử lý',
                    'url' => ContactRequestResource::getUrl('index'),
                ],
                [
                    'label' => 'Bình luận chờ duyệt',
                    'value' => Comment::query()->where('status', Comment::STATUS_PENDING)->count(),
                    'hint' => 'Nội dung cần kiểm duyệt',
                    'url' => CommentResource::getUrl('index'),
                ],
                [
                    'label' => 'File trong thư viện media',
                    'value' => Media::query()->count(),
                    'hint' => 'Tài nguyên đang quản lý',
                    'url' => url('/admin/media'),
                ],
            ],
            'recent_leads' => ContactRequest::query()
                ->with(['service:id,title', 'landingPage:id,title'])
                ->latest('created_at')
                ->limit(6)
                ->get([
                    'id',
                    'name',
                    'phone',
                    'service_id',
                    'landing_page_id',
                    'status',
                    'created_at',
                ]),
            'recent_content' => $this->recentContent($serviceCount, $projectCount, $postCount, $landingCount),
            'template_count' => LandingTemplate::query()->count(),
            'active_template_count' => LandingTemplate::query()->active()->count(),
        ];
    }

    /**
     * @return Collection<int, array{label: string, title: string, status: string, status_label: string, updated_at: mixed, url: string}>
     */
    private function recentContent(int $serviceCount, int $projectCount, int $postCount, int $landingCount): Collection
    {
        $items = collect();

        if ($serviceCount > 0) {
            $items = $items->merge(Service::query()
                ->latest('updated_at')
                ->limit(3)
                ->get(['id', 'title', 'status', 'updated_at'])
                ->map(fn (Service $record): array => $this->contentRow(
                    'Dịch vụ',
                    $record->title,
                    $record->status,
                    $record->updated_at,
                    ServiceResource::getUrl('edit', ['record' => $record->id]),
                )));
        }

        if ($projectCount > 0) {
            $items = $items->merge(Project::query()
                ->latest('updated_at')
                ->limit(3)
                ->get(['id', 'title', 'status', 'updated_at'])
                ->map(fn (Project $record): array => $this->contentRow(
                    'Dự án',
                    $record->title,
                    $record->status,
                    $record->updated_at,
                    ProjectResource::getUrl('edit', ['record' => $record->id]),
                )));
        }

        if ($postCount > 0) {
            $items = $items->merge(Post::query()
                ->latest('updated_at')
                ->limit(3)
                ->get(['id', 'title', 'status', 'updated_at'])
                ->map(fn (Post $record): array => $this->contentRow(
                    'Bài viết',
                    $record->title,
                    $record->status,
                    $record->updated_at,
                    PostResource::getUrl('edit', ['record' => $record->id]),
                )));
        }

        if ($landingCount > 0) {
            $items = $items->merge(LandingPage::query()
                ->latest('updated_at')
                ->limit(3)
                ->get(['id', 'title', 'status', 'updated_at'])
                ->map(fn (LandingPage $record): array => $this->contentRow(
                    'Landing page',
                    $record->title,
                    $record->status,
                    $record->updated_at,
                    LandingPageResource::getUrl('edit', ['record' => $record->id]),
                )));
        }

        return $items
            ->sortByDesc('updated_at')
            ->take(8)
            ->values();
    }

    /** @return array{label: string, title: string, status: string, status_label: string, updated_at: mixed, url: string} */
    private function contentRow(string $label, ?string $title, ?string $status, mixed $updatedAt, string $url): array
    {
        return [
            'label' => $label,
            'title' => $title ?: 'Chưa đặt tiêu đề',
            'status' => $status ?: 'draft',
            'status_label' => match ($status) {
                'published' => 'Đang hiển thị',
                'archived' => 'Lưu trữ',
                default => 'Bản nháp',
            },
            'updated_at' => $updatedAt,
            'url' => $url,
        ];
    }
}
