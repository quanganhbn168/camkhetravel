<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bni_gallery_items', function (Blueprint $table): void {
            $table->foreignId('bni_activity_id')
                ->nullable()
                ->after('bni_event_id')
                ->constrained('bni_activities')
                ->nullOnDelete();
        });

        $events = DB::table('bni_events')->get(['id', 'type', 'title'])->keyBy('id');
        $activities = DB::table('bni_activities')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(['id', 'bni_event_id', 'type'])
            ->groupBy('bni_event_id');

        DB::table('bni_gallery_items')
            ->whereNotNull('bni_event_id')
            ->whereNull('bni_activity_id')
            ->orderBy('id')
            ->eachById(function ($items) use ($events, $activities): void {
                foreach ($items as $item) {
                    $event = $events->get($item->bni_event_id);

                    if (! $event) {
                        continue;
                    }

                    $eventActivities = $activities->get($item->bni_event_id, collect());
                    $activity = $eventActivities->firstWhere('type', $item->group)
                        ?: $eventActivities->firstWhere('type', $event->type)
                        ?: $eventActivities->first();

                    if ($activity) {
                        DB::table('bni_gallery_items')
                            ->where('id', $item->id)
                            ->update(['bni_activity_id' => $activity->id]);
                    }
                }
            });
    }

    public function down(): void
    {
        Schema::table('bni_gallery_items', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('bni_activity_id');
        });
    }
};
