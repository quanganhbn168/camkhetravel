<section class="testimonials section-space" id="khach-hang-noi-gi">
    <div class="container testimonials__layout">
        <div >
            <h2>Khách hàng nói về chúng tôi</h2>
            <p class="lead">Những chia sẻ từ các hành trình đã đồng hành.</p>
        </div>
        <div class="testimonials__slider">
            <div class="swiper" data-testimonial-swiper>
                <div class="swiper-wrapper">
                    @forelse ($testimonials as $testimonial)
                        <div class="swiper-slide h-auto">
                            <article class="testimonial">
                                @if ($testimonial->rating)
                                    <div class="testimonial__rating" aria-label="{{ $testimonial->rating }} trên 5 sao">
                                        @for ($star = 1; $star <= $testimonial->rating; $star++)
                                            <span aria-hidden="true">★</span>
                                        @endfor
                                    </div>
                                @endif
                                <blockquote class="testimonial__quote">“{{ $testimonial->quote }}”</blockquote>
                                <div class="testimonial__person">
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
                        <div class="swiper-slide"><p >Phản hồi khách hàng sẽ hiển thị tại đây sau khi được thêm và bật trong quản trị.</p></div>
                    @endforelse
                </div>
            </div>
            @if ($testimonials->isNotEmpty())
                <button class="btn btn-outline-primary" type="button" aria-label="Phản hồi trước" data-testimonial-swiper-prev>
                    <span aria-hidden="true">←</span>
                </button>
                <button class="btn btn-outline-primary" type="button" aria-label="Phản hồi tiếp theo" data-testimonial-swiper-next>
                    <span aria-hidden="true">→</span>
                </button>
            @endif
        </div>
    </div>
</section>
