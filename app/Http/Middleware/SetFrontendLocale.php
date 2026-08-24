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
        $languages = app(LanguageCatalog::class);
        $routeLocale = $request->route('locale');
        $locale = (string) ($routeLocale ?: $languages->defaultCode());

        abort_unless($languages->isSupported($locale), 404);

        app()->setLocale($locale);

        return $next($request);
    }
}
