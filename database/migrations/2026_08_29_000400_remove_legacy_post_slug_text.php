<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach ([
            'landings' => ['body'],
            'projects' => ['body'],
            'posts' => ['body'],
        ] as $table => $columns) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            foreach ($columns as $column) {
                if (! Schema::hasColumn($table, $column)) {
                    continue;
                }

                DB::table($table)
                    ->where($column, 'like', '%wordpress-%')
                    ->orderBy('id')
                    ->chunkById(100, function ($records) use ($table, $column): void {
                        foreach ($records as $record) {
                            $body = str_ireplace(
                                ['/tin-tuc/wordpress-6283', 'wordpress-6283'],
                                'THT Media',
                                (string) $record->{$column},
                            );

                            if ($body !== (string) $record->{$column}) {
                                DB::table($table)
                                    ->where('id', $record->id)
                                    ->update([
                                        $column => $body,
                                        'updated_at' => now(),
                                    ]);
                            }
                        }
                    });
            }
        }
    }

    public function down(): void
    {
        throw new RuntimeException('This content cleanup is irreversible; restore the database backup to roll it back.');
    }
};
