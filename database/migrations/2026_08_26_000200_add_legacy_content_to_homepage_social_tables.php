<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('partners', function (Blueprint $table): void {
            $table->foreignId('legacy_content_item_id')
                ->nullable()
                ->unique()
                ->after('id')
                ->constrained('content_items')
                ->nullOnDelete();
        });

        Schema::table('testimonials', function (Blueprint $table): void {
            $table->foreignId('legacy_content_item_id')
                ->nullable()
                ->unique()
                ->after('id')
                ->constrained('content_items')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('testimonials', function (Blueprint $table): void {
            $table->dropForeign(['legacy_content_item_id']);
            $table->dropUnique(['legacy_content_item_id']);
            $table->dropColumn('legacy_content_item_id');
        });

        Schema::table('partners', function (Blueprint $table): void {
            $table->dropForeign(['legacy_content_item_id']);
            $table->dropUnique(['legacy_content_item_id']);
            $table->dropColumn('legacy_content_item_id');
        });
    }
};
