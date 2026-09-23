<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contact_requests', function (Blueprint $table): void {
            $table->string('request_type', 32)->default('contact')->index();
            $table->json('details')->nullable();
            $table->timestamp('privacy_consent_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('contact_requests', function (Blueprint $table): void {
            $table->dropIndex(['request_type']);
            $table->dropColumn(['request_type', 'details', 'privacy_consent_at']);
        });
    }
};
