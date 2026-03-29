<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Koperasi') }}</title>
    <meta name="description" content="Aplikasi koperasi untuk kelola anggota, simpanan, pinjaman, angsuran, kas, dan laporan.">

    <link rel="icon" type="image/png" href="{{ asset('logobrico.png') }}">

    <meta property="og:title" content="{{ config('company.name', config('app.name', 'BRI Cooperative')) }}" />
    <meta property="og:description" content="Sistem manajemen koperasi (BRI Cooperative): anggota, simpanan, pinjaman, kas, dan laporan." />
    <meta property="og:image" content="{{ asset('logobrico.png') }}" />
    <meta property="og:url" content="{{ url()->current() }}" />

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-b from-emerald-50 via-white to-white text-slate-900">
    <div class="pointer-events-none absolute inset-0 overflow-hidden">
        <div class="absolute -top-24 left-1/2 h-72 w-[42rem] -translate-x-1/2 rounded-full bg-emerald-200/40 blur-3xl"></div>
        <div class="absolute -bottom-24 left-1/3 h-72 w-[42rem] -translate-x-1/2 rounded-full bg-teal-200/40 blur-3xl"></div>
    </div>

    <div class="relative mx-auto max-w-6xl px-6 py-10">
        <header class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                @if (file_exists(public_path(config('company.logo', 'logobrico.png'))))
                    <img src="{{ asset(config('company.logo', 'logobrico.png')) }}" alt="{{ config('company.name', config('app.name', 'Bri Cooperative')) }}" class="h-10 w-auto" />
                @else
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-600 text-sm font-bold text-white">
                        {{ str(config('app.name', 'Koperasi'))->substr(0, 1)->upper() }}
                    </div>
                @endif

                <div class="leading-tight">
                    <p class="text-sm font-semibold text-slate-900">{{ config('app.name', 'Koperasi') }}</p>
                    <p class="text-xs text-slate-600">Sistem manajemen koperasi berbasis web</p>
                </div>
            </div>

            <nav class="flex flex-wrap items-center gap-2">
                <a href="#fitur" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100">Fitur</a>
                <a href="#modul" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100">Modul</a>
                <a href="#mulai" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100">Mulai</a>
            </nav>
        </header>

        <main class="mt-10">
            <section class="grid items-center gap-10 lg:grid-cols-2">
                <div>
                    <div class="inline-flex items-center gap-2 rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-800 ring-1 ring-emerald-200">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-600"></span>
                        Mempermudah Monitoring <span class="font-mono">Koperasi Anda</span>
                    </div>

                    <h1 class="mt-4 text-3xl font-bold tracking-tight text-slate-900 sm:text-5xl">
                        Kelola koperasi lebih cepat, rapi, dan terukur.
                    </h1>
                    <p class="mt-4 max-w-xl text-base text-slate-700">
                        Manajemen anggota, simpanan, pinjaman, angsuran, kas, dan laporan — dalam satu panel yang aman dengan kontrol akses per role.
                    </p>

                    <div id="mulai" class="mt-6 flex flex-col gap-3 sm:flex-row">
                        <a href="{{ url('/admin') }}"
                           class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700">
                            Buka Panel Admin
                        </a>
                        <a href="{{ url('/admin/login') }}"
                           class="inline-flex items-center justify-center rounded-xl bg-white px-5 py-3 text-sm font-semibold text-emerald-700 ring-1 ring-emerald-200 hover:bg-emerald-50">
                            Login
                        </a>
                    </div>

                    <dl class="mt-8 grid grid-cols-2 gap-4 sm:grid-cols-3">
                        <div class="rounded-2xl bg-white/80 p-4 ring-1 ring-slate-200">
                            <dt class="text-xs font-semibold text-slate-500">Akses</dt>
                            <dd class="mt-1 text-sm font-semibold text-slate-900">Role-based</dd>
                        </div>
                        <div class="rounded-2xl bg-white/80 p-4 ring-1 ring-slate-200">
                            <dt class="text-xs font-semibold text-slate-500">Data</dt>
                            <dd class="mt-1 text-sm font-semibold text-slate-900">Terstruktur</dd>
                        </div>
                        <div class="rounded-2xl bg-white/80 p-4 ring-1 ring-slate-200">
                            <dt class="text-xs font-semibold text-slate-500">Laporan</dt>
                            <dd class="mt-1 text-sm font-semibold text-slate-900">Kas & Laba Rugi</dd>
                        </div>
                    </dl>
                </div>

                <div class="rounded-3xl bg-white/80 p-6 ring-1 ring-slate-200 shadow-sm">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-semibold text-slate-900">Ringkasan Modul</p>
                        <p class="text-xs text-slate-500">Aman • Cepat • Terintegrasi</p>
                    </div>

                    <div class="mt-4 grid gap-3 sm:grid-cols-2">
                        <div class="rounded-2xl border border-slate-200 bg-white p-4">
                            <p class="text-sm font-semibold text-slate-900">Anggota</p>
                            <p class="mt-1 text-sm text-slate-600">Profil, status aktif, histori pinjaman & simpanan.</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-white p-4">
                            <p class="text-sm font-semibold text-slate-900">Simpanan</p>
                            <p class="mt-1 text-sm text-slate-600">Pencatatan setoran dan rekap simpanan.</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-white p-4">
                            <p class="text-sm font-semibold text-slate-900">Pinjaman</p>
                            <p class="mt-1 text-sm text-slate-600">Pengajuan, approve/tolak, pencairan, status.</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-white p-4">
                            <p class="text-sm font-semibold text-slate-900">Angsuran</p>
                            <p class="mt-1 text-sm text-slate-600">Jadwal angsuran + tombol bayar per angsuran.</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-white p-4 sm:col-span-2">
                            <p class="text-sm font-semibold text-slate-900">Kas & Laporan</p>
                            <p class="mt-1 text-sm text-slate-600">Transaksi kas, kategori, dan laporan laba rugi.</p>
                        </div>
                    </div>
                </div>
            </section>

            <section id="fitur" class="mt-14">
                <div class="rounded-3xl bg-white/80 p-8 ring-1 ring-slate-200">
                    <h2 class="text-xl font-bold text-slate-900">Fitur Utama</h2>
                    <p class="mt-2 max-w-2xl text-sm text-slate-600">Fokus ke alur kerja koperasi: input data cepat, kontrol akses jelas, dan status pinjaman terbaca.</p>

                    <div class="mt-6 grid gap-4 md:grid-cols-3">
                        <div class="rounded-2xl border border-slate-200 bg-white p-5">
                            <p class="text-sm font-semibold text-slate-900">Approval Pinjaman</p>
                            <p class="mt-1 text-sm text-slate-600">Approve/tolak dari list maupun detail, lengkap catatan penolakan.</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-white p-5">
                            <p class="text-sm font-semibold text-slate-900">Angsuran Terjadwal</p>
                            <p class="mt-1 text-sm text-slate-600">Otomatis buat jadwal, bayar per angsuran, dan update status pinjaman.</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-white p-5">
                            <p class="text-sm font-semibold text-slate-900">Filter Status Kredit</p>
                            <p class="mt-1 text-sm text-slate-600">Pantau anggota macet, belum bayar, dan lunas dari menu anggota.</p>
                        </div>
                    </div>
                </div>
            </section>

            <section id="modul" class="mt-6">
                <div class="rounded-3xl bg-white/80 p-8 ring-1 ring-slate-200">
                    <h2 class="text-xl font-bold text-slate-900">Modul Aplikasi</h2>
                    <div class="mt-6 grid gap-4 md:grid-cols-2">
                        <div class="rounded-2xl border border-slate-200 bg-white p-5">
                            <p class="text-sm font-semibold text-slate-900">Koperasi</p>
                            <ul class="mt-3 space-y-2 text-sm text-slate-700">
                                <li class="flex gap-2"><span class="mt-1 h-1.5 w-1.5 rounded-full bg-emerald-600"></span>Anggota</li>
                                <li class="flex gap-2"><span class="mt-1 h-1.5 w-1.5 rounded-full bg-emerald-600"></span>Simpanan</li>
                                <li class="flex gap-2"><span class="mt-1 h-1.5 w-1.5 rounded-full bg-emerald-600"></span>Pinjaman & Angsuran</li>
                                <li class="flex gap-2"><span class="mt-1 h-1.5 w-1.5 rounded-full bg-emerald-600"></span>Setting Bunga</li>
                            </ul>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-white p-5">
                            <p class="text-sm font-semibold text-slate-900">Kas</p>
                            <ul class="mt-3 space-y-2 text-sm text-slate-700">
                                <li class="flex gap-2"><span class="mt-1 h-1.5 w-1.5 rounded-full bg-emerald-600"></span>Kategori Kas</li>
                                <li class="flex gap-2"><span class="mt-1 h-1.5 w-1.5 rounded-full bg-emerald-600"></span>Transaksi Kas</li>
                                <li class="flex gap-2"><span class="mt-1 h-1.5 w-1.5 rounded-full bg-emerald-600"></span>Laporan Laba Rugi</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <footer class="mt-10 border-t border-slate-200 pt-6 text-sm text-slate-600">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <p>&copy; {{ date('Y') }} {{ config('app.name', 'Koperasi') }}</p>
                <p class="text-xs">Developer : <a class="font-mono text-emerald-700 hover:underline" href="https://wa.me/628224442890" target="_blank">Dibscode Software House</a></p>
            </div>
        </footer>
    </div>
</body>
</html>
