<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('testimonials', function (Blueprint $table): void {
            $table->unsignedTinyInteger('rating')->nullable()->default(null)->change();
        });

        DB::table('testimonials')
            ->join('content_items', 'content_items.id', '=', 'testimonials.legacy_content_item_id')
            ->where('content_items.source', 'wordpress')
            ->where('content_items.type', 'us_testimonial')
            ->where('testimonials.rating', 5)
            ->update(['testimonials.rating' => null]);
    }

    public function down(): void
    {
        DB::table('testimonials')->whereNull('rating')->update(['rating' => 5]);

        Schema::table('testimonials', function (Blueprint $table): void {
            $table->unsignedTinyInteger('rating')->default(5)->nullable(false)->change();
        });
    }
};
