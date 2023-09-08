<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Tic-Tac-Toe</title>

    </head>
    <body class="tic-tac-toe">

        <div id="game"></div>

        @viteReactRefresh
        @vite(['resources/scss/app.scss', 'resources/scss/tic-tac-toe.scss', 'resources/js/app.js'])

    </body>
</html>
