<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('bni_events') && ! Schema::hasColumn('bni_events', 'directions_url')) {
            Schema::table('bni_events', function (Blueprint $table): void {
                $table->string('directions_url', 2048)->nullable()->after('address');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('bni_events') && Schema::hasColumn('bni_events', 'directions_url')) {
            Schema::table('bni_events', function (Blueprint $table): void {
                $table->dropColumn('directions_url');
            });
        }
    }
};
