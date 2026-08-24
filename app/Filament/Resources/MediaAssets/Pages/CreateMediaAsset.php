<?php

namespace App\Filament\Resources\MediaAssets\Pages;

use App\Filament\Resources\MediaAssets\MediaAssetResource;
use App\Filament\Resources\MediaAssets\Pages\Concerns\NormalizesMediaAssetData;
use Filament\Resources\Pages\CreateRecord;

class CreateMediaAsset extends CreateRecord
{
    use NormalizesMediaAssetData;

    protected static string $resource = MediaAssetResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $this->normalizeMediaAssetData($data, isCreating: true);
    }
}
