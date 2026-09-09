@if (! empty($content['partner_rows']))
    <section class="branding-section branding-partners" aria-labelledby="branding-partners-title">
        <div class="branding-container mx-auto w-full max-w-7xl px-4 lg:px-8 relative">
            <div class="branding-section-heading">
                <h2 id="branding-partners-title">ĐỐI TÁC ĐỒNG HÀNH TIN CẬY</h2>
                <button class="branding-partners-toggle" type="button" aria-pressed="false" aria-controls="branding-partner-rows">Tạm dừng chuyển động</button>
            </div>
            <div id="branding-partner-rows" class="branding-partner-rows">
                @foreach ($content['partner_rows'] as $partners)
                    <div class="branding-partner-row">
                        <div class="branding-partner-track">
                            @for ($copy = 0; $copy < 2; $copy++)
                                <div class="branding-partner-group" @if ($copy) aria-hidden="true" @endif>
                                    @foreach ($partners as $partner)
                                        <div class="branding-partner-logo" @if ($partner['repeated']) aria-hidden="true" @endif>
                                            <img src="{{ $partner['image'] }}" alt="{{ $copy || $partner['repeated'] ? '' : $partner['name'] }}" width="176" height="80" loading="lazy" decoding="async">
                                        </div>
                                    @endforeach
                                </div>
                            @endfor
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
