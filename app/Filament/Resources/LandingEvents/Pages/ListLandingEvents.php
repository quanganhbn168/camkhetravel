<?php

namespace App\Filament\Resources\LandingEvents\Pages;

use App\Filament\Resources\LandingEvents\LandingEventResource;
use Filament\Resources\Pages\ListRecords;

class ListLandingEvents extends ListRecords
{
    protected static string $resource = LandingEventResource::class;
}
