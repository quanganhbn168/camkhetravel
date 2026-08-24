<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('languages', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 10)->unique();
            $table->string('name', 100);
            $table->string('native_name', 100);
            $table->string('og_locale', 20);
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('is_default')->default(false)->index();
            $table->boolean('is_indexable')->default(false)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->timestamps();
        });

        $now = now();

        DB::table('languages')->insert([
            ['code' => 'vi', 'name' => 'Tiếng Việt', 'native_name' => 'VI', 'og_locale' => 'vi_VN', 'is_active' => true, 'is_default' => true, 'is_indexable' => true, 'sort_order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'en', 'name' => 'English', 'native_name' => 'EN', 'og_locale' => 'en_US', 'is_active' => true, 'is_default' => false, 'is_indexable' => false, 'sort_order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'zh', 'name' => '中文', 'native_name' => '中文', 'og_locale' => 'zh_CN', 'is_active' => true, 'is_default' => false, 'is_indexable' => false, 'sort_order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'ko', 'name' => '한국어', 'native_name' => '한국어', 'og_locale' => 'ko_KR', 'is_active' => true, 'is_default' => false, 'is_indexable' => false, 'sort_order' => 4, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('languages');
    }
};
