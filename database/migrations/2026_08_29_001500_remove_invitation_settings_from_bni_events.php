<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('bni_events')) {
            return;
        }

        DB::table('bni_events')
            ->select(['id', 'settings'])
            ->orderBy('id')
            ->get()
            ->each(function (object $event): void {
                $settings = is_array($event->settings)
                    ? $event->settings
                    : json_decode((string) $event->settings, true);

                if (! is_array($settings) || ! array_key_exists('invitation', $settings)) {
                    return;
                }

                unset($settings['invitation']);

                DB::table('bni_events')
                    ->where('id', $event->id)
                    ->update([
                        'settings' => $settings === []
                            ? null
                            : json_encode($settings, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    ]);
            });
    }

    public function down(): void
    {
        // Event-level invitation copy is intentionally not restored.
    }
};
