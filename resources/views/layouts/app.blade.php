<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#f8f5ee">
  <meta name="description" content="{{ __('Explore Afnan, Lattafa and the world of Arabic fragrance by the notes you love.') }}">
  <title>{{ $title ?? config('app.name') }}</title>
  <link rel="stylesheet" href="{{ asset('assets/sule-ui.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/shop.css') }}">
  <script src="{{ asset('assets/shop.js') }}" defer></script>
  @livewireStyles
</head>
<body>
  <a class="skip-link" href="#main">{{ __('Skip to content') }}</a>
  {{ $slot }}
  @livewireScripts
</body>
</html>
