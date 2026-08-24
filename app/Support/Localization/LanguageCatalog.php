<?php

namespace App\Support\Localization;

use App\Models\Language;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class LanguageCatalog
{
    private ?Collection $languages = null;

    /**
     * The database is the runtime source of truth. The config list is only a
     * safe bootstrap fallback while migrations are being run.
     *
     * @return Collection<string, Language>
     */
    public function all(): Collection
    {
        if ($this->languages !== null) {
            return $this->languages;
        }

        if (Schema::hasTable('languages')) {
            return $this->languages = Language::query()
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get()
                ->keyBy('code');
        }

        return $this->languages = collect(config('locales.supported', []))
            ->map(function (array $language, string $code): Language {
                return new Language([
                    'code' => $code,
                    'name' => $language['label'],
                    'native_name' => $language['native'],
                    'og_locale' => $language['og_locale'],
                    'is_active' => true,
                    'is_default' => $code === config('locales.default'),
                    'is_indexable' => $code === config('locales.default'),
                ]);
            });
    }

    /** @return Collection<string, Language> */
    public function active(): Collection
    {
        return $this->all()->filter(fn (Language $language): bool => $language->is_active);
    }

    /** @return Collection<string, Language> */
    public function indexable(): Collection
    {
        return $this->active()->filter(fn (Language $language): bool => $language->is_indexable);
    }

    public function find(string $code): ?Language
    {
        return $this->active()->get($code);
    }

    public function default(): Language
    {
        return $this->all()->firstWhere('is_default', true)
            ?? $this->all()->first()
            ?? new Language([
                'code' => config('locales.default'),
                'name' => 'Tiếng Việt',
                'native_name' => 'VI',
                'og_locale' => 'vi_VN',
                'is_active' => true,
                'is_default' => true,
                'is_indexable' => true,
            ]);
    }

    public function defaultCode(): string
    {
        return (string) $this->default()->code;
    }

    public function isSupported(string $code): bool
    {
        return $this->active()->has($code);
    }

    public function isIndexable(string $code): bool
    {
        return $this->indexable()->has($code);
    }

    public function ogLocale(string $code): string
    {
        return (string) ($this->find($code)?->og_locale ?? $this->default()->og_locale ?? 'vi_VN');
    }

    public function languageTag(string $code): string
    {
        return str_replace('_', '-', $this->ogLocale($code));
    }

    /** @return array<string, string> */
    public function options(): array
    {
        return $this->active()
            ->mapWithKeys(fn (Language $language): array => [$language->code => $language->name])
            ->all();
    }

    public function forget(): void
    {
        $this->languages = null;
    }
}
