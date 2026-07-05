<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

@php
    $appTitle = config('app.name', 'EBA Information System');
    if ($appTitle === 'Laravel') {
        $appTitle = 'EBA Information System';
    }
@endphp

<title>
    {{ filled($title ?? null) ? $title.' - '.$appTitle : $appTitle }}
</title>

<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
<link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

@php
    $compiledCssPath = public_path('build/assets/app.css');
    $useTailwindCdnFallback = ! file_exists($compiledCssPath) || filesize($compiledCssPath) < 1000;
@endphp

@vite(['resources/css/app.css', 'resources/js/app.js'])

@if ($useTailwindCdnFallback)
    <script>
        window.tailwind = window.tailwind || {};
        window.tailwind.config = {
            darkMode: 'class',
        };
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
@endif

@fluxAppearance
