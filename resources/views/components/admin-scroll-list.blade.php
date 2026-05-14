@props(['columns' => ''])

<div {{ $attributes->merge(['class' => trim('admin-scroll-list '.$columns)]) }}>
    {{ $slot }}
</div>
