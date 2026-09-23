@if ($item['image_url'] ?? null)
    <img class="project-case__item-image" src="{{ $item['image_url'] }}" alt="" loading="lazy">
@elseif ($item['icon'] ?? null)
    <i class="fa-solid {{ $item['icon'] }}" aria-hidden="true"></i>
@endif
