<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hero_slides', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('curator_media_id')->nullable()->constrained('curator')->nullOnDelete();
            $table->string('eyebrow')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('primary_label')->nullable();
            $table->string('primary_url')->nullable();
            $table->string('secondary_label')->nullable();
            $table->string('secondary_url')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->timestamps();
        });

        Schema::create('hero_slide_translations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('hero_slide_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 10);
            $table->string('eyebrow')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('primary_label')->nullable();
            $table->string('primary_url')->nullable();
            $table->string('secondary_label')->nullable();
            $table->string('secondary_url')->nullable();
            $table->timestamps();

            $table->unique(['hero_slide_id', 'locale']);
        });

        $now = now();
        $slides = [
            [
                'eyebrow' => 'THT Media · Strategic Creative Partner',
                'title' => 'Biến câu chuyện thương hiệu thành trải nghiệm đáng nhớ.',
                'description' => 'Từ chiến lược, sản xuất nội dung đến kích hoạt thương hiệu — THT Media đồng hành để ý tưởng đi đến đúng khách hàng.',
                'primary_label' => 'Trao đổi dự án',
                'secondary_label' => 'Xem dự án',
            ],
            [
                'eyebrow' => 'Creative production',
                'title' => 'Nội dung có chiều sâu, hình ảnh có sức lan tỏa.',
                'description' => 'Một quy trình gọn gàng để đội ngũ, thông điệp và thước phim cùng đi về một hướng.',
                'primary_label' => 'Khám phá dịch vụ',
                'secondary_label' => 'Gặp đội ngũ',
            ],
            [
                'eyebrow' => 'Brand activation',
                'title' => 'Những khoảnh khắc thật khiến thương hiệu được nhớ đến.',
                'description' => 'Chúng tôi thiết kế sự kiện và chiến dịch bằng tư duy trải nghiệm, từ trước khi diễn ra đến sau khi lan tỏa.',
                'primary_label' => 'Bắt đầu cùng THT',
                'secondary_label' => 'Xem tin tức',
            ],
        ];

        foreach ($slides as $index => $slide) {
            $id = DB::table('hero_slides')->insertGetId([
                ...$slide,
                'is_active' => true,
                'sort_order' => $index + 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            foreach ($this->translationsFor($index) as $translation) {
                DB::table('hero_slide_translations')->insert([
                    'hero_slide_id' => $id,
                    ...$translation,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('hero_slide_translations');
        Schema::dropIfExists('hero_slides');
    }

    private function translationsFor(int $index): array
    {
        return match ($index) {
            0 => [
                ['locale' => 'en', 'eyebrow' => 'THT Media · Strategic Creative Partner', 'title' => 'Turn brand stories into memorable experiences.', 'description' => 'From strategy and content production to brand activation, THT Media helps ideas reach the right audience.', 'primary_label' => 'Discuss a project', 'secondary_label' => 'View projects'],
                ['locale' => 'zh', 'eyebrow' => 'THT Media · 品牌创意伙伴', 'title' => '将品牌故事转化为难忘的体验。', 'description' => '从策略、内容制作到品牌激活，THT Media 让创意抵达真正的受众。', 'primary_label' => '洽谈项目', 'secondary_label' => '查看项目'],
                ['locale' => 'ko', 'eyebrow' => 'THT Media · 전략적 크리에이티브 파트너', 'title' => '브랜드 스토리를 기억에 남는 경험으로 만듭니다.', 'description' => '전략과 콘텐츠 제작부터 브랜드 활성화까지, THT Media가 아이디어를 고객에게 연결합니다.', 'primary_label' => '프로젝트 상담', 'secondary_label' => '프로젝트 보기'],
            ],
            1 => [
                ['locale' => 'en', 'eyebrow' => 'Creative production', 'title' => 'Content with depth. Images with reach.', 'description' => 'A focused process that keeps the team, message and final frame moving in the same direction.', 'primary_label' => 'Explore services', 'secondary_label' => 'Meet the team'],
                ['locale' => 'zh', 'eyebrow' => '创意制作', 'title' => '有深度的内容，有传播力的画面。', 'description' => '以清晰流程让团队、信息与成片始终朝同一方向前进。', 'primary_label' => '探索服务', 'secondary_label' => '认识团队'],
                ['locale' => 'ko', 'eyebrow' => '크리에이티브 프로덕션', 'title' => '깊이 있는 콘텐츠, 확산되는 이미지.', 'description' => '팀과 메시지, 결과물이 한 방향으로 움직이도록 명확한 프로세스를 만듭니다.', 'primary_label' => '서비스 살펴보기', 'secondary_label' => '팀 만나기'],
            ],
            default => [
                ['locale' => 'en', 'eyebrow' => 'Brand activation', 'title' => 'Real moments that make a brand memorable.', 'description' => 'We design events and campaigns around experience, before, during and after the moment of impact.', 'primary_label' => 'Start with THT', 'secondary_label' => 'Read our journal'],
                ['locale' => 'zh', 'eyebrow' => '品牌激活', 'title' => '让品牌被记住的真实瞬间。', 'description' => '我们从发生前、进行中到传播后，以体验为核心设计活动与整合传播。', 'primary_label' => '与 THT 开始合作', 'secondary_label' => '阅读动态'],
                ['locale' => 'ko', 'eyebrow' => '브랜드 활성화', 'title' => '브랜드를 기억하게 만드는 진짜 순간.', 'description' => '영향이 시작되기 전부터 현장과 확산 이후까지, 경험을 중심으로 이벤트와 캠페인을 설계합니다.', 'primary_label' => 'THT와 시작하기', 'secondary_label' => '스토리 보기'],
            ],
        };
    }
};
