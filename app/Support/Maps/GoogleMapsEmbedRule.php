<?php

namespace App\Support\Maps;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

final class GoogleMapsEmbedRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (is_string($value) && trim($value) !== '' && GoogleMapsUrl::normalizeEmbed($value) === null) {
            $fail('Hãy dán URL nhúng Google Maps hoặc nguyên thẻ iframe hợp lệ. Link maps.app.goo.gl nhập ở ô Google Maps link.');
        }
    }
}
