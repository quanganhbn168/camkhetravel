<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LandingTemplate extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'palette' => 'array',
            'settings_schema' => 'array',
            'default_settings' => 'array',
            'default_sections' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function landings(): HasMany
    {
        return $this->hasMany(Landing::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /** @return array<string, mixed> */
    public function toRegistryDefinition(): array
    {
        return [
            'id' => $this->id,
            'label' => $this->name,
            'description' => $this->description ?? '',
            'view' => $this->view_name,
            'css_class' => $this->css_class,
            'css_source' => $this->css_source,
            'use_case' => $this->use_case ?? '',
            'source_name' => $this->source_name,
            'source_path' => $this->source_path,
            'icon' => $this->icon,
            'palette' => is_array($this->palette) ? $this->palette : [],
            'settings_schema' => is_array($this->settings_schema) ? $this->settings_schema : [],
            'settings' => is_array($this->default_settings) ? $this->default_settings : [],
            'default_sections' => is_array($this->default_sections) ? $this->default_sections : [],
            'is_active' => $this->is_active,
            'sort_order' => $this->sort_order,
        ];
    }
}
