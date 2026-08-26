<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('landings', function (Blueprint $table): void {
            $table->string('faq_title')->nullable()->after('backstage_gallery');
            $table->text('faq_description')->nullable()->after('faq_title');
            $table->json('faq_items')->nullable()->after('faq_description');
        });

        $legacyLandingId = DB::table('content_items')
            ->where('source', 'wordpress')
            ->where('type', 'landing')
            ->where('slug', 'san-xuat-phim-doanh-nghiep')
            ->value('id');

        if (! $legacyLandingId) {
            return;
        }

        DB::table('landings')
            ->where('legacy_content_item_id', $legacyLandingId)
            ->whereNull('faq_items')
            ->update([
                'faq_title' => 'Câu hỏi thường gặp',
                'faq_description' => 'Trước khi sản xuất TVC doanh nghiệp',
                'faq_items' => json_encode([
                    [
                        'question' => 'Sản xuất một TVC mất bao lâu?',
                        'answer' => 'Thông thường từ 7 - 20 ngày tùy độ phức tạp. TVC đơn giản có thể nhanh hơn, còn TVC có concept riêng, diễn viên, nhiều bối cảnh và hậu kỳ nâng cao sẽ cần nhiều thời gian hơn.',
                    ],
                    [
                        'question' => 'Doanh nghiệp chưa có ý tưởng thì có làm được không?',
                        'answer' => 'Có. THT Media sẽ tư vấn concept, kịch bản, thông điệp, phong cách hình ảnh và cách triển khai phù hợp với mục tiêu truyền thông.',
                    ],
                    [
                        'question' => 'TVC có dùng để chạy quảng cáo Facebook, TikTok không?',
                        'answer' => 'Có. Video có thể được cắt thành nhiều phiên bản 16:9, 1:1 và 9:16 để dùng cho Facebook Ads, TikTok Ads, Reels, Shorts và landing page.',
                    ],
                    [
                        'question' => 'Chi phí TVC phụ thuộc vào những yếu tố nào?',
                        'answer' => 'Chi phí phụ thuộc vào concept, số ngày quay, thiết bị, bối cảnh, diễn viên, flycam, motion graphic, voice, nhạc bản quyền và số lượng phiên bản bàn giao.',
                    ],
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        Schema::table('landings', function (Blueprint $table): void {
            $table->dropColumn(['faq_title', 'faq_description', 'faq_items']);
        });
    }
};
