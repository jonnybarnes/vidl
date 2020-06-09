<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name') }}</title>
        <link href="/css/app.css" rel="stylesheet">
    </head>
    <body>
        <div id="app">
            <h1>{{ config('app.name') }}</h1>
            <p>@lang('homepage.youtubedl-support')</p>
            <media-download tenor-api-key="{{ config('tenor.api-key') }}"></media-download>
        </div>
    </body>
    <script src="/js/app.js"></script>
</html>
