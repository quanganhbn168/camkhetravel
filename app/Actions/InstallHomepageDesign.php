<?php

namespace App\Actions;

use App\Models\HeroSlide;
use App\Models\Faq;
use App\Models\Post;
use App\Models\Service;
use App\Settings\HomepageSettings;
use App\Settings\WebsiteSettings;
use App\Support\Homepage\HomepageDesignDefaults;
use Awcodes\Curator\Models\Media;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

final class InstallHomepageDesign
{
    public function handle(): bool
    {
        $settings = app(HomepageSettings::class);
        if ($settings->design_version >= 1) {
            return false;
        }
        $manifest = json_decode(file_get_contents(public_path('images/homepage-redesign/v1/manifest.json')), true, flags: JSON_THROW_ON_ERROR);
        $images = [];
        // Fixed, versioned assets: import once, never replace a user's media record.
        foreach ($manifest as $asset) {
            $filename = $asset['name'].'.webp';
            $path = 'media/homepage/v1/'.$filename;
            foreach ([$filename, $asset['name'].'-768.webp'] as $file) {
                if (! Storage::disk('public')->exists('media/homepage/v1/'.$file)) {
                    Storage::disk('public')->put('media/homepage/v1/'.$file, file_get_contents(public_path('images/homepage-redesign/v1/'.$file)));
                }
            }
            $images[$asset['name']] = Media::firstOrCreate(['disk' => 'public', 'path' => $path], [
                'directory' => 'media/homepage/v1', 'visibility' => 'public', 'name' => $asset['name'],
                'width' => 1536, 'height' => 1024, 'size' => Storage::disk('public')->size($path),
                'type' => 'image/webp', 'ext' => 'webp', 'alt' => $asset['alt'], 'title' => $asset['label'],
                'description' => 'Ảnh minh họa tạo bằng AI cho thiết kế trang chủ. Không phải ảnh xác nhận đội xe hoặc khách hàng thực tế.',
            ])->id;
        }

        DB::transaction(function () use ($settings, $images): void {
            $serviceGroups = [
                'trip' => ['Bao xe đi tỉnh', 'Bao xe du lịch', 'Xe ghép Hà Nội ⇄ Cẩm Khê'],
                'partner' => ['Xe hợp đồng'], 'wedding' => ['Xe cưới – xe dâu'],
            ];
            if ($settings->audience_groups === []) {
                $settings->audience_groups = collect(HomepageDesignDefaults::audiences())->map(fn ($item) => [
                    ...$item, 'media_id' => $images['service-'.($item['key'] === 'trip' ? 'private' : $item['key'])],
                    'service_ids' => Service::whereIn('title', $serviceGroups[$item['key']])->pluck('id')->all(),
                ])->all();
            }
            $fleetImages = ['sedan' => 'fleet-vf8', 'mpv' => 'fleet-vf9', 'van' => 'fleet-minibus'];
            $settings->fleet_types = collect($settings->fleet_types)->map(fn ($item) => isset($fleetImages[$item['code'] ?? '']) && ! array_key_exists('media_id', $item)
                ? [...$item, 'media_id' => $images[$fleetImages[$item['code']]]] : $item)->all();
            $settings->section_content = array_replace(HomepageDesignDefaults::sections(), $settings->section_content);
            $settings->consultation_media_id ??= $images['journey-background'];
            if ($settings->route_items === []) {
                $settings->route_items = [
                    ['title' => 'Hà Nội ⇄ Cẩm Khê / Yên Lập', 'description' => 'Trao đổi điểm đón và lịch xe phù hợp.', 'service_id' => Service::where('title', 'Xe ghép Hà Nội ⇄ Cẩm Khê')->value('id')],
                    ['title' => 'Đi tỉnh theo lịch trình', 'description' => 'Công tác, thăm người thân, những cuộc hẹn.', 'service_id' => Service::where('title', 'Bao xe đi tỉnh')->value('id')],
                    ['title' => 'Du lịch và chương trình riêng', 'description' => 'Từ chuyến đi gia đình đến hành trình của đối tác.', 'service_id' => Service::where('title', 'Bao xe du lịch')->value('id')],
                ];
            }
            $settings->design_version = 1;
            $settings->settingsConfig()->resetDefaultValueLoadedProperties();
            $settings->save();

            $website = app(WebsiteSettings::class);
            if ($this->isPlaceholder($website->about_image_media_id)) {
                $website->about_image_media_id = $images['service-private'];
                $website->save();
            }
            if (! HeroSlide::exists()) {
                HeroSlide::create(['title' => 'Mỗi hành trình, một sự đồng hành.', 'description' => 'Xe cho chuyến đi riêng, chương trình của đối tác và ngày vui của gia đình.',
                    'primary_label' => 'Tư vấn phương án xe', 'primary_url' => '/#bao-gia', 'secondary_label' => 'Khám phá dịch vụ', 'secondary_url' => '/#dich-vu',
                    'curator_media_id' => $images['hero-vinfast'], 'is_active' => true, 'sort_order' => 10]);
            } else {
                foreach (HeroSlide::all() as $slide) {
                    if ($this->isPlaceholder($slide->curator_media_id)) {
                        $slide->curator_media_id = $images['hero-vinfast'];
                    }
                    // Only refresh the unchanged original seed copy.
                    if ($slide->title === 'Bao xe Phú Thọ' && $slide->description === 'Đi tỉnh · Du lịch gia đình · Công tác · Xe cưới') {
                        $slide->title = 'Mỗi hành trình, một sự đồng hành.';
                        $slide->description = 'Xe cho chuyến đi riêng, chương trình của đối tác và ngày vui của gia đình.';
                    }
                    $slide->save();
                }
            }
            foreach ($serviceGroups as $key => $titles) {
                foreach (Service::whereIn('title', $titles)->get() as $service) {
                    if ($this->isPlaceholder($service->curator_media_id)) {
                        $service->update(['curator_media_id' => $images['service-'.($key === 'trip' ? 'private' : $key)]]);
                    }
                }
            }
            foreach (['Trao đổi lịch đón cho chuyến xe cưới' => 'service-wedding', 'Chọn xe phù hợp với số khách và hành lý' => 'fleet-vf9', 'Chuẩn bị thông tin khi yêu cầu báo giá xe' => 'service-private'] as $title => $image) {
                $post = Post::where('title', $title)->first();
                if ($post && $this->isPlaceholder($post->curator_media_id)) {
                    $post->update(['curator_media_id' => $images[$image]]);
                }
            }
            if (! Faq::where('group', 'homepage')->exists()) {
                foreach (HomepageDesignDefaults::faqs() as $index => $faq) {
                    Faq::create([...$faq, 'group' => 'homepage', 'is_active' => true, 'sort_order' => ($index + 1) * 10]);
                }
            }
        });
        return true;
    }

    private function isPlaceholder(?int $id): bool
    {
        return ! $id || Media::whereKey($id)->where('path', 'media/site/no-image.svg')->exists();
    }
}
