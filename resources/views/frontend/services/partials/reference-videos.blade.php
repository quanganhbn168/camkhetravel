@if ($hasReferenceVideos || $hasReferenceImages)
    <section class="resource-related-section" id="tai-lieu-tham-khao">
        <div class="container">
            <header class="mx-auto text-center mb-4">
                <h2 class="display-title h2">Ảnh và video tham khảo</h2>
            </header>

            @if ($hasReferenceTabs)
                <div class="nav nav-pills gap-2 mb-4" role="tablist" aria-label="Tài liệu tham khảo">
                    <button class="nav-link {{ $hasReferenceVideos ? 'active' : '' }}" type="button" role="tab" data-bs-toggle="tab" data-bs-target="#service-reference-videos" aria-controls="service-reference-videos" aria-selected="{{ $hasReferenceVideos ? 'true' : 'false' }}">Video</button>
                    <button class="nav-link {{ $hasReferenceVideos ? '' : 'active' }}" type="button" role="tab" data-bs-toggle="tab" data-bs-target="#service-reference-images" aria-controls="service-reference-images" aria-selected="{{ $hasReferenceVideos ? 'false' : 'true' }}">Ảnh</button>
                </div>
            @endif

            <div class="tab-content">
            @if ($hasReferenceVideos)
                <div class="service-reference__panel  tab-pane fade {{ $hasReferenceVideos ? 'show active' : '' }}" id="service-reference-videos" role="tabpanel">
                    <div class="service-reference__grid">
                        @foreach ($referenceVideos as $video)
                            <a class="service-reference__card" href="{{ $video['url'] }}" target="_blank" rel="noopener" aria-label="Xem video {{ $video['title'] }}">
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
                <div class="service-reference__panel  tab-pane fade {{ $hasReferenceVideos ? '' : 'show active' }}" id="service-reference-images" role="tabpanel">
                    <div class="service-reference__grid">
                        @foreach ($referenceImages as $imageUrl)
                            <a class="service-reference__card" href="{{ $imageUrl }}" target="_blank" rel="noopener" aria-label="Mở ảnh tham khảo {{ $loop->iteration }} của {{ $service->title }}">
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
        </div>
    </section>
@endif
