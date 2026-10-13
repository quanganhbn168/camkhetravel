<?php

namespace App\Filament\Resources\Partners\Pages;

use App\Filament\Resources\Partners\PartnerResource;
use App\Models\Partner;
use Awcodes\Curator\Models\Media;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreatePartner extends CreateRecord
{
    protected static string $resource = PartnerResource::class;

    /** Mỗi logo được chọn tạo một đối tác, nối tiếp thứ tự hiện có. */
    protected function handleRecordCreation(array $data): Model
    {
        $ids = collect($data['logos'] ?? [])
            ->map(fn (mixed $item): mixed => is_array($item) ? ($item['id'] ?? $item['media_id'] ?? null) : $item)
            ->filter(fn (mixed $id): bool => is_numeric($id))
            ->map(fn (mixed $id): int => (int) $id)
            ->unique()
            ->values();

        $media = Media::query()->whereKey($ids->all())->get()->keyBy('id');
        $order = (int) Partner::query()->max('sort_order');
        $created = collect();

        foreach ($ids as $id) {
            $logo = $media->get($id);

            if (! $logo) {
                continue;
            }

            $created->push(Partner::query()->create([
                'curator_media_id' => $logo->getKey(),
                'is_active' => true,
                'sort_order' => ++$order,
            ]));
        }

        $this->createdCount = $created->count();

        return $created->first() ?? new Partner;
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Đã thêm '.$this->createdCount.' đối tác');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    private int $createdCount = 0;
}
