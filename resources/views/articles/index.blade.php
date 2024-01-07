@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <h3 class="page-header"><i class="fa fa-file-text-o"></i> Articole</h3>
            <ol class="breadcrumb">
                <li><i class="fa fa-home"></i><a href="{{ route('home') }}">Acasa</a></li>
                <li><i class="icon_documents_alt"></i>Articole</li>
                <li><i class="fa fa-info-circle"></i>Informatii & Rapoarte</li>

            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <section class="panel panel-default">
                <header class="panel-heading">
                    <span class="panel-title">Filtre Articole</span>
                </header>
                <div class="panel panel-body" style="margin-bottom: 0; padding-bottom: 10px;">

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

                            <div class="col-sm-2" style="margin-bottom: 10px;">
                                <label for="to" style="font-weight: 600;">Pana la</label>
                                <div class="input-group">
                                    <input type="date" id="to" value="{{ Carbon\Carbon::today()->endOfMonth()->format('Y-m-d') }}" name="to" class=" form-control" >
                                    <div class="input-group-addon">
                                        <span class="fa fa-calendar"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-2">
                                <label for="client_id" style="font-weight: 600;">Client</label>
                                <select class="form-control" name="client_id[]" id="client_id" multiple >
                                    {{--<option class="0" value="0" disabled selected>--selecteaza un client--</option>--}}
                                    @foreach($clients as $client)
                                        <option value="{{$client->id}}">{{$client->name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-sm-2" style="margin-bottom: 10px;">
                                <label for="category_id" style="font-weight: 600;">Categorie Articol</label>
                                <select class="form-control" name="category_id[]" id="category_id" multiple>
                                    {{--<option class="0" value="0" disabled selected>--alege o categorie--</option>--}}
                                    @foreach($article_categories as $article_category)
                                        <option value="{{$article_category->id}}">{{$article_category->name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-sm-2">
                                <label for="materials" style="font-weight: 600;">Materiale</label>
                                <select class="form-control" name="materials[]" id="materials" multiple>
                                    @foreach($materials as $material)
                                        <option value="{{$material->id}}">{{$material->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-2">
                                <label for="status" style="font-weight: 600;">Status</label>
                                <select class="form-control" name="status[]" id="status" multiple>
                                    <option value="1">activ</option>
                                    <option value="0">inactiv</option>
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
                    <div class="panel-heading">Informatii Articole</div>
					<div class="modal" style="display: none">
                        <div class="center">
                            <img alt="" src="{{ asset('images/loader.gif') }}" /><br>
                            <span style="color: black; font-size: 18px;">Asteptati...</span>
                        </div>
                    </div>
                    <div class="panel-body" id="add-article-info">
                        <table class="table table-striped" id="table-articles-info" style="width:100%">
                            <thead>
                            <tr>
                                <th style="font-size: 12px; padding-left: 10px; width: 20px;"></th>
                                <th style="padding-left: 10px;">Nume Articol</th>
                                <th style="padding-left: 10px;">Categorie Articol</th>
                                <th style="padding-left: 10px;">Client</th>
                                <th style="padding-left: 10px;">Status</th>
                                <th style="padding-left: 10px;">Creat la</th>
                                <th style="display: none;"></th>
                                <th style="padding-left: 10px;">Actiune</th>
                                <th style="width: 0 !important; padding: 0 !important; margin: 0 !important;"></th>
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
            // `d` is the original data object for the row
            var json_data =  d.details;
            var op = '';
            var check_extra = '';
            var count = 0;
            $.each(json_data, function(key, value){
                if(json_data[count].extra === 0) {
                    check_extra = '<input type="checkbox" class="input-control extra" value="0" id="extra" name="extra[]" style="height:20px; width:20px;" disabled>';
                } else {
                    check_extra = '<input type="checkbox" class="input-control extra" value="1" checked id="extra" name="extra[]" style="height:20px; width:20px;" disabled>';
                }
                op +=   '<tr>'+
                        '<td style="text-align: center; padding-top: 10px !important;">' + ++key + '</td>' +
                        '<td style="padding-top: 10px !important;">' + json_data[count].material.name + '</td>' +
                        '<td style="padding-top: 10px !important;">' + json_data[count].quantity + '</td>' +
                        '<td style="padding-top: 10px !important;">' + json_data[count].process.name + '</td>' +
                        '<td style="padding-top: 10px !important; padding-left: 10px;">' + check_extra + '</td>' +
                        '<td style="padding-top: 10px !important;">' + json_data[count].material.unit + '</td>' +
                        '</tr>';
                count++;
            });
            return '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px; border-collapse: separate; background-color: transparent;">\n' +
                '<thead>\n' +
                '<tr>\n' +
                '<th style="width: 40px; text-align: center; padding-top: 10px !important;">#</th>\n' +
                '<th style="padding-top: 10px !important;">Material</th>\n' +
                '<th style="width: 15%;padding-top: 10px !important;">Cantitate</th>\n' +
                '<th style="padding-top: 10px !important;">Proces</th>\n' +
                '<th style="width: 70px;padding-top: 10px !important;">Extra</th>\n' +
                '<th style="width: 7%; padding-top: 10px !important;">U.M.</th>\n' +
                '</tr>\n' +
                '</thead>\n' +
                '<tbody>'+
                 op +
                '</tbody></table>';
        }
        $(document).ready(function () {
            var table = $('#table-articles-info').DataTable({

                dom: 'Blfrtip',
                "lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]],
                "pageLength": 100,
                "processing": true,
                "serverSide": true,
                "ajax":{
                    "url": "{{ route('ajaxdata.getdataArticles') }}",
                    "dataType": "json",
                    "type": "POST",
                    "data": function (data) {
                        data.from = $('#from').val();
                        data.to = $('#to').val();
                        data.client_id = $('#client_id').val();
                        data.category_id = $('#category_id').val();
                        data.materials = $('#materials').val();
                        data.status = $('#status').val();
                        data._token = "{{csrf_token()}}";
                    }
                },
                "columns": [
                    {
                        "className":      'details-control',
                        "orderable":      false,
                        "data":           null,
                        "defaultContent": ''
                    },
                    { "data": "article_name" },
                    { "data": "article_category" },
                    { "data": "client" },
                    { "data": "status" },
                    { "data": "created_at" },
                    { "data": "details", "visible":false},
                    { "data": "action" },
                    { "data": "article_id" }
                ],
                buttons: [
                    {
                        extend: 'copy',

                        exportOptions: {
                            columns: [1,2,3,4,5]
                        }
                    },
                    {
                        extend: 'excel',
                        exportOptions: {
                            columns: [1,2,3,4,5]
                        }
                    },
                    {
                        extend: 'pdf',
                        exportOptions: {
                            columns: [1,2,3,4,5]
                        }
                    }
                ]
            } );
            $('#table-articles-info_filter input').addClass('form-control');

            $('#table-articles-info tbody').on('click', 'td.details-control', function () {
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

            $('#client_id').on('change', function () {
                table.draw();
            });

            $('#category_id').on('change', function () {
                table.draw();
            });

            $('#materials').on('change', function () {
                table.draw();
            });

            $('#status').on('change', function () {
                table.draw();
            });
        });
    //===============================================================
        function resetFilters() {
            document.getElementById("filters").reset();
            location.reload();
        }
    //==============================================================
        $('#client_id').multiselect({
            columns: 1,
            placeholder: '--selecteaza un client--',
            search: true,
            selectAll: true
        });

        $('#category_id').multiselect({
            columns: 1,
            placeholder: '--selecteaza o categorie--',
            search: true,
            selectAll: true
        });

        $('#materials').multiselect({
            columns: 1,
            placeholder: '--selecteaza un material--',
            search: true,
            selectAll: true
        });

        $('#status').multiselect({
            columns: 1,
            placeholder: '--selecteaza un status--',
            search: true,
            selectAll: true
        });
    //==============================================================
        $('.main').on('click', '.inactive_btn', function () {
            var tr = $(this).parent().parent();
            var id = tr.find('.v_id').val();
            $.ajax({
                type: 'post',
                url: '{!! URL::route('changeArticleActiveStatus') !!}',
                dataType: 'json',
                data: { 'id':id },
                success: function(data)
                {

                }
            });

            tr.find('.active_status').hide();
            tr.find('.inactive_btn').hide();
            tr.find('.inactive_status').show();
            tr.find('.active_btn').show();
            //alert(id);
        });

        $('.main').on('click', '.active_btn', function () {
            //alert('pressed active button');
            var tr = $(this).parent().parent();
            var id = tr.find('.v_id').val();
            $.ajax({
                type: 'post',
                url: '{!! URL::route('changeArticleInactiveStatus') !!}',
                dataType: 'json',
                data: { 'id':id },
                success: function(data)
                {

                }
            });

            tr.find('.inactive_status').hide();
            tr.find('.active_btn').hide();
            tr.find('.active_status').show();
            tr.find('.inactive_btn').show();

        });

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