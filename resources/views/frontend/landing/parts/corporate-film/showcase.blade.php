<?php

$showcase = $args['landing']['showcase'] ?? [];
$items = $showcase['items'] ?? [];

if (empty($items)) {
    return;
}
?>
<section class="tht-landing-film-showcase" id="du-an" aria-labelledby="tht-landing-film-showcase-title">
    <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8">
        <header class="tht-landing-film-showcase__head" data-aos="fade-up">
            <div>
                <p class="tht-landing-eyebrow"><?php echo e($showcase['eyebrow'] ?? 'DỰ ÁN ĐÃ TRIỂN KHAI'); ?></p>
                <h2 id="tht-landing-film-showcase-title"><?php echo e($showcase['title'] ?? 'Các sản phẩm tiêu biểu'); ?></h2>
            </div>
        </header>

        <div class="tht-landing-film-showcase__grid" data-aos="fade-up" data-aos-delay="80">
            <?php foreach ($items as $index => $item) :
                $video_url = trim((string) ($item['video_url'] ?? ''));
                $thumbnail_url = trim((string) ($item['thumbnail_url'] ?? ''));

                if ($thumbnail_url === '' && $video_url !== '') {
                    $video_host = strtolower((string) parse_url($video_url, PHP_URL_HOST));
                    $video_path = trim((string) parse_url($video_url, PHP_URL_PATH), '/');
                    $youtube_id = '';

                    if ($video_host === 'youtu.be' || $video_host === 'www.youtu.be') {
                        $youtube_id = strtok($video_path, '/');
                    } elseif (strpos($video_host, 'youtube.com') !== false) {
                        parse_str((string) parse_url($video_url, PHP_URL_QUERY), $video_query);
                        $youtube_id = (string) ($video_query['v'] ?? '');
                    }

                    if (preg_match('/^[A-Za-z0-9_-]{11}$/', $youtube_id)) {
                        $thumbnail_url = 'https://i.ytimg.com/vi/' . $youtube_id . '/hqdefault.jpg';
                    }
                }

                $image_url = $thumbnail_url !== '' ? $thumbnail_url : \App\Support\Landing\LandingRegistry::assetUrl($item['image'] ?? '');
                $media_url = $video_url !== '' ? $video_url : $image_url;
            ?>
                <article class="tht-landing-film-showcase__card">
                    <a
                        class="tht-landing-film-showcase__media glightbox"
                        href="<?php echo e($media_url); ?>"
                        data-gallery="tht-landing-film-projects"
                        data-title="<?php echo e($item['title'] ?? ''); ?>"
                        <?php if ($video_url !== '') : ?>data-type="video"<?php endif; ?>
                    >
                        <img src="<?php echo e($image_url); ?>" alt="<?php echo e($item['image_alt'] ?? $item['title'] ?? ''); ?>" width="<?php echo e((string) ($item['width'] ?? 1536)); ?>" height="<?php echo e((string) ($item['height'] ?? 1024)); ?>" loading="lazy" decoding="async">
                        <span class="tht-landing-film-showcase__shade" aria-hidden="true"></span>
                        <?php if ($video_url !== '') : ?>
                            <span class="tht-landing-film-showcase__play" aria-hidden="true"><i class="fa-solid fa-play"></i></span>
                        <?php endif; ?>
                        <span class="tht-landing-film-showcase__caption">
                            <small><?php echo e($item['category'] ?? 'Dự án phim'); ?></small>
                            <strong><?php echo e($item['title'] ?? ''); ?></strong>
                            <em><?php echo e($video_url !== '' ? 'Xem phim mẫu' : 'Xem hình ảnh'); ?></em>
                        </span>
                    </a>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
