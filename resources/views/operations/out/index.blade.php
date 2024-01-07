@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <h3 class="page-header"><i class="fa fa-file-text-o"></i> Rulaje Iesiri</h3>
            <ol class="breadcrumb">
                <li><i class="fa fa-home"></i><a href="{{ route('home') }}">Acasa</a></li>
                <li><i class="fa fa-arrow-circle-o-up"></i>Rulaje Iesiri</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <section class="panel panel-default">
                <header class="panel-heading">
                    <span class="panel-title">Filtre Iesiri</span>
                </header>
                <div class="panel panel-body" style="margin-bottom: 0; padding-bottom: 10px;">

                    <form class="form-horizontal" id="filters">
                        <div class="row">
                            <div class="col-sm-4">
                                <label for="year" style="font-weight: 600;">An</label>
                                <select class="form-control" name="year" id="year">
                                    <option value="0" disabled selected>--alege un an--</option>
                                    @foreach($years as $year)
                                        <option value="{{ Carbon\Carbon::parse($year->created_at)->format('Y') }}">{{ Carbon\Carbon::parse($year->created_at)->format('Y') }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-4">
                                <label for="month" style="font-weight: 600;">Luna</label>
                                <select class="form-control" name="month" id="month">
                                    <option value="0" disabled selected>--alege o luna--</option>
                                    <option value="01">ianuarie</option>
                                    <option value="02">februarie</option>
                                    <option value="03">martie</option>
                                    <option value="04">aprilie</option>
                                    <option value="05">mai</option>
                                    <option value="06">iunie</option>
                                    <option value="07">iulie</option>
                                    <option value="08">august</option>
                                    <option value="09">septembrie</option>
                                    <option value="10">octombrie</option>
                                    <option value="11">noiembrie</option>
                                    <option value="12">decembrie</option>
                                </select>
                            </div>
                            <div class="col-sm-4" style="margin-top: 5px;">
                                <br>
                                <input type="button" class="input-control" onclick="resetFilters()" value="Resetare Filtre" style="padding: 5px 5px 5px 5px;">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-1" style="margin-top: 14px;">
                                <input type="checkbox" class="input-control" value="1" id="zero" name="zero" style="transform: scale(1.5); margin-right: 2px;">
                                <label for="zero" style="font-size: 1.5em;">&theta;</label>
                            </div>
                            <div class="col-lg-1" style="margin-top: 20px;">
                                <input type="checkbox" class="input-control" value="1" id="ytd" name="ytd" style="transform: scale(1.5); margin-right: 2px;">
                                <label for="ytd" style="font-weight: 600;">YTD</label>
                            </div>
                            <div class="col-lg-1" style="margin-top: 20px;">
                                <input type="checkbox" class="input-control" value="1" id="mtd" name="mtd" style="transform: scale(1.5); margin-right: 2px;">
                                <label for="mtd" style="font-weight: 600;">MTD</label>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">Informatii Rulaje Iesiri</div>
                    <div class="panel-body" id="add-out-info">

                    </div>
                </div>
            </section>
        </div>
    </div>


@endsection

@section('scripts')

    <script type="text/javascript">
        showOutInfo();
    //=============================================================
        $('#filters').on('submit', function (e) {
            e.preventDefault();
            var data = $(this).serialize();
            var url = $(this).attr('action');
            $.post(url,data, function (data) {
                showOutInfo(data);
            });
            $(this).trigger('reset');
        });
    //=============================================================
        $('#year').on('change', function (e) {
            showOutInfo();
        });

        $('#month').on('change', function (e) {
            showOutInfo();
        });

        $('#ytd').on('change', function (e) {
            showOutInfo();
        });

        $('#mtd').on('change', function (e) {
            showOutInfo();
        });

        $('#zero').on('change', function (e) {
            showOutInfo();
        });
    //=============================================================
        function showOutInfo(data) {
            var data = $('#filters').serialize();
            $.get("{{ route('showOutInformation') }}", data, function (data) {
                $('#add-out-info').empty().append(data);

            })
        }
    //===========================================================
        function resetFilters() {
            document.getElementById("filters").reset();
            $('#year').val(0).trigger('change');
            $('#month').val(0).trigger('change');
            showOutInfo();
        }
    </script>
@endsection

<style>
    .form-inline {
        display: inline-block;
    }
    label {color: #606060;}

    ul#material li {
        display:inline;
        padding-right: 10px;
    }

    ul, ol {
        margin-top: 0;
        margin-bottom: 10px;
        padding-left: 0;
    }

    #materials {
        margin-right: 5px;
    }


</style>