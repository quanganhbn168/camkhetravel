<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_categories', function (Blueprint $table): void {
            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('is_home')->default(false)->index();
        });

        // Carry the existing homepage selection into the new category controls once.
        DB::table('service_categories')->where('is_active', true)
            ->whereIn('id', DB::table('services')->select('service_category_id')
                ->where('status', 'published')->where('is_home', true)
                ->where(fn ($query) => $query->whereNull('published_at')->orWhere('published_at', '<=', now())))
            ->update(['is_featured' => true, 'is_home' => true]);
    }

    public function down(): void
    {
        Schema::table('service_categories', function (Blueprint $table): void {
            $table->dropColumn(['is_featured', 'is_home']);
        });
    }
};
