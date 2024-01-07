@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <h3 class="page-header"><i class="fa fa-file-text-o"></i> Productie</h3>
            <ol class="breadcrumb">
                <li><i class="fa fa-home"></i><a href="{{ route('home') }}">Acasa</a></li>
                <li><i class="fa fa-database"></i>Productie</li>
                <li><i class="fa fa-info-circle"></i>Informatii & Rapoarte</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <section class="panel panel-default">
                <header class="panel-heading">
                    <span class="panel-title">Filtre Produse</span>
                </header>
                <div class="panel panel-body" style="margin-bottom: 0; padding-bottom: 10px;">
                    <!-- Custom Filter -->
                    <form class="form-horizontal" id="filters">
                        <div class="row">
                            <div class="col-sm-2" style="margin-bottom: 10px;">
                                <label for="from" style="font-weight: 600;">De la</label>
                                <div class="input-group">
                                    <input type="date" id="from" name="from" value="{{ Carbon\Carbon::today()->startOfMonth()->format('Y-m-d') }}" class="form-control" >
                                    <div class="input-group-addon">
                                        <span class="fa fa-calendar"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-2">
                                <label for="to" style="font-weight: 600;">Pana la</label>
                                <div class="input-group">
                                    <input type="date" id="to" value="{{ Carbon\Carbon::today()->endOfMonth()->format('Y-m-d') }}" name="to" class=" form-control" >
                                    <div class="input-group-addon">
                                        <span class="fa fa-calendar"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <label for="group_id" style="font-weight: 600;">Grup</label>
                                <select class="form-control" name="group_id[]" id="group_id" multiple >
                                    <option value="1">fara grup</option>
                                    @foreach($groups as $group)
                                        <option value="{{$group->id}}">{{$group->name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-sm-3">
                                <label for="worker_id" style="font-weight: 600;">Team Leader</label>
                                <select class="form-control" name="worker_id[]" id="worker_id" multiple >
                                    {{--<option class="0" value="0" disabled selected>--selecteaza un client--</option>--}}
                                    @foreach($team_leaders as $team_leader)
                                        <option value="{{$team_leader->worker['id']}}">{{$team_leader->worker['first']}} {{ $team_leader->worker['last'] }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-sm-2">
                                <label for="status" style="font-weight: 600;">Status</label>
                                <select class="form-control" name="status[]" id="status" multiple>
                                    {{--<option value="2" disabled selected>--alege un status--</option>--}}
                                    <option value="1">lansat</option>
                                    <option value="2">in lucru</option>
                                    <option value="4">finalizat</option>
                                    <option value="3">anulat</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <input type="button" class="input-control" onclick="resetFilters()" value="Resetare Filtre" style="height: 32px;">
                            </div>
                        </div>
                    </form>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">Informatii Produse</div>
                    <div class="panel-body table-responsive" id="add-report-info">
                        <table id="table-reports-info" class="table table-striped" style="width:100%">
                            <thead>
                            <tr>
                                <th></th>
                                <th style="padding: 0 !important;"></th>
                                <th style="padding-left: 10px; width: 25%;">Nume Produs</th>
                                <th style="padding-left: 10px; width: 15%;">Grup</th>
                                <th style="padding-left: 10px; width: 15%;">Echipa</th>
                                <th style="padding-left: 10px; width: 12%;">Creat la</th>
                                <th style="padding-left: 10px; width: 12%;">Preluat la</th>
                                <th style="padding-left: 10px;">Status</th>
                                <th style="padding-left: 10px;">Data Productie</th>
                                <th style="padding-left: 10px; width: 12%;">Finalizat la</th>
                                <th style="padding-left: 10px; width: 10%">Timp executie<br>(h:m:s)</th>
                                <th style="display: none; width: 0"></th>
                                <th style="padding-left: 10px; width: 1%;">Actiune</th>
                            </tr>
                            </thead>
                            <tbody class="main">

                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </div>

@endsection

@section('scripts')

    <script type="text/javascript">
        function format ( d ) {
			console.log(d);
            var json_data =  d.details;
            var op = '';
            var good = '';
            var count = 0;
            if(json_data === null ) {
                op = '<p style="padding-left: 60px; padding-top: 10px !important;">Nu s-a realizat nicio etapa pentru acest produs.</p>';
                return op;
            } else {
                $.each(json_data, function(key, value){
                    if (count < json_data[count].articles_materials_rows) {
                        good =  '';
                    } else {
                        good = '<i class="fa fa-exclamation" style="color: red;"></i>';
                    }

                    op +='<tr>' +
                        '<td>' + good + '</td>' +
                        '<td>' + ++key + '</td>' +
                        '<td>'+ json_data[count].team_leader.first + ' ' + json_data[count].team_leader.last +'</td>' +
                        '<td>' + json_data[count].material.name + '</td>' +
                        '<td>' + json_data[count].quantity + '</td>' +
                        '<td>' + json_data[count].material.unit + '</td>' +
                        '</tr>';
                    count++;
                });
                return '<table  cellpadding="5" cellspacing="0" border="0" style="padding-left:60px; border-collapse: separate; background-color: transparent;">\n' +
                    '<thead>\n' +
                    '<tr>\n' +
                    '<th></th>\n' +
                    '<th style="width: 40px; text-align: left; padding-top: 10px !important;">#</th>\n' +
                    '<th style="padding-top: 10px !important;">Team Leader</th>\n' +
                    '<th style="padding-top: 10px !important;">Material</th>\n' +
                    '<th style="width: 15%;padding-top: 10px !important;">Cantitate</th>\n' +
                    '<th style="width: 7%; padding-top: 10px !important;">U.M.</th>\n' +
                    ' </tr>\n' +
                    '</thead>\n' +
                    '<tbody>' +
                    op +
                    '</tbody></table>';
            }
        }

        $(document).ready(function() {
            var table = $('#table-reports-info').DataTable({
                dom: 'Blfrtip',
                "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                "pageLength": 100,
                "processing": true,
                "serverSide": true,
                "ajax":{
                    "url": "{{ route('ajaxdata.getdata') }}",
                    "dataType": "json",
                    "type": "POST",
                    "data": function (data) {
                        data.from = $('#from').val();
                        data.to = $('#to').val();
                        data.group_id = $('#group_id').val();
                        data.worker_id = $('#worker_id').val();
                        data.status = $('#status').val();
                        data._token = "{{csrf_token()}}";

                    }
                },
                "columns":[
                    {
                        "className":      'details-control',
                        "orderable":      false,
                        "data":           null,
                        "defaultContent": ''
                    },
                    { "data": "extra" },
                    { "data": "article_name" },
                    { "data": "group_name",
                        render: function (data) {
                            if (data === 'none') {
                                return '<span> - <span>'
                            }
                            else {
                                return data;
                            }
                        }
                    },
                    { "data": "workers_nr" },
                    { "data": "created_at" },
                    { "data": "taken_at" },
                    { "data": "status" },
                    { "data": "production_date" },
                    { "data": "finished_at" },
                    { "data": "exec_time" },
                    { "data": "details", "visible":false},
                    { "data": "action" },
                ],
                buttons: [
                    {
                        extend: 'copy',

                        exportOptions: {
                            columns: [2,3,4,5,6,7,8,9,10]
                        }
                    },
                    {
                        extend: 'excel',
                        exportOptions: {
                             columns: [2,3,4,5,6,7,8,9,10]
                         }
                    },
                    {
                        extend: 'pdf',
                        exportOptions: {
                            columns: [2,3,4,5,6,7,8,9,10]
                        }
                    }
                ]
            });
            $('#table-reports-info_filter input').addClass('form-control');

            $('#table-reports-info tbody').on('click', 'td.details-control', function () {
                var tr = $(this).closest('tr');
                var row = table.row( tr );

                if ( row.child.isShown() ) {
                    // This row is already open - close it
                    row.child.hide();
                    tr.removeClass('shown');
                }
                else {
                    // Open this row
                    row.child( format(row.data()) ).show();
                    tr.addClass('shown');
                }
            } );

            $('#from').on('change', function () {
                table.draw();
            });

            $('#to').on('change', function () {
                table.draw();
            });

            $('#group_id').on('change', function () {
                table.draw();
            });

            $('#worker_id').on('change', function () {
                table.draw();
            });

            $('#status').on('change', function () {
                table.draw();
            });
        });
        //-----------------------------------------------------
        $('#group_id').multiselect({
            columns: 1,
            placeholder: '--selecteaza un grup--',
            search: true,
            selectAll: true
        });

        $('#worker_id').multiselect({
            columns: 1,
            placeholder: '--selecteaza un Team Leader--',
            search: true,
            selectAll: true
        });

        $('#status').multiselect({
            columns: 1,
            placeholder: '--selecteaza un status--',
            search: true,
            selectAll: true
        });
        //-------------------------------------------------------
        $('tbody').delegate('.production_date', 'change', function () {
            var tr = $(this).parent().parent();
            var production_date = tr.find('.production_date').val();
            var product_id = tr.find('.production_date').attr("id");

            $.ajax({
                type: 'get',
                url: '{!! URL::route('productionDate') !!}',
                dataType: 'json',
                data: { 'production_date':production_date, 'product_id':product_id },
                success: function(data)
                {
                    //console.log(data);
                }
            });
        });
        //------------------------------------------------------------------
        function resetFilters() {
            document.getElementById("filters").reset();
            location.reload();
        }

    </script>
@endsection

<style>
    #ascrail2000 {
        top: 60px !important;
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

    td.details-control {
        background: url('../images/details_open.png') no-repeat center center;
        cursor: pointer;
    }
    tr.shown td.details-control {
        background: url('../images/details_close.png') no-repeat center center;
    }

    table.dataTable tbody th, table.dataTable tbody td {
        padding-top: 8px !important;
    }

    #noBorder {
        border: none !important;
        padding: 0 30px 0 0;
    }
	
	 table.dataTable tbody tr {
        background-color: transparent !important;
    }

    .table-striped>tbody>tr:nth-of-type(odd) {
        background-color: #f9f9f9 !important;
    }
</style>