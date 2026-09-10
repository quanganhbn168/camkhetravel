<?php

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use App\Providers\FrontendServiceProvider;
use App\Providers\TrackingServiceProvider;

return [
    TrackingServiceProvider::class,
    AppServiceProvider::class,
    AdminPanelProvider::class,
    FrontendServiceProvider::class,
];
