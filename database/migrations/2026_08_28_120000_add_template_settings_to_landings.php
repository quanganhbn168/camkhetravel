<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('landings', function (Blueprint $table): void {
            $table->json('template_settings')->nullable()->after('template_key');
        });
    }

    public function down(): void
    {
        Schema::table('landings', function (Blueprint $table): void {
            $table->dropColumn('template_settings');
        });
    }
};
