<!DOCTYPE html>
<html lang="en">

<head>
    <title>rManager</title>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="rManager">
    <meta name="author" content="Romwell">
    <meta name="keyword" content="Resin Manager">
    <link rel="shortcut icon" href="{{ asset('img/favicon.png') }}">
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/elegant-icons-style.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet" />
    <script src="{{ asset('js/jquery-3.7.0.js') }}"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <style>
        html,
        body {
            width: 770px;
            height: 480px;
            padding: 5px;
        }
    </style>

    @yield('style')
</head>

<body>

    <section id="raspberry-pi" class="">
        <section id="main-content">
            <div class="wrapper">
                @yield('content')
            </div>
        </section>
    </section>

    @yield('scripts')
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        });
    </script>
</body>

</html>