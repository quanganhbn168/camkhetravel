<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return ['is_public' => 'boolean'];
    }

    public function decodedValue(): mixed
    {
        return $this->type === 'json'
            ? json_decode((string) $this->value, true)
            : $this->value;
    }
}
