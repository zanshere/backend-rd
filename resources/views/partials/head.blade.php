<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="user-id" content="{{ Auth::id() }}">
<meta name="pusher-key" content="{{ config('broadcasting.connections.pusher.key') }}">
<meta name="pusher-cluster" content="{{ config('broadcasting.connections.pusher.options.cluster') }}">

<title>{{ $title ?? config('app.name') }}</title>

<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

<!-- Dynamically set Vite URL -->
@php
    $viteUrl = config('app.env') === 'local' ? 'http://' . env('NETWORK_IP', '127.0.0.1') . ':5173' : '';
@endphp

@if (config('app.env') === 'local')
    <script type="module">
        window.VITE_APP_URL = '{{ $viteUrl }}';
    </script>
@endif

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance
