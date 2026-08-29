<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->moveMediaDirectory();

        DB::table('curator')
            ->where('path', 'like', 'media/wordpress/%')
            ->update([
                'path' => DB::raw("REPLACE(path, 'media/wordpress/', 'media/library/')"),
                'directory' => DB::raw("REPLACE(directory, 'media/wordpress/', 'media/library/')"),
            ]);

        foreach ([
            'landings' => ['gallery', 'backstage_gallery', 'template_settings', 'sections', 'theme_settings', 'body'],
            'projects' => ['gallery', 'body'],
            'posts' => ['body'],
            'settings' => ['payload'],
        ] as $table => $columns) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            foreach ($columns as $column) {
                if (! Schema::hasColumn($table, $column)) {
                    continue;
                }

                DB::table($table)
                    ->where($column, 'like', '%media/wordpress/%')
                    ->update([
                        $column => DB::raw("REPLACE(`{$column}`, 'media/wordpress/', 'media/library/')"),
                    ]);
            }
        }
    }

    public function down(): void
    {
        throw new RuntimeException('This media namespace move is irreversible; restore the media backup and database backup to roll it back.');
    }

    private function moveMediaDirectory(): void
    {
        $publicRoot = (string) config('filesystems.disks.public.root', storage_path('app/public'));
        $old = $publicRoot.DIRECTORY_SEPARATOR.'media'.DIRECTORY_SEPARATOR.'wordpress';
        $new = $publicRoot.DIRECTORY_SEPARATOR.'media'.DIRECTORY_SEPARATOR.'library';

        if (! File::exists($old)) {
            return;
        }

        if (File::exists($new)) {
            throw new RuntimeException('Cannot move local media namespace because both media/wordpress and media/library exist.');
        }

        File::moveDirectory($old, $new);
    }
};
