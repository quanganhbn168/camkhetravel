<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bni_event_videos', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('bni_event_id')->unique()->constrained('bni_events')->cascadeOnDelete();
            $table->string('external_url', 2048)->nullable();
            $table->string('registration_label')->nullable();
            $table->string('registration_url', 2048)->nullable();
            $table->timestamps();
        });

        Schema::create('bni_event_landings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('bni_event_id')->unique()->constrained('bni_events')->cascadeOnDelete();
            $table->string('countdown_label')->nullable();
            $table->string('prizes_title')->nullable();
            $table->text('prizes_description')->nullable();
            $table->string('rules_title')->nullable();
            $table->longText('rules')->nullable();
            $table->string('registration_title')->nullable();
            $table->text('registration_description')->nullable();
            $table->timestamps();
        });

        Schema::create('bni_event_prizes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('bni_event_landing_id')->constrained('bni_event_landings')->cascadeOnDelete();
            $table->string('title');
            $table->string('value')->nullable();
            $table->text('description')->nullable();
            $table->boolean('highlight')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('bni_schedule_days', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('bni_event_id')->constrained('bni_events')->cascadeOnDelete();
            $table->date('event_date')->nullable()->index();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::table('bni_schedule_items', function (Blueprint $table): void {
            $table->foreignId('bni_schedule_day_id')
                ->nullable()
                ->after('id')
                ->constrained('bni_schedule_days')
                ->cascadeOnDelete();
        });

        $this->migrateEventVideosAndLandingContent();
        $this->migrateScheduleDays();

        Schema::table('bni_schedule_items', function (Blueprint $table): void {
            $table->dropForeign(['bni_event_id']);
            $table->dropColumn(['bni_event_id', 'day_number', 'stage', 'result', 'location']);
        });
    }

    public function down(): void
    {
        Schema::table('bni_schedule_items', function (Blueprint $table): void {
            $table->foreignId('bni_event_id')->nullable()->after('id');
            $table->unsignedTinyInteger('day_number')->default(1)->index();
            $table->string('stage')->nullable();
            $table->text('result')->nullable();
            $table->string('location')->nullable();
        });

        DB::table('bni_schedule_items')
            ->whereNotNull('bni_schedule_day_id')
            ->orderBy('id')
            ->eachById(function (object $item): void {
                $day = DB::table('bni_schedule_days')->find($item->bni_schedule_day_id);

                if ($day) {
                    DB::table('bni_schedule_items')->where('id', $item->id)->update([
                        'bni_event_id' => $day->bni_event_id,
                        'day_number' => max(1, (int) $day->sort_order),
                    ]);
                }
            });

        Schema::table('bni_schedule_items', function (Blueprint $table): void {
            $table->foreign('bni_event_id')->references('id')->on('bni_events')->cascadeOnDelete();
            $table->dropForeign(['bni_schedule_day_id']);
            $table->dropColumn('bni_schedule_day_id');
        });

        if (Schema::hasTable('media')) {
            DB::table('bni_event_videos')->orderBy('id')->each(function (object $video): void {
                DB::table('media')
                    ->whereIn('model_type', ['bni-event-video', 'App\\Models\\BniEventVideo'])
                    ->where('model_id', $video->id)
                    ->update([
                        'model_type' => 'bni-event',
                        'model_id' => $video->bni_event_id,
                    ]);

                DB::table('media')
                    ->whereIn('model_type', ['bni-event'])
                    ->where('model_id', $video->bni_event_id)
                    ->where('collection_name', 'poster')
                    ->update(['collection_name' => 'video_poster']);
            });
        }

        Schema::dropIfExists('bni_schedule_days');
        Schema::dropIfExists('bni_event_prizes');
        Schema::dropIfExists('bni_event_landings');
        Schema::dropIfExists('bni_event_videos');
    }

    private function migrateEventVideosAndLandingContent(): void
    {
        DB::table('bni_events')->orderBy('id')->each(function (object $event): void {
            $now = now();
            $videoId = DB::table('bni_event_videos')->insertGetId([
                'bni_event_id' => $event->id,
                'external_url' => $event->video_url,
                'registration_label' => $event->registration_label,
                'registration_url' => $event->registration_url,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            if (Schema::hasTable('media')) {
                DB::table('media')
                    ->whereIn('model_type', ['bni-event', 'App\\Models\\BniEvent'])
                    ->where('model_id', $event->id)
                    ->whereIn('collection_name', ['video_poster', 'video'])
                    ->update([
                        'model_type' => 'bni-event-video',
                        'model_id' => $videoId,
                    ]);

                DB::table('media')
                    ->where('model_type', 'bni-event-video')
                    ->where('model_id', $videoId)
                    ->where('collection_name', 'video_poster')
                    ->update(['collection_name' => 'poster']);
            }

            $settings = json_decode((string) $event->settings, true);
            $settings = is_array($settings) ? $settings : [];

            if ($event->type !== 'pickleball' && $settings === []) {
                return;
            }

            $landingId = DB::table('bni_event_landings')->insertGetId([
                'bni_event_id' => $event->id,
                'countdown_label' => $settings['countdown_label'] ?? null,
                'prizes_title' => $settings['prizes_title'] ?? null,
                'prizes_description' => $settings['prizes_description'] ?? null,
                'rules_title' => $settings['rules_title'] ?? null,
                'rules' => $settings['rules'] ?? null,
                'registration_title' => $settings['registration_title'] ?? null,
                'registration_description' => $settings['registration_description'] ?? null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            foreach ((array) ($settings['prizes'] ?? []) as $index => $prize) {
                if (! is_array($prize) || blank($prize['title'] ?? null)) {
                    continue;
                }

                DB::table('bni_event_prizes')->insert([
                    'bni_event_landing_id' => $landingId,
                    'title' => $prize['title'],
                    'value' => $prize['value'] ?? null,
                    'description' => $prize['description'] ?? null,
                    'highlight' => (bool) ($prize['highlight'] ?? false),
                    'sort_order' => $index + 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        });
    }

    private function migrateScheduleDays(): void
    {
        $eventStarts = DB::table('bni_events')->pluck('starts_at', 'id');
        $groups = DB::table('bni_schedule_items')
            ->select(['bni_event_id', 'day_number'])
            ->distinct()
            ->orderBy('bni_event_id')
            ->orderBy('day_number')
            ->get();

        foreach ($groups as $group) {
            $dayNumber = max(1, (int) $group->day_number);
            $startsAt = $eventStarts->get($group->bni_event_id);
            $eventDate = $startsAt
                ? Carbon::parse($startsAt)->startOfDay()->addDays($dayNumber - 1)->toDateString()
                : null;
            $dayId = DB::table('bni_schedule_days')->insertGetId([
                'bni_event_id' => $group->bni_event_id,
                'event_date' => $eventDate,
                'title' => 'Ngày '.$dayNumber,
                'is_active' => true,
                'sort_order' => $dayNumber,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('bni_schedule_items')
                ->where('bni_event_id', $group->bni_event_id)
                ->where('day_number', $group->day_number)
                ->update(['bni_schedule_day_id' => $dayId]);
        }
    }
};
