<?php

namespace App\Services\WordPress;

class WordPressValueDecoder
{
    public function decode(mixed $value): mixed
    {
        if (! is_string($value) || $value === '') {
            return $value;
        }

        if (! $this->looksSerialized($value)) {
            return $value;
        }

        $decoded = @unserialize($value, ['allowed_classes' => false]);

        return ($decoded === false && $value !== 'b:0;') ? $value : $decoded;
    }

    private function looksSerialized(string $value): bool
    {
        if ($value === 'N;') {
            return true;
        }

        if (strlen($value) < 4 || $value[1] !== ':') {
            return false;
        }

        return match ($value[0]) {
            's' => str_ends_with($value, ';'),
            'a', 'O', 'E' => str_ends_with($value, '}'),
            'b', 'i', 'd' => str_ends_with($value, ';'),
            default => false,
        };
    }
}
