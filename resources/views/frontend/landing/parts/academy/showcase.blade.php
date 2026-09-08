<?php
$section = $args['landing']['showcase'] ?? [];
if (empty($section['items'])) return;
$gallery_base = 'khoahocnhiepanhthucchien/assets/images/casestudy';
?>
<section class="academy-section academy-outcome-section" id="thanh-qua" aria-labelledby="academy-outcome-title">
    <div class="academy-container mx-auto w-full max-w-7xl px-4 lg:px-8">
        <div class="academy-section-head" data-aos="fade-up"><span class="academy-label"><?php echo e($section['eyebrow'] ?? ''); ?></span><h2 id="academy-outcome-title"><?php echo e($section['title'] ?? ''); ?></h2></div>
        <div class="academy-student-grid">
            <?php foreach ($section['items'] as $index => $item) : ?>
                <?php
                $folder = \App\Support\Landing\LandingView::fileName((string) ($item['folder'] ?? ''));
                $gallery_path = storage_path('app/public/media/landing/pages/khoahocnhiepanhthucchien/assets/images/casestudy/' . $folder);
                $images = $folder !== '' && is_dir($gallery_path)
                    ? glob($gallery_path . '/*.{jpg,jpeg,png,webp,avif,JPG,JPEG,PNG,WEBP,AVIF}', GLOB_BRACE)
                    : [];
                natsort($images);
                $images = array_values($images);
                $cover_image = $images[0] ?? '';
                $cover_relative = $cover_image !== '' ? $gallery_base . '/' . $folder . '/' . basename($cover_image) : '';
                $avatar = (string) ($item['avatar'] ?? '');
                $modal_id = 'academy-student-gallery-' . $index;
                ?>
                <article class="academy-student-card" data-aos="fade-up" data-aos-delay="<?php echo e((string) ($index * 60)); ?>">
                    <div class="academy-student-profile__cover">
                        <?php if ($cover_relative !== '') : ?>
                            <img src="<?php echo e(\App\Support\Landing\LandingRegistry::assetUrl($cover_relative)); ?>" alt="Sản phẩm portfolio của <?php echo e($item['title']); ?>" width="800" height="600" loading="lazy">
                        <?php else : ?>
                            <div class="academy-student-profile__cover-empty"><i class="fa-solid fa-camera-retro" aria-hidden="true"></i></div>
                        <?php endif; ?>
                        <span>Portfolio học viên</span>
                    </div>
                    <div class="academy-student-body">
                        <div class="academy-student-profile__identity">
                            <div class="academy-student-profile__avatar">
                                <?php if ($avatar !== '') : ?><img src="<?php echo e(\App\Support\Landing\LandingRegistry::assetUrl($avatar)); ?>" alt="<?php echo e($item['title']); ?>"><?php else : ?><i class="fa-solid fa-user" aria-hidden="true"></i><?php endif; ?>
                            </div>
                            <div><span class="academy-student-profile__eyebrow"><?php echo e($item['course'] ?? 'THT Academy'); ?></span><h3><?php echo e($item['title']); ?></h3></div>
                        </div>
                        <p><?php echo e($item['focus'] ?? 'Sản phẩm thực hành trong khóa học.'); ?></p><button type="button" class="academy-student-button" data-landing-modal-open="#<?php echo e($modal_id); ?>">XEM PORTFOLIO <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></button>
                    </div>
                </article>
                <div class="modal fade academy-showcase-modal" id="<?php echo e($modal_id); ?>" tabindex="-1" aria-labelledby="<?php echo e($modal_id); ?>-title" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable"><div class="modal-content"><div class="modal-header"><div><span>THT ACADEMY • PORTFOLIO HỌC VIÊN</span><h2 id="<?php echo e($modal_id); ?>-title"><?php echo e($item['title']); ?></h2></div><button type="button" class="tht-landing-modal-close" data-landing-modal-close aria-label="Đóng"></button></div><div class="modal-body">
                        <?php if (!empty($images)) : ?><div class="academy-student-gallery"><?php foreach ($images as $image) : ?><?php $image_relative = $gallery_base . '/' . $folder . '/' . basename($image); ?><a href="<?php echo e(\App\Support\Landing\LandingRegistry::assetUrl($image_relative)); ?>" target="_blank" rel="noopener"><img src="<?php echo e(\App\Support\Landing\LandingRegistry::assetUrl($image_relative)); ?>" alt="Sản phẩm của <?php echo e($item['title']); ?>" loading="lazy"></a><?php endforeach; ?></div>
                        <?php else : ?><div class="academy-student-gallery-empty"><i class="fa-solid fa-images" aria-hidden="true"></i><h3>Gallery đang được cập nhật</h3></div><?php endif; ?>
                    </div></div></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
