<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bni_chapters', function (Blueprint $table): void {
            $table->json('settings')->nullable()->after('contact_phone');
        });

        Schema::table('bni_invitations', function (Blueprint $table): void {
            $table->string('guest_name')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('bni_invitations', function (Blueprint $table): void {
            $table->string('guest_name')->nullable(false)->change();
        });

        Schema::table('bni_chapters', function (Blueprint $table): void {
            $table->dropColumn('settings');
        });
    }
};
