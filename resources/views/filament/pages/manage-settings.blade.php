<x-filament-panels::page>
    <form wire:submit="save" class="space-y-6">
        {{ $this->form }}

        <div class="fi-form-actions">
            <x-filament::button type="submit">
                Lưu cài đặt
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
