<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Creative - Bootstrap 3 Responsive Admin Template">
    <meta name="author" content="GeeksLabs">
    <meta name="keyword" content="Creative, Dashboard, Admin, Template, Theme, Bootstrap, Responsive, Retina, Minimal">
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
    <link href="{{ asset('css/style-responsive.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/xcharts.min.css') }}" rel=" stylesheet">
    <link href="{{ asset('css/jquery-ui-1.10.4.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/buttons.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/select2.min.css') }}" rel="stylesheet" />

    @yield('style')
</head>
<body>
<section id="container" class="">
    @include('layouts.header.header')
    @include('layouts.sidebar.sidebar')
    <section id="main-content">
        <section class="wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><i class="fa fa-laptop"></i> Panou</h3>
                    <ol class="breadcrumb">
                        <li><i class="fa fa-home"></i><a href="{{ route('home') }}">Acasa</a></li>
                        <li><i class="fa fa-laptop"></i>Panou</li>
                    </ol>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">Produse Lansate</div>
                        <div class="panel-body" style="margin-bottom: 0;">
						@if(count($newProducts) === 0)
							<p>Nu exista niciun produs lansat.</p>
						@else
                            <table class="table table-striped" style="margin-bottom: 0;">
                                <thead>
                                <tr>
                                    <th style="font-size: 12px; padding-left: 10px;">Nr. <br> crt.</th>
                                    <th style="padding-left: 10px;">Nume</th>
                                    <th style="padding-left: 10px;">Creat la</th>
                                    <th style="padding-left: 10px;">Pers.</th>
                                    <th style="padding-left: 10px;">Data Scadenta</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $i=0;
                                ?>
                                @foreach($newProducts as $key => $newProduct)
                                    <tr style="margin-top: 10px;">
                                        <td style="padding-left: 10px; vertical-align: middle;">{{ ++$i}}</td>
                                        <td style="padding-left: 10px; vertical-align: middle;">{{ $newProduct->article['name'] }}</td>
                                        <td style="padding-left: 10px; vertical-align: middle;">{{ Carbon\Carbon::parse($newProduct->created_at)->format('d.m.Y H:i:s') }}</td>
                                        <td style="padding-left: 10px; vertical-align: middle;">{{ $newProduct->article['workers_required'] }}</td>
                                        <td style="padding-left: 10px; vertical-align: middle;">{{  Carbon\Carbon::parse($newProduct->production_date)->format('d.m.Y') }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
						@endif
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">Produse in Lucru</div>
                        <div class="panel-body" style="margin-bottom: 0;">
						@if(count($processProducts) === 0)
							<p>Nu exista niciun produs in lucru.</p>
						@else
							<table class="table table-striped" style="margin-bottom: 0;">
                                <thead>
                                <tr>
                                    <th style="font-size: 12px; padding-left: 10px;">Nr. <br> crt.</th>
                                    <th style="padding-left: 10px;">Produs</th>
                                    <th style="padding-left: 10px;">Creat la</th>
                                    <th style="padding-left: 10px;">Pers.</th>
                                    <th style="padding-left: 10px;">Data Scadenta</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $i=0;
                                ?>
                                @foreach($processProducts as $key => $processProduct)
                                    <tr>
                                        <td style="padding-left: 10px; vertical-align: middle;">{{ ++$i}}</td>
                                        <td style="padding-left: 10px; vertical-align: middle;">{{ $processProduct->article['name'] }}</td>
                                        <td style="padding-left: 10px; vertical-align: middle;">{{ Carbon\Carbon::parse($processProduct->created_at)->format('d.m.Y H:i:s') }}</td>
                                        <td style="padding-left: 10px;">{{ $processProduct->article['workers_required'] }}</td>
                                        <td style="padding-left: 10px;">{{ Carbon\Carbon::parse($processProduct->production_date)->format('d.m.Y') }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>		
						@endif 
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">Consum Materiale in Luna Curenta (MTD)</div>
                        <div class="panel-body" style="margin-bottom: 0;">
                            <table class="table table-striped" style="margin-bottom: 0;">
                                <thead>
                                <tr>
                                    <th style="font-size: 12px; padding-left: 10px;">Nr. <br> crt.</th>
                                    <th style="padding-left: 10px;">Material</th>
                                    <th style="padding-left: 10px;">Nr. Pompe/U.M.</th>
                                    <th style="padding-left: 10px;">U.M.</th>
                                    <th style="padding-left: 10px;">Consum</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $i=0;
                                ?>
                                @foreach($monthOuts as $key => $monthOut)
                                    <tr style="margin-top: 10px;">
                                        <td style="padding-left: 10px; vertical-align: middle;">{{ ++$i}}</td>
                                        <td style="padding-left: 10px;">{{ $monthOut->name }}</td>
                                        <td style="padding-left: 10px;">{{ $monthOut->ratio }}</td>
                                        <td style="padding-left: 10px;">
                                            @if($monthOut->unit == 'm2')
                                                <span>m&sup2;</span>
                                            @else
                                                <span>{{ $monthOut->unit }}</span>
                                            @endif
                                        </td>
                                        <td style="padding-left: 10px;">{{ $monthOut->consum }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">Consum Materiale in Anul Curent (YTD)</div>
                        <div class="panel-body" style="margin-bottom: 0;">
                            <table class="table table-striped" style="margin-bottom: 0;">
                                <thead>
                                <tr>
                                    <th style="font-size: 12px; padding-left: 10px;">Nr. <br> crt.</th>
                                    <th style="padding-left: 10px;">Material</th>
                                    <th style="padding-left: 10px;">Nr. Pompe/U.M.</th>
                                    <th style="padding-left: 10px;">U.M.</th>
                                    <th style="padding-left: 10px;">Consum</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $i=0;
                                ?>
                                @foreach($yearOuts as $key => $yearOut)
                                    <tr style="margin-top: 10px;">
                                        <td style="padding-left: 10px; vertical-align: middle;">{{ ++$i}}</td>
                                        <td style="padding-left: 10px;">{{ $yearOut->name }}</td>
                                        <td style="padding-left: 10px;">{{ $yearOut->ratio }}</td>
                                        <td style="padding-left: 10px;">
                                            @if($yearOut->unit == 'm2')
                                                <span>m&sup2;</span>
                                            @else
                                                <span>{{ $yearOut->unit }}</span>
                                            @endif
                                        </td>
                                        <td style="padding-left: 10px;">{{ $yearOut->consum }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </section>
</section>

<script src="{{ asset('js/jquery.js') }}"></script>
<script src="{{ asset('js/jquery-ui-1.10.4.min.js') }}"></script>
{{--<script src="{{ asset('js/jquery-1.8.3.min.js') }}"></script>--}}
<script type="text/javascript" src="{{ asset('js/jquery-ui-1.9.2.custom.min.js') }}"></script>
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

{{--dataTables--}}
<script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('js/jszip.min.js') }}"></script>
<script src="{{ asset('js/pdfmake.min.js') }}"></script>
<script src="{{ asset('js/vfs_fonts.js') }}"></script>
<script src="{{ asset('js/buttons.html5.min.js') }}"></script>

{{--select2--}}
<script src="{{ asset('js/select2.min.js') }}"></script>

{{--typeahead--}}
<script src="{{ asset('https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.1/bootstrap3-typeahead.min.js') }}"></script>

@yield('scripts')
<script>

    $(document).ready(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });
    //=========================================================
    $('#toggle').click(function() {
        $('#map').toggle('500');
        $("i", this).toggleClass("fa-chevron-up fa-chevron-down");
    });
</script>
<style>



</style>
</body>

</html>