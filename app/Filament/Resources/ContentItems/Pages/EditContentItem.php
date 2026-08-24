<?php

namespace App\Filament\Resources\ContentItems\Pages;

use App\Filament\Resources\ContentItems\ContentItemResource;
use App\Support\Seo\SeoMaterializer;
use DateTimeInterface;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditContentItem extends EditRecord
{
    protected static string $resource = ContentItemResource::class;

    private const SEO_FIELDS = [
        'seo_title',
        'seo_description',
        'seo_canonical_url',
        'seo_robots',
        'focus_keyword',
        'is_pillar_content',
        'exclude_from_sitemap',
        'og_title',
        'og_description',
        'og_image_url',
        'twitter_title',
        'twitter_description',
        'twitter_image_url',
        'structured_data',
    ];

    private const SOURCE_FIELDS = [
        'type',
        'status',
        'title',
        'slug',
        'canonical_path',
        'parent_id',
        'menu_order',
        'excerpt',
        'body',
        'featured_media_source_id',
        'published_at',
        ...self::SEO_FIELDS,
    ];

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $lockedFields = array_values(array_filter(
            (array) $this->record->import_locked_fields,
            'is_string',
        ));

        foreach (self::SOURCE_FIELDS as $field) {
            if (
                array_key_exists($field, $data)
                && $this->comparableValue($data[$field])
                    !== $this->comparableValue($this->record->{$field})
            ) {
                $lockedFields[] = $field;
            }
        }

        foreach (self::SEO_FIELDS as $field) {
            if (
                array_key_exists($field, $data)
                && $this->comparableValue($data[$field])
                    !== $this->comparableValue($this->record->{$field})
            ) {
                $data['seo_source'] = 'manual';

                break;
            }
        }

        $data['import_locked_fields'] = array_values(array_unique($lockedFields));

        return $data;
    }

    private function comparableValue(mixed $value): string
    {
        if ($value instanceof DateTimeInterface) {
            return $value->format('Y-m-d H:i:s');
        }

        if (is_array($value)) {
            ksort($value);

            return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '';
        }

        return (string) ($value ?? '');
    }

    protected function afterSave(): void
    {
        app(SeoMaterializer::class)->materializeItem($this->record->refresh());
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
