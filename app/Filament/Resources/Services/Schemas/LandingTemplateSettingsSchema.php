<?php

namespace App\Filament\Resources\Services\Schemas;

use App\Support\Landing\LandingTemplateRegistry;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

class LandingTemplateSettingsSchema
{
    /** @return array<Section> */
    public static function sections(): array
    {
        $sections = [];

        foreach (LandingTemplateRegistry::all() as $key => $template) {
            $schema = is_array($template['settings_schema'] ?? null)
                ? $template['settings_schema']
                : [];
            $fields = is_array($schema['fields'] ?? null) ? $schema['fields'] : [];

            if ($fields === []) {
                continue;
            }

            $sections[] = Section::make((string) ($schema['title'] ?? 'Thiết lập '.$template['label']))
                ->icon((string) ($schema['icon'] ?? Heroicon::OutlinedAdjustmentsHorizontal->value))
                ->description((string) ($schema['description'] ?? 'Các trường riêng của template đang chọn.'))
                ->visible(fn ($get): bool => $get('layout_mode') === 'custom_template'
                    && $get('template_key') === $key)
                ->schema(array_values(array_map(
                    fn (array $field) => self::field($field),
                    array_filter($fields, 'is_array'),
                )))
                ->columns(max(1, min(4, (int) ($schema['columns'] ?? 2))));
        }

        return $sections;
    }

    private static function field(array $definition): mixed
    {
        $key = trim((string) ($definition['key'] ?? ''));
        $path = 'template_settings.'.$key;
        $type = (string) ($definition['type'] ?? 'text');
        $label = (string) ($definition['label'] ?? $key);

        $field = match ($type) {
            'textarea' => Textarea::make($path)
                ->rows(max(2, min(12, (int) ($definition['rows'] ?? 3))))
                ->maxLength((int) ($definition['max_length'] ?? 2000)),
            'tags' => TagsInput::make($path)
                ->placeholder('Nhập một ý rồi nhấn Enter'),
            'media' => CuratorPicker::make($path)
                ->disk('public')
                ->constrained()
                ->acceptedFileTypes(['video/*']),
            'url' => TextInput::make($path)
                ->url()
                ->maxLength((int) ($definition['max_length'] ?? 2048)),
            default => TextInput::make($path)
                ->maxLength((int) ($definition['max_length'] ?? 255)),
        };

        $field->label($label);

        if (filled($definition['helper_text'] ?? null)) {
            $field->helperText((string) $definition['helper_text']);
        }

        if ((bool) ($definition['full_width'] ?? false) || in_array($type, ['textarea', 'tags', 'media'], true)) {
            $field->columnSpanFull();
        }

        return $field;
    }
}
