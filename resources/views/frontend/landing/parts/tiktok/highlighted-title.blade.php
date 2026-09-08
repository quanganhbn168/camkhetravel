@php
    $highlighted = preg_replace('/'.preg_quote($pink, '/').'/iu', '<span class="tt-pink">$0</span>', e($title));
    $highlighted = preg_replace('/'.preg_quote($cyan, '/').'/iu', '<span class="tt-cyan">$0</span>', $highlighted);
@endphp
{!! $highlighted !!}
