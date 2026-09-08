<?php

use App\Models\Service;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_pricings', function (Blueprint $table): void {
            $table->boolean('is_cumulative')->default(false);
        });

        $serviceIds = Service::query()
            ->whereHas('slugs', fn ($query) => $query->where('slug', 'san-xuat-phim-doanh-nghiep'))
            ->pluck('id');
        DB::table('service_pricings')->whereIn('service_id', $serviceIds)->update(['is_cumulative' => true]);
    }

    public function down(): void
    {
        Schema::table('service_pricings', function (Blueprint $table): void {
            $table->dropColumn('is_cumulative');
        });
    }
};
