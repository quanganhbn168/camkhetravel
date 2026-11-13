<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Trang dịch vụ theo bố cục landing mới: banner chia đôi (nhãn, câu phụ, các ý chính),
 * gói dịch vụ có giá "Từ ...", bảng giá chia theo điểm đến, ảnh cho khu vực đón khách.
 * Bảng giá phẳng cũ (pricing_items) được chuyển sang pricing_groups rồi bỏ cột.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table): void {
            $table->string('hero_badge')->nullable()->after('excerpt');
            $table->string('hero_subtitle')->nullable()->after('hero_badge');
            $table->json('hero_points')->nullable()->after('hero_subtitle');
            $table->string('package_title')->nullable()->after('benefit_items');
            $table->text('package_description')->nullable()->after('package_title');
            $table->json('package_items')->nullable()->after('package_description');
            $table->json('pricing_groups')->nullable()->after('pricing_description');
            $table->foreignId('area_media_id')->nullable()->after('area_items')->constrained('curator')->nullOnDelete();
        });

        DB::table('services')->whereNotNull('pricing_items')->orderBy('id')->each(function (object $service): void {
            $groups = collect(json_decode((string) $service->pricing_items, true) ?: [])
                ->filter(fn (mixed $row): bool => is_array($row) && filled($row['route'] ?? null))
                ->groupBy(fn (array $row): string => trim((string) $row['route']))
                ->map(fn ($rows, string $destination): array => [
                    'destination' => $destination,
                    'rows' => $rows->map(fn (array $row): array => [
                        'kind' => 'private',
                        'label' => trim((string) ($row['vehicle'] ?? '')),
                        'price' => trim((string) ($row['price'] ?? '')),
                    ])->values()->all(),
                ])
                ->values()
                ->all();

            DB::table('services')->where('id', $service->id)->update(['pricing_groups' => $groups === [] ? null : json_encode($groups, JSON_UNESCAPED_UNICODE)]);
        });

        Schema::table('services', function (Blueprint $table): void {
            $table->dropColumn('pricing_items');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table): void {
            $table->json('pricing_items')->nullable()->after('pricing_description');
        });

        Schema::table('services', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('area_media_id');
            $table->dropColumn(['hero_badge', 'hero_subtitle', 'hero_points', 'package_title', 'package_description', 'package_items', 'pricing_groups']);
        });
    }
};
