@php
    $hasReferenceVideos = $referenceVideos !== [];
    $hasReferenceImages = $referenceImages !== [];
    $hasReferenceTabs = $hasReferenceVideos && $hasReferenceImages;
    $referenceInitialTab = $hasReferenceVideos ? 'videos' : 'images';
@endphp

@if ($hasReferenceVideos || $hasReferenceImages)
    <section class="service-reference resource-related-section border-y border-slate-100 bg-white" id="tai-lieu-tham-khao" x-data="{ activeTab: '{{ $referenceInitialTab }}' }">
        <div class="site-shell">
            <header class="mx-auto max-w-3xl text-center">
                <h2 class="display-title text-3xl leading-tight md:text-4xl">Các dự án nổi bật</h2>
            </header>

            @if ($hasReferenceTabs)
                <div class="service-reference__tabs mt-8" role="tablist" aria-label="Tài liệu tham khảo">
                    <button class="service-reference__tab" type="button" role="tab" :aria-selected="(activeTab === 'videos').toString()" :class="{ 'is-active': activeTab === 'videos' }" @click="activeTab = 'videos'">Video</button>
                    <button class="service-reference__tab" type="button" role="tab" :aria-selected="(activeTab === 'images').toString()" :class="{ 'is-active': activeTab === 'images' }" @click="activeTab = 'images'">Ảnh</button>
                </div>
            @endif

            @if ($hasReferenceVideos)
                <div class="service-reference__panel mt-9" role="tabpanel" x-show="activeTab === 'videos'" x-cloak>
                    <div class="service-reference__grid">
                        @foreach ($referenceVideos as $video)
                            <a class="service-reference__card glightbox group" href="{{ $video['url'] }}" data-type="video" data-gallery="service-reference-videos-{{ $service->id }}" data-title="{{ $video['title'] }}" aria-label="Xem video {{ $video['title'] }}">
                                <div class="service-reference__media">
                                    @if ($video['thumbnail_url'] ?? null)
                                        <img src="{{ $video['thumbnail_url'] }}" alt="{{ $video['title'] }}" loading="lazy">
                                    @else
                                        <div class="service-reference__placeholder" aria-hidden="true">▶</div>
                                    @endif
                                    <span class="service-reference__play" aria-hidden="true">▶</span>
                                    <div class="service-reference__overlay">
                                        <h3>{{ $video['title'] }}</h3>
                                        @if ($video['description'])
                                            <p>{{ $video['description'] }}</p>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($hasReferenceImages)
                <div class="service-reference__panel mt-9" role="tabpanel" x-show="activeTab === 'images'" x-cloak>
                    <div class="service-reference__grid">
                        @foreach ($referenceImages as $imageUrl)
                            <a class="service-reference__card glightbox group" href="{{ $imageUrl }}" data-type="image" data-gallery="service-reference-images-{{ $service->id }}" data-title="{{ $service->title }} — ảnh tham khảo {{ $loop->iteration }}" aria-label="Mở ảnh tham khảo {{ $loop->iteration }} của {{ $service->title }}">
                                <div class="service-reference__media">
                                    <img src="{{ $imageUrl }}" alt="{{ $service->title }} — ảnh tham khảo {{ $loop->iteration }}" loading="lazy">
                                    <div class="service-reference__overlay">
                                        <h3>{{ $service->title }}</h3>
                                        <p>Ảnh tài liệu tham khảo {{ $loop->iteration }}</p>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>
@endif
