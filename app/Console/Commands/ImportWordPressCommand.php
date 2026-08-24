<?php

namespace App\Console\Commands;

use App\Models\ImportRun;
use App\Services\WordPress\WordPressImporter;
use App\Services\WordPress\WordPressMediaLocalizer;
use App\Services\WordPress\WordPressMediaUrlMapper;
use App\Support\Seo\SeoMaterializer;
use App\Support\WordPressMigrationLock;
use Illuminate\Console\Command;
use Throwable;

class ImportWordPressCommand extends Command
{
    protected $signature = 'wordpress:import
        {--skip-media : Bỏ qua việc nhập bản ghi thư viện tệp đính kèm}
        {--skip-localize : Không copy media và rewrite URL sau import}
        {--skip-seo : Không materialize SEO hiệu lực sau import}';

    protected $description = 'Nhập dữ liệu WordPress và Rank Math qua kết nối chỉ đọc';

    public function handle(
        WordPressImporter $importer,
        WordPressMediaLocalizer $localizer,
        WordPressMediaUrlMapper $mediaUrlMapper,
        SeoMaterializer $seoMaterializer,
        WordPressMigrationLock $lock,
    ): int {
        return $lock->run(fn (): int => $this->runImport(
            $importer,
            $localizer,
            $mediaUrlMapper,
            $seoMaterializer,
        ));
    }

    private function runImport(
        WordPressImporter $importer,
        WordPressMediaLocalizer $localizer,
        WordPressMediaUrlMapper $mediaUrlMapper,
        SeoMaterializer $seoMaterializer,
    ): int {
        $this->info('Đang nhập WordPress bằng kết nối chỉ đọc...');

        $run = $importer->import((bool) $this->option('skip-media'));

        $this->newLine();
        $this->info('Import hoàn tất.');
        $this->table(
            ['Hạng mục', 'Số lượng'],
            collect($run->counts)->map(fn ($count, $name) => [$name, $count])->values()->all(),
        );

        foreach ($run->warnings ?? [] as $warning) {
            $this->warn($warning);
        }

        try {
            return $this->runPostImport($run, $localizer, $mediaUrlMapper, $seoMaterializer);
        } catch (Throwable $exception) {
            $this->markRunFailed($run, $exception->getMessage());

            throw $exception;
        }
    }

    private function runPostImport(
        ImportRun $run,
        WordPressMediaLocalizer $localizer,
        WordPressMediaUrlMapper $mediaUrlMapper,
        SeoMaterializer $seoMaterializer,
    ): int {
        if (! $this->option('skip-localize')) {
            if (! is_dir($mediaUrlMapper->sourceUploadsPath())) {
                $this->error('Không tìm thấy uploads WordPress để hoàn tất media: '.$mediaUrlMapper->sourceUploadsPath());
                $this->line('Chỉ dùng --skip-localize khi media local hiện tại đã sẵn sàng và anh chủ động chấp nhận URL nguồn trong dữ liệu vừa nhập.');

                return $this->failRun($run, 'Không tìm thấy uploads WordPress để hoàn tất media.');
            }

            $this->newLine();
            $this->info('Đang đồng bộ media local và rewrite URL...');
            $mediaStats = $localizer->localize();
            $this->table(
                ['Hạng mục media', 'Số lượng'],
                collect($mediaStats)->map(fn ($value, $key): array => [$key, $value])->values()->all(),
            );

            if ((
                $mediaStats['files_missing']
                + $mediaStats['media_failed']
                + $mediaStats['external_media_failed']
                + $mediaStats['unresolved_source_references']
            ) > 0) {
                return $this->failRun($run, 'Hậu xử lý media phát hiện file thiếu hoặc lỗi.');
            }
        }

        if (! $this->option('skip-seo')) {
            $this->newLine();
            $this->info('Đang cập nhật SEO hiệu lực...');
            $seoStats = $seoMaterializer->materialize();
            $this->table(
                ['Hạng mục SEO', 'Số lượng'],
                collect($seoStats)->map(fn ($value, $key): array => [$key, $value])->values()->all(),
            );
        }

        return self::SUCCESS;
    }

    private function failRun(ImportRun $run, string $message): int
    {
        $this->markRunFailed($run, $message);

        return self::FAILURE;
    }

    private function markRunFailed(ImportRun $run, string $message): void
    {
        $run->forceFill([
            'status' => 'failed',
            'finished_at' => now(),
            'error_message' => $message,
        ])->save();
    }
}
