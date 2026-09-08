<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_pricings', function (Blueprint $table): void {
            $table->json('comparison_rows')->nullable();
            $table->dropColumn('is_cumulative');
        });

        // Preserve the original, independent comparison as editable data.
        foreach (DB::table('service_pricings')->get() as $pricing) {
            $packages = DB::table('pricing_packages')->where('service_pricing_id', $pricing->id)->orderBy('sort_order')->get();
            $items = DB::table('pricing_package_items')->whereIn('pricing_package_id', $packages->pluck('id'))->where('is_active', true)->orderBy('sort_order')->get();
            $rows = $items->pluck('name')->map(fn ($name) => trim($name))->unique()->values()->map(fn ($name) => [
                'name' => $name,
                'cells' => $packages->map(function ($package) use ($items, $name) {
                    $item = $items->first(fn ($item) => $item->pricing_package_id === $package->id && trim($item->name) === $name);

                    return ['package_id' => $package->id, 'included' => $item !== null, 'value' => $item?->description];
                })->all(),
            ])->all();
            DB::table('service_pricings')->where('id', $pricing->id)->update(['comparison_rows' => json_encode($rows, JSON_UNESCAPED_UNICODE)]);
        }
    }

    public function down(): void
    {
        Schema::table('service_pricings', function (Blueprint $table): void {
            $table->dropColumn('comparison_rows');
            $table->boolean('is_cumulative')->default(false);
        });
    }
};
