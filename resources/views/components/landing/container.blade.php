@props(['class' => ''])

<div {{ $attributes->class(['tht-landing-container mx-auto w-full max-w-7xl px-4 lg:px-8', $class]) }}>
    {{ $slot }}
</div>
