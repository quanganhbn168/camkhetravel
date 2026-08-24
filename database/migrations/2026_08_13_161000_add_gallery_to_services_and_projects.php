<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table): void {
            $table->json('gallery')->nullable()->after('curator_media_id');
        });

        Schema::table('projects', function (Blueprint $table): void {
            $table->json('gallery')->nullable()->after('curator_media_id');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table): void {
            $table->dropColumn('gallery');
        });

        Schema::table('projects', function (Blueprint $table): void {
            $table->dropColumn('gallery');
        });
    }
};
