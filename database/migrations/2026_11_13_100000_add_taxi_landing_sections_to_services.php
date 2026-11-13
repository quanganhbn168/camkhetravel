<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table): void {
            $table->string('usecase_title')->nullable()->after('benefit_items');
            $table->text('usecase_description')->nullable()->after('usecase_title');
            $table->json('usecase_items')->nullable()->after('usecase_description');
            $table->string('vehicle_title')->nullable()->after('usecase_items');
            $table->text('vehicle_description')->nullable()->after('vehicle_title');
            $table->json('vehicle_items')->nullable()->after('vehicle_description');
            $table->string('area_title')->nullable()->after('vehicle_items');
            $table->text('area_description')->nullable()->after('area_title');
            $table->json('area_items')->nullable()->after('area_description');
            $table->string('pricing_title')->nullable()->after('area_items');
            $table->text('pricing_description')->nullable()->after('pricing_title');
            $table->json('pricing_items')->nullable()->after('pricing_description');
            $table->text('pricing_note')->nullable()->after('pricing_items');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table): void {
            $table->dropColumn([
                'usecase_title', 'usecase_description', 'usecase_items',
                'vehicle_title', 'vehicle_description', 'vehicle_items',
                'area_title', 'area_description', 'area_items',
                'pricing_title', 'pricing_description', 'pricing_items', 'pricing_note',
            ]);
        });
    }
};
