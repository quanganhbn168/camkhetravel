<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bni_gallery_items', function (Blueprint $table): void {
            $table->foreignId('uploaded_by_user_id')
                ->nullable()
                ->after('bni_chapter_id')
                ->constrained('users')
                ->nullOnDelete();
            $table->foreignId('approved_by_user_id')
                ->nullable()
                ->after('uploaded_by_user_id')
                ->constrained('users')
                ->nullOnDelete();
            $table->string('uploader_name')->nullable()->after('caption');
            $table->string('uploader_email')->nullable()->after('uploader_name');
            $table->string('uploader_phone', 32)->nullable()->after('uploader_email');
            $table->string('source', 32)->default('admin')->after('uploader_phone')->index();
            $table->string('status', 32)->default('approved')->after('source')->index();
            $table->timestamp('approved_at')->nullable()->after('status');
        });

        DB::table('bni_gallery_items')->update([
            'source' => 'admin',
            'status' => 'approved',
            'approved_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::table('bni_gallery_items', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('uploaded_by_user_id');
            $table->dropConstrainedForeignId('approved_by_user_id');
            $table->dropColumn([
                'uploader_name',
                'uploader_email',
                'uploader_phone',
                'source',
                'status',
                'approved_at',
            ]);
        });
    }
};
