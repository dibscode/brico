@php
    $title = config('company.name', config('app.name', 'BRI Cooperative'));
    $description = 'Sistem manajemen koperasi (BRI Cooperative): anggota, simpanan, pinjaman, kas, dan laporan.';

    $logo = config('company.logo', 'logobrico.png');
    $imageUrl = asset($logo);
@endphp

<link rel="icon" type="image/png" href="{{ asset('logobrico.png') }}">

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<meta property="og:title" content="{{ $title }}" />
<meta property="og:description" content="{{ $description }}" />
<meta property="og:image" content="{{ $imageUrl }}" />
<meta property="og:url" content="{{ url()->current() }}" />

<style>
    :root {
        --brico-primary: #1976D2;
        --brico-primary-hover: #1565C0;
    }

    :is(a, button, .fi-btn).fi-btn.fi-color-primary,
    :is(a, button, .fi-btn).fi-btn.fi-color.fi-color-primary,
    :is(a, button, .fi-btn).fi-btn.fi-outlined.fi-color-primary,
    :is(a, button, .fi-btn).fi-btn.fi-outlined.fi-color.fi-color-primary {
        background-color: var(--brico-primary) !important;
        color: #ffffff !important;
        --tw-ring-color: var(--brico-primary) !important;
    }

    :is(a, button, .fi-btn).fi-btn.fi-color-primary > .fi-icon,
    :is(a, button, .fi-btn).fi-btn.fi-color.fi-color-primary > .fi-icon,
    :is(a, button, .fi-btn).fi-btn.fi-outlined.fi-color-primary > .fi-icon,
    :is(a, button, .fi-btn).fi-btn.fi-outlined.fi-color.fi-color-primary > .fi-icon {
        color: #ffffff !important;
    }

    @media (hover: hover) {
        :is(a, button, .fi-btn).fi-btn.fi-color-primary:hover,
        :is(a, button, .fi-btn).fi-btn.fi-color.fi-color-primary:hover,
        :is(a, button, .fi-btn).fi-btn.fi-outlined.fi-color-primary:hover,
        :is(a, button, .fi-btn).fi-btn.fi-outlined.fi-color.fi-color-primary:hover {
            background-color: var(--brico-primary-hover) !important;
            --tw-ring-color: var(--brico-primary-hover) !important;
        }
    }
</style>
