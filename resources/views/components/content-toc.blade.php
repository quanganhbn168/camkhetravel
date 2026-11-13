@props(['toc', 'title' => 'Nội dung chính', 'id' => 'content-toc'])
{{-- Mục lục một cột, tự thu gọn mờ dần + "Xem thêm" khi dài (resources/js/frontend/expandable.js). --}}
@if (count($toc) >= 2)
    <nav {{ $attributes->class('content-toc') }} aria-labelledby="{{ $id }}-title">
        <p class="content-toc__title" id="{{ $id }}-title"><i class="fa-solid fa-list-ul" aria-hidden="true"></i>{{ $title }}</p>
        <div class="expandable" data-expandable data-expandable-max="240">
            <div class="expandable__body" id="{{ $id }}-list">
                <ol class="content-toc__list">
                    @foreach ($toc as $item)
                        <li @class(['content-toc__item', 'content-toc__item--sub' => $item['level'] === 3])><a href="#{{ $item['id'] }}">{{ $item['title'] }}</a></li>
                    @endforeach
                </ol>
            </div>
            <button class="expandable__toggle" type="button" aria-controls="{{ $id }}-list" aria-expanded="false" hidden data-expandable-toggle>
                <span data-expandable-label>Xem thêm</span><i class="fa-solid fa-chevron-down" aria-hidden="true"></i>
            </button>
        </div>
    </nav>
@endif
