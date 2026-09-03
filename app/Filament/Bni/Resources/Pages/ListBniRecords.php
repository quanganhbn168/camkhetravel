<?php

namespace App\Filament\Bni\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;

abstract class ListBniRecords extends ListRecords
{
    protected Width|string|null $maxContentWidth = Width::Full;
}
