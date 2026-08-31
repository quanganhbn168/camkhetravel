<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bni_invitations', function (Blueprint $table): void {
            $table->string('access_token', 64)->nullable()->unique()->after('slug');
        });

        DB::table('bni_invitations')
            ->select('id')
            ->whereNull('access_token')
            ->orderBy('id')
            ->eachById(function (object $invitation): void {
                do {
                    $token = Str::random(48);
                } while (DB::table('bni_invitations')->where('access_token', $token)->exists());

                DB::table('bni_invitations')
                    ->where('id', $invitation->id)
                    ->update(['access_token' => $token]);
            });
    }

    public function down(): void
    {
        Schema::table('bni_invitations', function (Blueprint $table): void {
            $table->dropUnique('bni_invitations_access_token_unique');
            $table->dropColumn('access_token');
        });
    }
};
