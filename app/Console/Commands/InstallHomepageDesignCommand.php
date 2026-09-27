<?php

namespace App\Console\Commands;

use App\Actions\InstallHomepageDesign;
use Illuminate\Console\Command;

final class InstallHomepageDesignCommand extends Command
{
    protected $signature = 'homepage:install-design';

    protected $description = 'Nhập bộ ảnh và nội dung trang chủ v1 một lần, giữ các chỉnh sửa CMS.';

    public function handle(InstallHomepageDesign $installer): int
    {
        $changed = $installer->handle();
        $this->info($changed ? 'Đã nhập thiết kế trang chủ v1 vào CMS.' : 'Thiết kế v1 đã được nhập; giữ nguyên các chỉnh sửa CMS.');
        return self::SUCCESS;
    }
}
