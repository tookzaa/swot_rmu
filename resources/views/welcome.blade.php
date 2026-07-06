<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>

    @vite(['resources/css/app.scss', 'resources/js/app.js'])
</head>
<body>
    <div class="container py-5">
        <h1>{{ config('app.name', 'Laravel') }}</h1>
        <p class="lead">Laravel 12 + Bootstrap 5.3</p>
        <button class="btn btn-primary">Bootstrap is ready</button>
    </div>
</body>
</html>
