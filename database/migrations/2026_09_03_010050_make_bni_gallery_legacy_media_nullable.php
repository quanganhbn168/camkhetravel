<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bni_gallery_items', function (Blueprint $table): void {
            $table->unsignedBigInteger('media_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        // Không ép NOT NULL trở lại vì bản ghi tạo bằng Spatie không dùng cột Curator cũ.
    }
};
