@props(['name', 'size' => 18])

@php
    $paths = [
        'users'  => '<circle cx="9" cy="8" r="3.2"/><path d="M3 20c0-3.5 2.7-6 6-6s6 2.5 6 6"/><circle cx="17" cy="9" r="2.4"/><path d="M15.5 14.2c3 .1 5 1.8 5 4.8"/>',
        'plus'   => '<path d="M12 5v14M5 12h14"/>',
        'eye'    => '<path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/><circle cx="12" cy="12" r="3"/>',
        'edit'   => '<path d="M12 20h9M16.5 3.5l4 4L8 20l-5 1 1-5z"/>',
        'trash'  => '<path d="M3 6h18M8 6V4h8v2M6 6l1 14h10l1-14"/>',
        'search' => '<circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/>',
        'check'  => '<path d="M5 12.5l4.5 4.5L19 7"/>',
        'back'   => '<path d="M15 5l-7 7 7 7"/>',
        'warn'   => '<path d="M12 3l10 18H2z"/><path d="M12 10v5M12 18v.5"/>',
    ];
@endphp

<svg viewBox="0 0 24 24" width="{{ $size }}" height="{{ $size }}" fill="none"
     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
     aria-hidden="true" {{ $attributes }}>{!! $paths[$name] ?? '' !!}</svg>
