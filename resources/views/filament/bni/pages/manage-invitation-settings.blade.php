<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}

        <div class="fi-form-actions">
            <x-filament::button type="submit">
                Lưu mẫu thư mời
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
