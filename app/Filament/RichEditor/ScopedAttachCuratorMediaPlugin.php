<?php

namespace App\Filament\RichEditor;

use Awcodes\Curator\Components\Forms\RichEditor\AttachCuratorMediaPlugin;
use Illuminate\Support\Facades\Vite;

class ScopedAttachCuratorMediaPlugin extends AttachCuratorMediaPlugin
{
    /**
     * Use the project integration so Curator's global selection event is
     * handled only for the RichEditor that opened the media panel.
     *
     * @return array<string>
     */
    public function getTipTapJsExtensions(): array
    {
        return [Vite::asset('resources/js/filament/curator-rich-editor-integration.js')];
    }
}
