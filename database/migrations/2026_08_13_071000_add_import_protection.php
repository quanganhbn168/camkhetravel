<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('content_items', function (Blueprint $table): void {
            $table->json('source_snapshot')->nullable()->after('legacy_meta');
            $table->json('import_locked_fields')->nullable()->after('source_snapshot');
        });

        Schema::table('media_assets', function (Blueprint $table): void {
            $table->json('source_snapshot')->nullable()->after('metadata');
            $table->json('import_locked_fields')->nullable()->after('source_snapshot');
        });
    }

    public function down(): void
    {
        Schema::table('content_items', function (Blueprint $table): void {
            $table->dropColumn(['source_snapshot', 'import_locked_fields']);
        });

        Schema::table('media_assets', function (Blueprint $table): void {
            $table->dropColumn(['source_snapshot', 'import_locked_fields']);
        });
    }
};
