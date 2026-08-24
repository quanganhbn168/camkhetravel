<?php

namespace App\Console\Commands;

use App\Support\Seo\SeoMaterializer;
use Illuminate\Console\Command;

class MaterializeSeoCommand extends Command
{
    protected $signature = 'seo:materialize {--dry-run : Chỉ thống kê, không cập nhật DB}';

    protected $description = 'Lưu SEO hiệu lực dạng không phụ thuộc domain vào database';

    public function handle(SeoMaterializer $materializer): int
    {
        $stats = $materializer->materialize((bool) $this->option('dry-run'));

        $this->table(
            ['Hạng mục', 'Số lượng'],
            collect($stats)->map(fn ($value, $key): array => [$key, $value])->values()->all(),
        );

        return self::SUCCESS;
    }
}
