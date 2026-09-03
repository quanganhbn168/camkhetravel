<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bni_schedule_days', function (Blueprint $table): void {
            $table->unique(['bni_event_id', 'event_date']);
        });
    }

    public function down(): void
    {
        Schema::table('bni_schedule_days', function (Blueprint $table): void {
            $table->dropUnique(['bni_event_id', 'event_date']);
        });
    }
};
