<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('projects')
            ->join('content_items', 'content_items.id', '=', 'projects.legacy_content_item_id')
            ->where('content_items.source', 'wordpress')
            ->where('content_items.type', 'us_portfolio')
            ->where(fn ($query) => $query->whereNull('content_items.excerpt')->orWhere('content_items.excerpt', ''))
            ->select(['projects.id', 'projects.excerpt as project_excerpt', 'content_items.body'])
            ->orderBy('projects.id')
            ->cursor()
            ->each(function (object $project): void {
                $plainBody = trim(strip_tags((string) $project->body));

                if (blank($plainBody) || trim((string) $project->project_excerpt) !== Str::limit($plainBody, 260)) {
                    return;
                }

                DB::table('projects')
                    ->where('id', $project->id)
                    ->update(['excerpt' => $plainBody]);
            });
    }

    public function down(): void
    {
        // The migration deliberately preserves the restored full descriptions.
    }
};
