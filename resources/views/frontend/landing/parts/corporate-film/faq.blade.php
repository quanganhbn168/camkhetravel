<?php

$faq = $args['landing']['faq'] ?? [];

if (empty($faq['items'])) {
    return;
}
?>

<section class="tht-landing-faq" id="faq" aria-labelledby="tht-landing-faq-title">
    <div class="tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8">
        <div class="grid gap-8 lg:grid-cols-12">
            <div class="lg:col-span-5" data-aos="fade-up">
                <div class="tht-landing-faq__heading">
                    <h2 id="tht-landing-faq-title"><?php echo e($faq['title'] ?? ''); ?></h2>
                </div>
            </div>
            <div class="lg:col-span-7" data-aos="fade-up" data-aos-delay="100">
                <div class="tht-landing-accordion" x-data="{ active: 0 }">
                    <?php foreach ($faq['items'] as $index => $item) :
                        $question_id = 'tht-landing-faq-question-' . $index;
                        $answer_id = 'tht-landing-faq-answer-' . $index;
                    ?>
                        <article class="tht-landing-accordion__item">
                            <h3>
                                <button
                                    class="tht-landing-accordion__trigger"
                                    type="button"
                                    id="<?php echo e($question_id); ?>"
                                    @click="active = active === <?php echo e((string) $index); ?> ? null : <?php echo e((string) $index); ?>"
                                    :aria-expanded="(active === <?php echo e((string) $index); ?>).toString()"
                                    aria-controls="<?php echo e($answer_id); ?>"
                                >
                                    <span><?php echo e($item['question']); ?></span>
                                    <span class="tht-landing-accordion__mark" :class="{ 'is-open': active === <?php echo e((string) $index); ?> }" aria-hidden="true"></span>
                                </button>
                            </h3>
                            <div
                                class="tht-landing-accordion__panel"
                                id="<?php echo e($answer_id); ?>"
                                role="region"
                                aria-labelledby="<?php echo e($question_id); ?>"
                                x-cloak
                                x-show="active === <?php echo e((string) $index); ?>"
                                x-transition.opacity.duration.180ms
                            >
                                <p><?php echo e($item['answer']); ?></p>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>
