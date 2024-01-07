@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <ol class="breadcrumb">
                <li><i class="fa fa-home"></i><a href="{{ route('home') }}">Acasa</a></li>
                <li><i class="fa fa-file-text-o"></i>Raportare Zilnica</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <section class="panel panel-default">
                <header class="panel-heading">
                    <span class="panel-title">Filtre Raportare Zilnica</span>
                </header>
                <div class="panel panel-body" style="margin-bottom: 0; padding-bottom: 10px;">
                    <!-- Custom Filter -->
                    <form class="form-horizontal" id="filters">
                        <div class="row">
                            <div class="col-sm-3" style="margin-bottom: 10px;">
                                <label for="from" style="font-weight: 600;">De la</label>
                                <div class="input-group">
                                    <input type="date" id="from" name="from" class="form-control" >
                                    <div class="input-group-addon">
                                        <span class="fa fa-calendar"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <label for="to" style="font-weight: 600;">Pana la</label>
                                <div class="input-group">
                                    <input type="date" id="to" name="to" class="form-control" >
                                    <div class="input-group-addon">
                                        <span class="fa fa-calendar"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <label for="worker_id" style="font-weight: 600;">Operator</label>
                                <select class="form-control" name="worker_id[]" id="worker_id" multiple >
                                    @foreach($team_leaders as $team_leader)
                                        <option value="{{ $team_leader->id }}">{{ $team_leader->first }} {{ $team_leader->last }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-sm-3">
                                <label for="client_id" style="font-weight: 600;">Client</label>
                                <select class="form-control" name="client_id[]" id="client_id" multiple >
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}">{{ $client->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="row">
                                <div class="input-group">
                                    <buton id="filter" name="filter" class="btn btn-success"  style="font-weight: 500; margin-top:15px; width: 120px; height: 33px; margin-right: 15px;"><i class="fa fa-check"></i> Aplica filtrele</buton>
                                    <button id="refresh" name="refresh" class="btn btn-danger" style="font-weight: 500; margin-top:15px; width: 120px; height: 33px;"><i class="fa fa-trash-o"></i> Sterge filtrele</button>
                                </div>
                            </div>
                        </div>
                        {{ csrf_field() }}
                    </form>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">Informatii Raportare Zilnica</div>
                    <div class="panel-body table-responsive" id="add-report-info">
                        <table id="table-daily-reports-info" class="table table-striped" style="width:100%">
                            <thead>
                            <tr>
                                <th style="padding-left: 10px; width: 25%;">Nume Operator</th>
                                <th style="padding-left: 10px; width: 15%;">Data (an-luna-zi)</th>
                                <th style="padding-left: 10px; width: 15%;">Total ore</th>
                                <th style="padding-left: 10px; width: 45%;">Detalii</th>
                            </tr>
                            </thead>
                            <tbody>

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

        $('#worker_id').multiselect({
        columns: 1,
        placeholder: '--selecteaza un Team Leader--',
        search: true,
        selectAll: true
        });

        $('#client_id').multiselect({
            columns: 1,
            placeholder: '--selecteaza un client--',
            search: true,
            selectAll: true
        });
    //-----------------------------------------------------------------
        $('#filter').on ('click', function(){

            $('#table-daily-reports-info').dataTable().fnDestroy();

            var from = $('#from').val();
            var to = $('#to').val();

            if(from !== '' &&  to !== '' && $('#worker_id').get(0).selectedIndex !== -1 && $('#client_id').get(0).selectedIndex !== -1)
            {
                var output = '';
                $.ajax({
                    type: "POST",
                    data: $('#filters').serialize(),
                    dataType: 'json',
                    url: "{{ route('ajaxdata.getdataDailyReports') }}",
                    "dataSrc": "tableData",
                    success: function(data) {
                        //console.log(data);

                        $.each(data.days, function (i, day) {
                            var j;

                            for (j = 0; j < data.workers.length; j++) {

                                var dayTotalHours = '';
                                var productFinishedAt = '';
                                var show = 'false';
                                var select = '<select class="form-control" id="products">';

                                var countDays = data.days.length;
                                var countDaysProducts = data.workers[j].products.length;

                                if (countDaysProducts > 0) {

                                    if (countDays === countDaysProducts) {
                                        dayTotalHours = data.workers[j].products[i].day_total_hours;
                                        productFinishedAt = data.workers[j].products[i].product_finished_at;

                                        $.each(data.workers[j].products[i].products, function (key, product) {
                                            select += '<option value="' + product.product_id + '">' + product.product_name + ' / ' + product.total_product_quantity + ' L / ' + product.workers_nr +' lucratori</option>';

                                        });


                                        show = 'true';
                                    } else {
                                        var k;

                                        for (k = 0; k < countDaysProducts; k++) {
                                            if (data.workers[j].products[k].product_finished_at === day.product_finished_at) {
                                                dayTotalHours = data.workers[j].products[k].day_total_hours;
                                                productFinishedAt = data.workers[j].products[k].product_finished_at;

                                                $.each(data.workers[j].products[k].products, function (key1, product1) {
                                                    select += '<option value="' + product1.product_id + '">' + product1.product_name + ' / ' + product1.total_product_quantity + ' L / ' + product1.workers_nr + ' lucratori</option>';

                                                });

                                                show = 'true';
                                            }
                                        }
                                    }
                                    select += '</select>';

                                    if (show === 'true') {
                                        output += '<tr>\n' +
                                            '<td>\n' +
                                                data.workers[j].last + " " + data.workers[j].first +
                                            '</td>\n' +
                                            '<td>\n' +
                                                productFinishedAt +
                                            '</td>\n' +
                                            '<td>' +
                                                dayTotalHours +
                                            '</td>' +
                                            '<td>\n' +
                                                 select +
                                            '</td>\n' +
                                            '</tr>';

                                        $('#products').empty();
                                    }
                                }
                            }
                        });
                        $("tbody").empty().append(output);


                       $('#table-daily-reports-info').DataTable({
                           dom: 'Blfrtip',
                           "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                           "pageLength": 100,
                           retrieve: true,
                           buttons: [
                               {
                                   extend: 'excel',
                                   exportOptions: {
                                       columns: [0,1,2,3]
                                   }
                               },
                               {
                                   extend: 'pdf',
                                   exportOptions: {
                                       columns: [0,1,2,3]
                                   }
                               },
                           ],
                           "bDestroy": true, //use for reinitialize datatable
                        });

                    }
                });
            }
            else
            {
                alert('Toate campurile sunt obligatorii.');
            }
        });

        $('#refresh').click(function(){
            document.getElementById("filters").reset();
            location.reload();
        });

    </script>

@endsection
