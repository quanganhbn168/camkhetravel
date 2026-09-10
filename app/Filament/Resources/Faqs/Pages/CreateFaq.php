<?php

namespace App\Filament\Resources\Faqs\Pages;

use App\Filament\Resources\Faqs\FaqResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFaq extends CreateRecord
{
    protected static string $resource = FaqResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (($data['faqable_type'] ?? 'homepage') === 'homepage') {
            $data['faqable_type'] = null;
            $data['faqable_id'] = null;
            $data['group'] = 'homepage';
        }

        return $data;
    }
}
