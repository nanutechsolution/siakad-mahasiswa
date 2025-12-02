@props(['active' => false])

@php
    // LOGIKA WARNA BRANDING UNMARIS
    // Active State:
    // - Light: Background Brand Blue, Teks Putih (Sangat Resmi)
    // - Dark: Background Brand Blue, Teks Emas (Mewah)
    $activeClasses =
        'bg-brand-blue text-white shadow-md shadow-brand-blue/30 dark:bg-brand-blue dark:text-brand-gold dark:shadow-none';

    // Inactive State:
    // - Hover effect halus
    $inactiveClasses =
        'text-slate-600 hover:bg-slate-100 hover:text-brand-blue dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-brand-gold';

    $classes =
        $active ?? false
            ? 'group relative flex items-center gap-x-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-all duration-200 ' .
                $activeClasses
            : 'group relative flex items-center gap-x-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-all duration-200 ' .
                $inactiveClasses;

    // Icon Color
    // Saat aktif, icon ikut warna teks.
    // Saat tidak aktif, icon abu-abu.
    $iconColor =
        $active ?? false
            ? 'text-brand-gold' // Icon selalu emas saat aktif agar kontras dengan biru
            : 'text-slate-400 group-hover:text-brand-blue dark:group-hover:text-brand-gold transition-colors';

@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>

    <!-- Active Indicator (Garis Emas di Kiri) -->
    @if ($active)
        <div class="absolute left-0 h-full w-1 rounded-r bg-brand-gold"></div>
    @endif

    <!-- Icon -->
    <span class="{{ $iconColor }} flex h-6 w-6 shrink-0 items-center justify-center">
        {{ $slot }}
    </span>

    <!-- Label -->
    <span class="truncate">{{ $attributes->get('label') }}</span>
</a>
