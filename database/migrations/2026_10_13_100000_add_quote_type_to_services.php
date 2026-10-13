<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table): void {
            // Loại yêu cầu mở sẵn trong form khi khách bấm "Nhận tư vấn" ở dịch vụ này.
            $table->string('quote_type', 16)->default('trip')->after('is_home');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table): void {
            $table->dropColumn('quote_type');
        });
    }
};
