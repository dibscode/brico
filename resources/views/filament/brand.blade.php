<div
    style="display: flex; align-items: center; gap: 8px; white-space: nowrap;"
>
    <img
        src="{{ asset(config('company.logo', 'logobrico.png')) }}"
        alt="{{ config('company.name', config('app.name')) }}"
        style="height: 32px; width: 32px; max-height: 32px; max-width: 32px; object-fit: contain; flex: 0 0 auto;"
    />

    <span
        class="fi-brand-text"
        style="font-weight: 600; line-height: 1.2;"
    >
        {{ config('company.name', config('app.name')) }}
    </span>
</div>

<style>
    @media (max-width: 767.98px) {
        .fi-brand-text {
            display: none;
        }
    }
</style>
