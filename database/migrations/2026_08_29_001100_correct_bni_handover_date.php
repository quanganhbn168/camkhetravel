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
            ->where('slug', 'le-chuyen-giao-bni')
            ->where('type', 'handover')
            ->update([
                'starts_at' => '2026-10-01 08:00:00',
                'ends_at' => '2026-10-01 21:00:00',
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        // The previous date was generated dynamically and cannot be restored safely.
    }
};
