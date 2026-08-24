<?php

namespace App\Console\Commands;

use App\Services\WordPress\WordPressMediaLocalizer;
use App\Support\WordPressMigrationLock;
use Illuminate\Console\Command;

class LocalizeWordPressMediaCommand extends Command
{
    protected $signature = 'wordpress:localize-media
        {--dry-run : Chỉ thống kê, không copy hoặc cập nhật DB}
        {--verify : So khớp SHA-256 tất cả file nguồn và đích}';

    protected $description = 'Đưa ảnh WordPress về storage Laravel và thay URL uploads trong dữ liệu';

    public function handle(
        WordPressMediaLocalizer $localizer,
        WordPressMigrationLock $lock,
    ): int {
        $stats = $lock->run(fn (): array => $localizer->localize(
            (bool) $this->option('dry-run'),
            (bool) $this->option('verify'),
        ));

        $this->table(
            ['Hạng mục', 'Số lượng'],
            collect($stats)->map(fn ($value, $key): array => [$key, $value])->values()->all(),
        );

        return (
            $stats['files_missing']
            + $stats['media_failed']
            + $stats['external_media_failed']
            + $stats['unresolved_source_references']
        ) > 0
            ? self::FAILURE
            : self::SUCCESS;
    }
}
