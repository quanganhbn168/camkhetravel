<?php

namespace App\Http\Middleware;

use App\Support\Localization\LanguageCatalog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetFrontendLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        app()->setLocale(app(LanguageCatalog::class)->defaultCode());

        return $next($request);
    }
}
