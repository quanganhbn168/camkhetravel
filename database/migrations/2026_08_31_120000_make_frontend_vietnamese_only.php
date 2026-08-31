<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('languages')) {
            return;
        }

        DB::table('languages')->where('code', 'vi')->update([
            'is_active' => true,
            'is_default' => true,
            'is_indexable' => true,
            'updated_at' => now(),
        ]);

        DB::table('languages')->where('code', '!=', 'vi')->update([
            'is_active' => false,
            'is_default' => false,
            'is_indexable' => false,
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        if (! Schema::hasTable('languages')) {
            return;
        }

        DB::table('languages')->whereIn('code', ['en', 'zh', 'ko'])->update([
            'is_active' => true,
            'updated_at' => now(),
        ]);
    }
};
