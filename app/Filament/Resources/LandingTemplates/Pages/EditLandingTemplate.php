<?php

namespace App\Filament\Resources\LandingTemplates\Pages;

use App\Filament\Resources\LandingTemplates\LandingTemplateResource;
use Filament\Resources\Pages\EditRecord;

class EditLandingTemplate extends EditRecord
{
    protected static string $resource = LandingTemplateResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
