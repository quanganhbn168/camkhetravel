<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bni_contacts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('bni_event_id')->nullable()->constrained('bni_events')->cascadeOnDelete();
            $table->foreignId('bni_chapter_id')->nullable()->constrained('bni_chapters')->cascadeOnDelete();
            $table->string('name')->nullable();
            $table->string('position')->nullable();
            $table->string('phone', 32)->nullable();
            $table->string('email')->nullable();
            $table->string('zalo_url', 2048)->nullable();
            $table->text('note')->nullable();
            $table->boolean('is_primary')->default(false)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['bni_event_id', 'sort_order']);
            $table->index(['bni_chapter_id', 'sort_order']);
        });

        $now = now();

        DB::table('bni_events')
            ->where(fn ($query) => $query
                ->whereNotNull('contact_name')
                ->orWhereNotNull('contact_phone')
                ->orWhereNotNull('contact_email'))
            ->orderBy('id')
            ->each(function (object $event) use ($now): void {
                DB::table('bni_contacts')->insert([
                    'bni_event_id' => $event->id,
                    'name' => $event->contact_name,
                    'phone' => $event->contact_phone,
                    'email' => $event->contact_email,
                    'is_primary' => true,
                    'is_active' => true,
                    'sort_order' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            });

        DB::table('bni_chapters')
            ->where(fn ($query) => $query
                ->whereNotNull('contact_name')
                ->orWhereNotNull('contact_phone')
                ->orWhereNotNull('contact_email'))
            ->orderBy('id')
            ->each(function (object $chapter) use ($now): void {
                DB::table('bni_contacts')->insert([
                    'bni_chapter_id' => $chapter->id,
                    'name' => $chapter->contact_name,
                    'phone' => $chapter->contact_phone,
                    'email' => $chapter->contact_email,
                    'is_primary' => true,
                    'is_active' => true,
                    'sort_order' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('bni_contacts');
    }
};
