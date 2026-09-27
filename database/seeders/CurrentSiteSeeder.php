<?php

namespace Database\Seeders;

use App\Actions\RestoreCurrentSite;
use Illuminate\Database\Seeder;

/** Deliberate CMS replacement with the checked-in local snapshot, not a routine reseed. */
final class CurrentSiteSeeder extends Seeder
{
    public function run(): void
    {
        app(RestoreCurrentSite::class)->handle();
        $this->command?->info('Đã áp dụng bản chốt nội dung local, ảnh, menu và số gọi/Zalo 0354865688. Tài khoản và yêu cầu liên hệ được giữ nguyên.');
    }
}
