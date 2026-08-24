<?php

namespace App\Support;

use Closure;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Support\Facades\Cache;
use RuntimeException;

class WordPressMigrationLock
{
    public function run(Closure $callback): mixed
    {
        try {
            return Cache::lock('thtmedia-wordpress-migration', 7_200)->block(0, $callback);
        } catch (LockTimeoutException) {
            throw new RuntimeException(
                'Một tiến trình import/localize WordPress khác đang chạy. Hãy chờ tiến trình đó hoàn tất.',
            );
        }
    }
}
