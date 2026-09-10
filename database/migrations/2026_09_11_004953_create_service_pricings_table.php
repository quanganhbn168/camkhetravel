<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_pricings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('service_id')->unique()->constrained('services')->cascadeOnDelete();
            $table->string('title')->default('Bảng giá dịch vụ');
            $table->text('description')->nullable();
            $table->foreignId('source_media_id')->nullable()->constrained('curator')->nullOnDelete();
            $table->text('source_url')->nullable();
            $table->longText('source_json')->nullable();
            $table->json('comparison_rows')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_pricings');
    }
};
