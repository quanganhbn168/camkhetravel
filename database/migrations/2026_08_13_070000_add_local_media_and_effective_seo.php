<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('content_items', function (Blueprint $table): void {
            $table->json('effective_seo')->nullable()->after('seo_source');
            $table->string('effective_seo_hash', 64)->nullable()->after('effective_seo');
            $table->timestamp('seo_materialized_at')->nullable()->after('effective_seo_hash');
        });

        Schema::table('media_assets', function (Blueprint $table): void {
            $table->text('effective_alt_text')->nullable()->after('alt_text');
            $table->string('localization_status', 32)->default('pending')->index()->after('file_path');
            $table->string('checksum_sha256', 64)->nullable()->after('localization_status');
            $table->timestamp('localized_at')->nullable()->after('checksum_sha256');
            $table->text('localization_error')->nullable()->after('localized_at');
        });
    }

    public function down(): void
    {
        Schema::table('content_items', function (Blueprint $table): void {
            $table->dropColumn([
                'effective_seo',
                'effective_seo_hash',
                'seo_materialized_at',
            ]);
        });

        Schema::table('media_assets', function (Blueprint $table): void {
            $table->dropIndex(['localization_status']);
            $table->dropColumn([
                'effective_alt_text',
                'localization_status',
                'checksum_sha256',
                'localized_at',
                'localization_error',
            ]);
        });
    }
};
