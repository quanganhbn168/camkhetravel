<?php

namespace App\Filament\Resources\Faqs\Pages;

use App\Filament\Resources\Faqs\FaqResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFaq extends EditRecord
{
    protected static string $resource = FaqResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['faqable_type'] = $data['faqable_type'] ?: 'homepage';

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (($data['faqable_type'] ?? 'homepage') === 'homepage') {
            $data['faqable_type'] = null;
            $data['faqable_id'] = null;
            $data['group'] = 'homepage';
        }

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
