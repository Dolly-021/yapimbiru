@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 border-b-2 border-yellow-300 dark:border-yellow-400 text-sm font-medium leading-5 text-white dark:text-white focus:outline-none focus:border-yellow-400 transition duration-150 ease-in-out'
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-white/70 dark:text-cyan-200 hover:text-white dark:hover:text-white hover:border-cyan-300 dark:hover:border-cyan-300 focus:outline-none focus:text-white dark:focus:text-white focus:border-yellow-300 dark:focus:border-yellow-300 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
