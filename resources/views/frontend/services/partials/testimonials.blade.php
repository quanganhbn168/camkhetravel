<section class="home-testimonials section-space" id="khach-hang-noi-gi">
    <div class="site-container home-testimonials__layout w-100 mx-auto site-services-partials-testimonials__div-1">
        <div class="home-testimonials__intro">
            <h2>Khách hàng nói về chúng tôi</h2>
            <p class="site-services-partials-testimonials__copy-2">Những chia sẻ từ các hành trình đã đồng hành.</p>
        </div>
        <div class="home-testimonials__slider">
            <div class="swiper" data-testimonial-swiper>
                <div class="swiper-wrapper">
                    @forelse ($testimonials as $testimonial)
                        <div class="swiper-slide h-auto">
                            <article class="home-testimonial">
                                @if ($testimonial->rating)
                                    <div class="home-testimonial__rating" aria-label="{{ $testimonial->rating }} trên 5 sao">
                                        @for ($star = 1; $star <= $testimonial->rating; $star++)
                                            <span aria-hidden="true">★</span>
                                        @endfor
                                    </div>
                                @endif
                                <blockquote class="home-testimonial__quote">“{{ $testimonial->quote }}”</blockquote>
                                <div class="home-testimonial__person">
                                    @if ($testimonial->curatorMedia?->url)
                                        <img src="{{ $testimonial->curatorMedia->url }}" alt="{{ $testimonial->client_name }}" loading="lazy">
                                    @else
                                        <span>{{ mb_substr($testimonial->client_name, 0, 1) }}</span>
                                    @endif
                                    <div><p>{{ $testimonial->client_name }}</p><p>{{ collect([$testimonial->client_role, $testimonial->company_name])->filter()->implode(' · ') }}</p></div>
                                </div>
                            </article>
                        </div>
                    @empty
                        <div class="swiper-slide"><p class="home-testimonials__empty">Phản hồi khách hàng sẽ hiển thị tại đây sau khi được thêm và bật trong quản trị.</p></div>
                    @endforelse
                </div>
            </div>
            @if ($testimonials->isNotEmpty())
                <button class="position-absolute site-services-partials-testimonials__action-4" type="button" aria-label="Phản hồi trước" data-testimonial-swiper-prev>
                    <span aria-hidden="true">←</span>
                </button>
                <button class="position-absolute site-services-partials-testimonials__action-5" type="button" aria-label="Phản hồi tiếp theo" data-testimonial-swiper-next>
                    <span aria-hidden="true">→</span>
                </button>
            @endif
        </div>
    </div>
</section>
