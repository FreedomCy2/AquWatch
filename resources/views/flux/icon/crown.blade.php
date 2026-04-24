{{-- Credit: Lucide (https://lucide.dev) --}}

@props([
    'variant' => 'outline',
])

@php
    if ($variant === 'solid') {
        throw new \Exception('The "solid" variant is not supported in Lucide.');
    }

    $classes = Flux::classes('shrink-0')->add(
        match ($variant) {
            'outline' => '[:where(&)]:size-6',
            'solid' => '[:where(&)]:size-6',
            'mini' => '[:where(&)]:size-5',
            'micro' => '[:where(&)]:size-4',
        },
    );

    $strokeWidth = match ($variant) {
        'outline' => 2,
        'mini' => 2.25,
        'micro' => 2.5,
    };
@endphp

<svg
    {{ $attributes->class($classes) }}
    data-flux-icon
    xmlns="http://www.w3.org/2000/svg"
    viewBox="0 0 24 24"
    fill="none"
    stroke="currentColor"
    stroke-width="{{ $strokeWidth }}"
    stroke-linecap="round"
    stroke-linejoin="round"
    aria-hidden="true"
    data-slot="icon"
>
    <path d="m2 7 4.41 5.88a2 2 0 0 0 3.2-.03L12 10l2.39 2.85a2 2 0 0 0 3.2.03L22 7" />
    <path d="M5 18h14" />
    <path d="M5 22h14" />
    <path d="M5 18 3 7" />
    <path d="M19 18 21 7" />
    <path d="M9 6.75c0-1.24 1.01-2.25 2.25-2.25S13.5 5.51 13.5 6.75c0 .46-.14.9-.38 1.27l-.62.98-.62-.98a2.24 2.24 0 0 1-.38-1.27Z" />
</svg>
