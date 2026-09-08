<footer class="branding-footer">
    <div class="branding-container mx-auto w-full max-w-7xl px-4 lg:px-8 relative">
        <a class="branding-logo" href="#top" aria-label="THT Media – đầu trang">
            <img src="{{ $page['logo_url'] }}" alt="THT Media" width="180" height="60">
            <span><strong>THT MEDIA</strong><small>Đồng hành cùng thương hiệu Việt</small></span>
        </a>
        <nav aria-label="Liên kết chân trang">
            @foreach ($page['navigation'] as $item)
                <a href="{{ $item['url'] }}">{{ $item['label'] }}</a>
            @endforeach
        </nav>
        <div class="branding-copyright"><p>© {{ date('Y') }} THT MEDIA. All rights reserved.</p><p>{{ $content['footer_text'] }}</p></div>
    </div>
</footer>
