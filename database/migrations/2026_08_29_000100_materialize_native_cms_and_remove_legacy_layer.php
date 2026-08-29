<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->materializeNativeHtml();
        $this->removeLegacyMenus();

        foreach (['landings', 'projects', 'posts', 'partners', 'testimonials'] as $table) {
            $this->dropColumns($table, ['legacy_content_item_id', 'legacy_media_asset_id']);
        }

        foreach (['landing_categories', 'project_categories', 'post_categories'] as $table) {
            $this->dropColumns($table, ['legacy_term_id']);
        }

        $this->dropColumns('menu_items', ['source_id', 'parent_source_id', 'legacy_meta'], dropForeign: false);
        $this->dropColumns('menus', ['source', 'source_id'], dropForeign: false);

        Schema::dropIfExists('content_item_term');
        Schema::dropIfExists('pages');
        Schema::dropIfExists('content_items');
        Schema::dropIfExists('media_assets');
        Schema::dropIfExists('terms');
        Schema::dropIfExists('taxonomies');
        Schema::dropIfExists('site_settings');
        Schema::dropIfExists('import_runs');
    }

    public function down(): void
    {
        throw new LogicException('The native CMS cutover is irreversible. Restore the database backup instead.');
    }

    private function materializeNativeHtml(): void
    {
        if (! Schema::hasTable('content_items')) {
            return;
        }

        $pathMap = $this->nativePathMap();

        foreach (['landings', 'projects', 'posts'] as $table) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'body')) {
                continue;
            }

            DB::table($table)
                ->whereNotNull('body')
                ->select(['id', 'body'])
                ->orderBy('id')
                ->get()
                ->each(function (object $record) use ($table, $pathMap): void {
                    $body = $this->normalizeHtml((string) $record->body, $pathMap);

                    if ($body !== (string) $record->body) {
                        DB::table($table)->where('id', $record->id)->update(['body' => $body]);
                    }
                });
        }
    }

    /** @return array<string, string> */
    private function nativePathMap(): array
    {
        $map = ['/.' => '/', '/' => '/'];

        foreach ([
            'landings' => ['type' => 'landing', 'prefix' => ''],
            'projects' => ['type' => 'project', 'prefix' => 'du-an'],
            'posts' => ['type' => 'post', 'prefix' => 'tin-tuc'],
        ] as $table => $definition) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'legacy_content_item_id')) {
                continue;
            }

            DB::table($table)
                ->whereNotNull('legacy_content_item_id')
                ->get(['id', 'legacy_content_item_id'])
                ->each(function (object $record) use (&$map, $definition): void {
                    $source = DB::table('content_items')
                        ->where('id', $record->legacy_content_item_id)
                        ->first(['slug', 'canonical_path']);
                    $slug = DB::table('slugs')
                        ->where('sluggable_type', $definition['type'])
                        ->where('sluggable_id', $record->id)
                        ->where('locale', 'vi')
                        ->value('slug');

                    if (! $source || ! $slug) {
                        return;
                    }

                    $newPath = '/'.trim(($definition['prefix'] ? $definition['prefix'].'/' : '').$slug, '/');
                    $oldPath = '/'.trim((string) $source->canonical_path, '/');
                    $map[$oldPath] = $newPath;
                    $map[$oldPath.'/'] = $newPath;

                    if ($definition['type'] === 'landing') {
                        $map['/service/'.trim((string) $source->slug, '/').'/'] = $newPath;
                        $map['/service/'.trim((string) $source->slug, '/')] = $newPath;
                    }
                });
        }

        return $map;
    }

    /** @param array<string, string> $pathMap */
    private function normalizeHtml(string $html, array $pathMap): string
    {
        $html = preg_replace_callback(
            "~https?://(?:www\\.)?thtmedia\\.com\\.vn(?<path>/[^\"'\\s<>)?,#]*)?~iu",
            function (array $matches) use ($pathMap): string {
                $path = '/'.trim((string) ($matches['path'] ?? ''), '/');

                if (str_starts_with($path, '/wp-content/uploads/')) {
                    return '/storage/media/wordpress/'.ltrim(substr($path, strlen('/wp-content/uploads/')), '/');
                }

                return $pathMap[$path] ?? $pathMap[$path.'/'] ?? ($path === '/.' ? '/' : $path);
            },
            $html,
        ) ?? $html;

        return preg_replace([
            '/\\s+wp-image-\\d+/i',
            '/\\s+wp-block-[a-z0-9-]+/i',
            '/\\s+wp-embedded-content/i',
        ], '', $html) ?? $html;
    }

    /** @param list<string> $columns */
    private function dropColumns(string $table, array $columns, bool $dropForeign = true): void
    {
        if (! Schema::hasTable($table)) {
            return;
        }

        $existing = array_values(array_filter($columns, fn (string $column): bool => Schema::hasColumn($table, $column)));

        if ($existing === []) {
            return;
        }

        if ($dropForeign) {
            foreach ($existing as $column) {
                $constraints = DB::select(
                    'select distinct constraint_name from information_schema.key_column_usage where constraint_schema = database() and table_name = ? and column_name = ? and referenced_table_name is not null',
                    [$table, $column],
                );

                foreach ($constraints as $constraint) {
                    DB::statement(sprintf(
                        'alter table `%s` drop foreign key `%s`',
                        $table,
                        $constraint->CONSTRAINT_NAME ?? $constraint->constraint_name,
                    ));
                }
            }
        }

        Schema::table($table, fn (Blueprint $blueprint): mixed => $blueprint->dropColumn($existing));
    }

    private function removeLegacyMenus(): void
    {
        if (! Schema::hasTable('menus')) {
            return;
        }

        DB::table('menus')->where('source', 'wordpress')->delete();

        if (Schema::hasTable('menu_items') && Schema::hasColumn('menu_items', 'source_id')) {
            Schema::table('menu_items', function (Blueprint $table): void {
                $table->index('menu_id', 'menu_items_menu_id_index');
                $table->dropUnique('menu_items_menu_id_source_id_unique');
            });
        }

        if (Schema::hasColumn('menus', 'source_id')) {
            Schema::table('menus', function (Blueprint $table): void {
                $table->dropUnique('menus_source_source_id_unique');
            });
        }
    }
};
