<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="rManager">
    <meta name="author" content="Romwell">
    <meta name="keyword" content="Resin Manager">
    <link rel="shortcut icon" href="{{ asset('img/favicon.png') }}">

    <title>rManager</title>

    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/bootstrap-theme.css') }}" rel="stylesheet">
    <link href="{{ asset('css/elegant-icons-style.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/fullcalendar/fullcalendar/bootstrap-fullcalendar.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/fullcalendar/fullcalendar/fullcalendar.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/jquery-easy-pie-chart/jquery.easy-pie-chart.css') }}" rel="stylesheet" type="text/css" media="screen" />
    <link rel="stylesheet" href="{{ asset('css/owl.carousel.css') }}" type="text/css">
    <link href="{{ asset('css/jquery-jvectormap-1.2.2.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/fullcalendar.css') }}">
    <link href="{{ asset('css/widgets.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('css/jquery-ui-1.10.4.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/buttons.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/jquery.multiselect.css') }}" rel="stylesheet" />

    @yield('style')
</head>
<body>
<section id="container" class="">
    @include('layouts.header.header')
    @include('layouts.sidebar.sidebar')
    <section id="main-content">
        <div class="wrapper">
            @yield('content')
        </div>
    </section>
</section>

{{--<script src="{{ asset('js/jquery.js') }}"></script>--}}
<script src="{{ asset('js/jquery-3.7.0.js') }}"></script>
<script src="{{ asset('js/jquery-ui-1.10.4.min.js') }}"></script>
{{--<script src="{{ asset('js/jquery-1.8.3.min.js') }}"></script>--}}
{{--<script type="text/javascript" src="{{ asset('js/jquery-ui-1.9.2.custom.min.js') }}"></script>--}}
<!-- bootstrap -->
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<!-- nice scroll -->
<script src="{{ asset('js/jquery.scrollTo.min.js') }}"></script>
<script src="{{ asset('js/jquery.nicescroll.js') }}" type="text/javascript"></script>
<!-- charts scripts -->
<script src="{{ asset('assets/jquery-knob/js/jquery.knob.js') }}"></script>
<script src="{{ asset('js/jquery.sparkline.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/jquery-easy-pie-chart/jquery.easy-pie-chart.js') }}"></script>
<script src="{{ asset('js/owl.carousel.js') }}"></script>
<!-- jQuery full calendar -->
<script src="{{ asset('js/fullcalendar.min.js') }}"></script>
<!-- Full Google Calendar - Calendar -->
<script src="{{ asset('assets/fullcalendar/fullcalendar/fullcalendar.js') }}"></script>
<!--script for this page only-->
<script src="{{ asset('js/calendar-custom.js') }}"></script>
<script src="{{ asset('js/jquery.rateit.min.js') }}"></script>
<!-- custom select -->
<script src="{{ asset('js/jquery.customSelect.min.js') }}"></script>
<script src="{{ asset('assets/chart-master/Chart.js') }}"></script>

<!--custome script for all page-->
<script src="{{ asset('js/scripts.js') }}"></script>
<!-- custom script for this page-->
<script src="{{ asset('js/sparkline-chart.js') }}"></script>
<script src="{{ asset('js/easy-pie-chart.js') }}"></script>
<script src="{{ asset('js/jquery-jvectormap-1.2.2.min.js') }}"></script>
<script src="{{ asset('js/jquery-jvectormap-world-mill-en.js') }}"></script>
<script src="{{ asset('js/xcharts.min.js') }}"></script>
<script src="{{ asset('js/jquery.autosize.min.js') }}"></script>
<script src="{{ asset('js/jquery.placeholder.min.js') }}"></script>
<script src="{{ asset('js/gdp-data.js') }}"></script>
<script src="{{ asset('js/morris.min.js') }}"></script>
<script src="{{ asset('js/sparklines.js') }}"></script>
<script src="{{ asset('js/charts.js') }}"></script>
<script src="{{ asset('js/jquery.slimscroll.min.js') }}"></script>
<script src="{{ asset('js/jquery-sortable.js') }}"></script>

{{--dataTables--}}
<script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('js/jszip.min.js') }}"></script>
<script src="{{ asset('js/pdfmake.min.js') }}"></script>
<script src="{{ asset('js/vfs_fonts.js') }}"></script>
<script src="{{ asset('js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('js/buttons.print.min.js') }}"></script>
<script src="{{ asset('js/dataTables.select.min.js') }}"></script>
{{--<script src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>--}}
{{--<script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.colVis.min.js"></script>--}}

{{--select2--}}
<script src="{{ asset('js/select2.min.js') }}"></script>

{{--typeahead--}}
<script src="{{ asset('js/bootstrap3-typeahead.min.js') }}"></script>

{{--multiselect--}}
<script src="{{ asset('js/jquery.multiselect.js') }}"></script>

@yield('scripts')
<script>

    $(document).ready(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });


</script>


</body>

</html>