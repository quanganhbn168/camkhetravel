<?php

namespace App\Filament\Resources\Menus\Pages\Concerns;

use App\Filament\Resources\Menus\MenuResource;
use App\Models\LandingPage;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Service;
use App\Models\ServiceCategory;
use Filament\Forms\Components\Repeater;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;

trait InteractsWithMenuBuilder
{
    public string $customMenuLabel = '';

    public string $customMenuUrl = '';

    public function addMenuItemFromSource(string $sourceKey): void
    {
        [$type, $sourceId] = array_pad(explode(':', $sourceKey, 2), 2, null);

        if (blank($type) || blank($sourceId)) {
            return;
        }

        $item = $this->sourceItemState($type, $sourceId);

        if ($item === null) {
            Notification::make()
                ->danger()
                ->title('Không tìm thấy nội dung')
                ->body('Mục này có thể đã bị xoá hoặc không còn được xuất bản.')
                ->send();

            return;
        }

        $this->appendMenuItem($item);

        Notification::make()
            ->success()
            ->title('Đã thêm mục vào menu')
            ->body('Nhấn Lưu thay đổi để ghi cấu trúc mới.')
            ->send();
    }

    public function addCustomMenuItem(): void
    {
        $label = trim($this->customMenuLabel);
        $url = trim($this->customMenuUrl);

        if ($label === '' || $url === '' || $url === '#') {
            Notification::make()
                ->danger()
                ->title('Thiếu thông tin liên kết')
                ->body('Vui lòng nhập nhãn hiển thị và URL hợp lệ.')
                ->send();

            return;
        }

        if (! filter_var($url, FILTER_VALIDATE_URL) && ! Str::startsWith($url, '/')) {
            Notification::make()
                ->danger()
                ->title('URL chưa hợp lệ')
                ->body('Dùng URL đầy đủ hoặc đường dẫn nội bộ bắt đầu bằng /.')
                ->send();

            return;
        }

        $this->appendMenuItem([
            'label' => $label,
            'linked_source_type' => 'custom',
            'linked_source_id' => null,
            'url' => $url,
            'target' => '_self',
            'css_classes' => null,
            'children' => [],
        ]);

        $this->reset('customMenuLabel', 'customMenuUrl');

        Notification::make()
            ->success()
            ->title('Đã thêm link custom')
            ->body('Nhấn Lưu thay đổi để ghi cấu trúc mới.')
            ->send();
    }

    /** @return array<string, mixed> | null */
    private function sourceItemState(string $type, string $sourceId): ?array
    {
        if ($type === 'route') {
            $label = MenuResource::routeOptions()[$sourceId] ?? null;

            return $label === null ? null : [
                'label' => $label,
                'linked_source_type' => 'native_route',
                'linked_source_id' => null,
                'url' => $sourceId,
                'target' => '_self',
                'css_classes' => null,
                'children' => [],
            ];
        }

        $source = match ($type) {
            'service' => Service::query()->published()->find($sourceId),
            'service_category' => ServiceCategory::query()->where('is_active', true)->find($sourceId),
            'landing_page' => LandingPage::query()->published()->find($sourceId),
            'project' => Project::query()->published()->find($sourceId),
            'project_category' => ProjectCategory::query()->where('is_active', true)->find($sourceId),
            'post' => Post::query()->published()->find($sourceId),
            'post_category' => PostCategory::query()->where('is_active', true)->find($sourceId),
            default => null,
        };

        if ($source === null) {
            return null;
        }

        return [
            'label' => (string) ($source->title ?? $source->name),
            'linked_source_type' => 'native_'.$type,
            'linked_source_id' => $source->getKey(),
            'url' => null,
            'target' => '_self',
            'css_classes' => null,
            'children' => [],
        ];
    }

    /** @param array<string, mixed> $item */
    private function appendMenuItem(array $item): void
    {
        $component = $this->form->getComponent('topLevelItems', withHidden: true);
        $key = (string) Str::uuid();

        if (! $component instanceof Repeater) {
            $this->data['topLevelItems'][$key] = $item;

            return;
        }

        $state = $component->getRawState();
        $state[$key] = $item;
        $component->rawState($state);
        $component->callAfterStateUpdated();
        $component->partiallyRender();
    }
}
