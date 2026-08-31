<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
            $table->timestamps();
        });

        Schema::create('pricing_packages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('service_pricing_id')->constrained('service_pricings')->cascadeOnDelete();
            $table->string('name');
            $table->string('badge')->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('list_price')->nullable();
            $table->string('price_label')->nullable();
            $table->string('price_unit')->nullable();
            $table->string('promotion_type', 32)->nullable();
            $table->decimal('promotion_value', 12, 2)->nullable();
            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->timestamps();
        });

        Schema::create('pricing_package_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('pricing_package_id')->constrained('pricing_packages')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->timestamps();
        });

        DB::table('services')->orderBy('id')->get()->each(function (object $service): void {
            $legacyPlans = Schema::hasColumn('pricing_plans', 'service_id')
                ? DB::table('pricing_plans')->where('service_id', $service->id)->orderBy('sort_order')->get()
                : collect();

            if ($legacyPlans->isEmpty() && blank($service->pricing_media_id ?? null)) {
                return;
            }

            $pricingId = DB::table('service_pricings')->insertGetId([
                'service_id' => $service->id,
                'title' => 'Bảng giá dịch vụ',
                'source_media_id' => $service->pricing_media_id ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($legacyPlans as $index => $plan) {
                $packageId = DB::table('pricing_packages')->insertGetId([
                    'service_pricing_id' => $pricingId,
                    'name' => $plan->name,
                    'badge' => $plan->badge,
                    'description' => $plan->description,
                    'list_price' => $plan->price,
                    'price_label' => $plan->price_label,
                    'price_unit' => $plan->price_unit,
                    'is_featured' => $plan->is_featured,
                    'is_active' => $plan->is_active,
                    'sort_order' => $plan->sort_order ?: $index + 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                foreach ((array) json_decode((string) ($plan->features ?? '[]'), true) as $itemIndex => $feature) {
                    $name = is_array($feature)
                        ? trim((string) ($feature['name'] ?? $feature['title'] ?? $feature['label'] ?? ''))
                        : trim((string) $feature);

                    if ($name === '') {
                        continue;
                    }

                    DB::table('pricing_package_items')->insert([
                        'pricing_package_id' => $packageId,
                        'name' => $name,
                        'description' => is_array($feature) ? ($feature['description'] ?? null) : null,
                        'sort_order' => $itemIndex + 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        });

        if (Schema::hasColumn('pricing_plans', 'service_id')) {
            Schema::table('pricing_plans', function (Blueprint $table): void {
                $table->dropConstrainedForeignId('service_id');
            });
        }

        if (Schema::hasColumn('services', 'pricing_media_id')) {
            Schema::table('services', function (Blueprint $table): void {
                $table->dropConstrainedForeignId('pricing_media_id');
            });
        }
    }

    public function down(): void
    {
        throw new LogicException('The service pricing catalog migration is irreversible. Restore the database backup instead.');
    }
};
