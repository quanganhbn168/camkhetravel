<?php

namespace App\Filament\Forms\Components;

use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Actions\Action;

class GalleryPicker extends CuratorPicker
{
    public function getRemoveAllAction(): Action
    {
        return parent::getRemoveAllAction()
            ->label('Xóa tất cả')
            ->requiresConfirmation()
            ->modalHeading('Xóa toàn bộ ảnh trong gallery?')
            ->modalDescription('Các ảnh chỉ được gỡ khỏi khu vực này, không bị xóa khỏi Thư viện media.')
            ->modalSubmitActionLabel('Xóa tất cả');
    }
}
