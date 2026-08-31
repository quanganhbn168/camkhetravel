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
        DB::table('bni_invitations')
            ->select('id')
            ->where(function ($query): void {
                $query->whereNull('invitation_code')->orWhere('invitation_code', '');
            })
            ->orderBy('id')
            ->eachById(function (object $invitation): void {
                do {
                    $code = 'tm-'.Str::lower(Str::random(10));
                } while (DB::table('bni_invitations')->where('invitation_code', $code)->exists());

                DB::table('bni_invitations')
                    ->where('id', $invitation->id)
                    ->update(['invitation_code' => $code]);
            });

        Schema::table('bni_invitations', function (Blueprint $table): void {
            $table->string('invitation_code', 32)->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('bni_invitations', function (Blueprint $table): void {
            $table->string('invitation_code', 32)->nullable()->change();
        });
    }
};
