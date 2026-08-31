<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('landing_templates')
            ->where('view_name', 'like', 'frontend.services.%')
            ->update([
                'view_name' => DB::raw("REPLACE(view_name, 'frontend.services.', 'frontend.landing-pages.')"),
            ]);
    }

    public function down(): void
    {
        DB::table('landing_templates')
            ->where('view_name', 'like', 'frontend.landing-pages.%')
            ->update([
                'view_name' => DB::raw("REPLACE(view_name, 'frontend.landing-pages.', 'frontend.services.')"),
            ]);
    }
};
