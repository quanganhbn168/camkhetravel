<?php

namespace App\Filament\Resources\MediaAssets\Pages\Concerns;

use App\Models\MediaAsset;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait NormalizesMediaAssetData
{
    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function normalizeMediaAssetData(array $data, bool $isCreating = false): array
    {
        $data['disk'] = 'public';

        if ($isCreating) {
            $data['source'] = 'native';
            $data['source_id'] = filled($data['source_id'] ?? null)
                ? $data['source_id']
                : $this->nextNativeSourceId();
            $data['published_at'] ??= now();
        } elseif (isset($this->record)) {
            $sourceFields = [
                'title',
                'slug',
                'effective_alt_text',
                'caption',
                'description',
                'file_path',
            ];
            $lockedFields = array_values(array_filter(
                (array) $this->record->import_locked_fields,
                'is_string',
            ));

            foreach ($sourceFields as $field) {
                if (array_key_exists($field, $data) && $data[$field] !== $this->record->{$field}) {
                    $lockedFields[] = $field;

                    if ($field === 'file_path') {
                        $lockedFields = [
                            ...$lockedFields,
                            'mime_type',
                            'width',
                            'height',
                            'file_size',
                        ];
                    }
                }
            }

            $data['import_locked_fields'] = array_values(array_unique($lockedFields));
        }

        if (blank($data['slug'] ?? null) && filled($data['title'] ?? null)) {
            $data['slug'] = Str::slug((string) $data['title']);
        }

        $filePath = $data['file_path'] ?? null;

        if (! is_string($filePath) || blank($filePath)) {
            return $data;
        }

        $disk = Storage::disk('public');

        if (! $disk->exists($filePath)) {
            return $data;
        }

        $absolutePath = $disk->path($filePath);
        $dimensions = getimagesize($absolutePath);

        $data['mime_type'] = $disk->mimeType($filePath) ?: ($data['mime_type'] ?? null);
        $data['file_size'] = $disk->size($filePath);
        $data['width'] = is_array($dimensions) ? $dimensions[0] : ($data['width'] ?? null);
        $data['height'] = is_array($dimensions) ? $dimensions[1] : ($data['height'] ?? null);
        $data['checksum_sha256'] = hash_file('sha256', $absolutePath);
        $data['localization_status'] = 'localized';
        $data['localized_at'] = now();
        $data['localization_error'] = null;

        return $data;
    }

    private function nextNativeSourceId(): int
    {
        $currentMaximum = (int) MediaAsset::query()->max('source_id');

        return max(1_000_000_000, $currentMaximum + 1);
    }
}
