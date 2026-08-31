<?php

namespace Database\Seeders;

use App\Models\BniActivity;
use App\Models\BniArticle;
use App\Models\BniChapter;
use App\Models\BniEvent;
use App\Models\BniPurpose;
use App\Models\BniScheduleItem;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class BniFoundationSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['bni_admin', 'bni_chapter_manager', 'bni_member'] as $role) {
            Role::findOrCreate($role, 'web');
        }

        if (Role::query()->where('name', 'super_admin')->exists()) {
            User::role('super_admin')->each(fn (User $user) => $user->assignRole('bni_admin'));
        }

        $handover = BniEvent::query()->firstOrNew(['slug' => 'le-chuyen-giao-bni']);
        $handoverIsNew = ! $handover->exists;
        $handover->fill([
            'type' => 'handover',
            'title' => 'Lễ chuyển giao Ban Điều hành BNI',
            'kicker' => 'BNI VIETNAM',
            'summary' => 'Một dấu mốc kết nối bốn chapter, tôn vinh hành trình đã qua và cùng mở ra nhiệm kỳ mới.',
            'content' => '<p>Lễ chuyển giao là không gian để các chapter cùng nhìn lại hành trình, tri ân Ban Điều hành và khởi động một chu kỳ phát triển mới.</p>',
            'venue' => 'Địa điểm sự kiện',
            'address' => 'Thông tin địa điểm sẽ được Ban tổ chức cập nhật',
            'contact_name' => 'Ban tổ chức BNI',
            'registration_label' => 'Đăng ký ngay',
            'registration_url' => '#dang-ky',
            'status' => 'published',
            'is_featured' => true,
        ]);
        if ($handoverIsNew) {
            $handover->starts_at = Carbon::create(2026, 10, 1, 8, 0, 0, config('app.timezone'));
            $handover->ends_at = Carbon::create(2026, 10, 1, 21, 0, 0, config('app.timezone'));
        }
        $handover->save();

        $chapters = collect([
            ['name' => 'KINHBAC', 'slug' => 'kinhbac', 'description' => 'Kết nối doanh nhân Bắc Ninh bằng tinh thần cho đi để nhận lại.'],
            ['name' => 'KBG', 'slug' => 'kbg', 'description' => 'Cộng đồng kinh doanh chủ động, tin cậy và tăng trưởng.'],
            ['name' => 'IMPACT', 'slug' => 'impact', 'description' => 'Tạo tác động tích cực cho hội viên và cộng đồng.'],
            ['name' => 'FAMOUS', 'slug' => 'famous', 'description' => 'Nơi những kết nối chất lượng cùng lan tỏa giá trị.'],
        ])->map(fn (array $chapter, int $index) => BniChapter::query()->updateOrCreate(['slug' => $chapter['slug']], $chapter + [
            'bni_event_id' => $handover->id,
            'short_name' => $chapter['name'],
            'is_active' => true,
            'sort_order' => $index + 1,
        ]));

        foreach ([
            ['Kết nối ban điều hành', 'Cùng nhìn lại chặng đường và chuyển giao tinh thần lãnh đạo giữa các nhiệm kỳ.'],
            ['Lan tỏa văn hóa BNI', 'Củng cố giá trị Givers Gain và tạo thêm những kết nối tin cậy.'],
            ['Ghi nhận hành trình', 'Tôn vinh cá nhân, chapter và tập thể đã tạo nên những kết quả đáng nhớ.'],
            ['Khởi động nhiệm kỳ mới', 'Thống nhất định hướng để từng chapter bước vào chu kỳ phát triển tiếp theo.'],
        ] as $index => [$title, $description]) {
            BniPurpose::query()->updateOrCreate(['bni_event_id' => $handover->id, 'title' => $title], [
                'description' => $description,
                'sort_order' => $index + 1,
            ]);
        }

        foreach ([
            [1, '08:00', '09:00', 'Đón tiếp & kết nối', 'Check-in, giao lưu giữa các chapter.', 'Sảnh đón'],
            [1, '09:00', '11:30', 'Lễ chuyển giao Ban Điều hành', 'Nghi thức chuyển giao, tri ân và chia sẻ định hướng.', 'Hội trường chính'],
            [1, '18:00', '21:00', 'Gala dinner & sinh nhật', 'Đêm giao lưu, vinh danh và chúc mừng sinh nhật hội viên.', 'Không gian gala'],
            [2, '07:30', '11:30', 'Hoạt động kết nối chapter', 'Các hoạt động gắn kết và phiên chia sẻ kinh nghiệm.', 'Khu vực hoạt động'],
            [2, '13:30', '16:30', 'BNI Pickleball', 'Giải đấu giao hữu và kết nối cộng đồng.', 'Sân thi đấu'],
        ] as $index => [$day, $start, $end, $title, $description, $location]) {
            BniScheduleItem::query()->updateOrCreate(['bni_event_id' => $handover->id, 'day_number' => $day, 'title' => $title], [
                'starts_at' => $start,
                'ends_at' => $end,
                'description' => $description,
                'location' => $location,
                'sort_order' => $index + 1,
            ]);
        }

        foreach ([
            ['handover', 'Hình ảnh lễ chuyển giao', 'Khoảnh khắc trang trọng của nghi thức chuyển giao và vinh danh.'],
            ['gala', 'Gala dinner & sinh nhật', 'Một buổi tối kết nối, sẻ chia và lan tỏa niềm vui.'],
            ['pickleball', 'BNI Pickleball', 'Giải đấu giao hữu dành cho cộng đồng doanh nhân BNI.'],
        ] as $index => [$type, $title, $description]) {
            BniActivity::query()->updateOrCreate(['bni_event_id' => $handover->id, 'type' => $type], [
                'title' => $title,
                'description' => $description,
                'link_url' => $type === 'pickleball' ? route('bni.pickleball') : null,
                'sort_order' => $index + 1,
                'is_active' => true,
            ]);
        }

        $pickleball = BniEvent::query()->firstOrNew(['slug' => 'bni-pickleball']);
        $pickleballIsNew = ! $pickleball->exists;
        $pickleball->fill([
            'type' => 'pickleball',
            'title' => 'BNI Pickleball Championship',
            'kicker' => 'KẾT NỐI BẰNG NĂNG LƯỢNG',
            'summary' => 'Giải đấu giao hữu giúp hội viên kết nối ngoài không gian kinh doanh, bằng thể thao và tinh thần đồng đội.',
            'venue' => 'Sân thi đấu sẽ được cập nhật',
            'status' => 'published',
            'is_featured' => true,
        ]);
        if ($pickleballIsNew) {
            $pickleball->starts_at = $handover->starts_at?->copy()->addDay()->setTime(13, 30);
            $pickleball->ends_at = $handover->starts_at?->copy()->addDay()->setTime(18, 0);
        }
        $pickleball->save();

        foreach ([
            [1, '13:30', '14:00', 'Check-in vận động viên', 'Xác nhận danh sách và phổ biến điều lệ.', 'Khu check-in'],
            [1, '14:00', '17:00', 'Vòng bảng', 'Thi đấu theo bảng đấu đã công bố.', 'Sân thi đấu'],
            [1, '17:00', '18:00', 'Chung kết & trao giải', 'Tổng kết và vinh danh các đội thi đấu.', 'Sân trung tâm'],
        ] as $index => [$day, $start, $end, $title, $description, $location]) {
            BniScheduleItem::query()->updateOrCreate(['bni_event_id' => $pickleball->id, 'day_number' => $day, 'title' => $title], [
                'starts_at' => $start,
                'ends_at' => $end,
                'description' => $description,
                'location' => $location,
                'sort_order' => $index + 1,
            ]);
        }

        foreach ([
            ['event', 'Khởi động hành trình Lễ chuyển giao BNI', 'Cùng chuẩn bị cho một sự kiện kết nối bốn chapter.', true],
            ['chapter', 'KINHBAC sẵn sàng cho nhiệm kỳ mới', 'Những chia sẻ đầu tiên từ chapter KINHBAC.', false],
            ['chapter', 'IMPACT: Kết nối để tạo tác động', 'Câu chuyện về các giá trị được lan tỏa từ cộng đồng.', false],
            ['event', 'Gala dinner và sinh nhật hội viên', 'Không gian kết nối giàu cảm xúc trong khuôn khổ sự kiện.', false],
        ] as $index => [$type, $title, $excerpt, $featured]) {
            BniArticle::query()->updateOrCreate(['slug' => str($title)->slug()->toString()], [
                'bni_event_id' => $handover->id,
                'bni_chapter_id' => $type === 'chapter' ? $chapters->get($index % $chapters->count())?->id : null,
                'type' => $type,
                'title' => $title,
                'excerpt' => $excerpt,
                'body' => '<p>'.$excerpt.'</p>',
                'status' => 'published',
                'is_featured' => $featured,
                'published_at' => now()->subDays(4 - $index),
            ]);
        }
    }
}
