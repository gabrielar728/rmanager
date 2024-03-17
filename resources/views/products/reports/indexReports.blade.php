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
                                <input type="date" id="from" name="from"
                                       value="{{ Carbon\Carbon::today()->startOfMonth()->format('Y-m-d') }}"
                                       class="form-control">

                            </div>

                            <div class="col-sm-2">
                                <label for="to" style="font-weight: 600;">Pana la</label>
                                <input type="date" id="to"
                                       value="{{ Carbon\Carbon::today()->endOfMonth()->format('Y-m-d') }}" name="to"
                                       class=" form-control">
                            </div>

                            <div class="col-sm-2">
                                <label for="group_id" style="font-weight: 600;">Grup</label>
                                <select class="form-control" name="group_id[]" id="group_id" multiple>
                                    <option value="1">fara grup</option>
                                    @foreach($groups as $group)
                                        <option value="{{$group->id}}">{{$group->name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-sm-2">
                                <label for="worker_id" style="font-weight: 600;">Team Leader</label>
                                <select class="form-control" name="worker_id[]" id="worker_id" multiple>
                                    {{--<option class="0" value="0" disabled selected>--alege un client--</option>--}}
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

                            <div class="col-sm-2">
                                <label for="pump" style="font-weight: 600;">Pompa</label>
                                <select class="form-control" name="pump" id="pump">
                                    <option class="0" value="0" disabled selected>--alege o pompa--</option>
                                    @foreach($pumps as $pump)
                                        <option value="{{$pump->id}}">{{$pump->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <input type="button" class="input-control" onclick="resetFilters()"
                                       value="Resetare Filtre" style="height: 32px;">
                            </div>
                        </div>
                    </form>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">Informatii Produse</div>
                    <div class="panel-body table-responsive" id="add-report-info">
                        <table id="table-reports-info" class="table" style="width:100%">
                            <thead>
                            <tr>
                                <th></th>
                                <th style="padding-left: 10px;">Produs</th>
                                <th style="padding-left: 10px;">Nume Flowopt</th>
                                <th style="padding-left: 10px;">Numar Serie</th>
                                <th style="padding-left: 10px;">Numar Comanda</th>
                                <th style="padding-left: 10px;">Grup</th>
                                {{--                                <th style="padding-left: 10px">Echipa</th>--}}
                                <th style="padding-left: 10px;">Saptamana lansare</th>
                                <th style="padding-left: 10px;">Preluat la</th>
                                <th style="padding-left: 10px;">Finalizat la</th>
                                <th style="padding-left: 10px;">Status</th>
                                <th style="padding-left: 10px">Timp executie</th>
                                <th style="padding-left: 10px">Total rasina (L)</th>
                                <th style="padding-left: 10px; width: 1%;">Actiune</th>
                                <th>Details</th>
                            </tr>
                            </thead>
                            {{--                            <tbody class="main">--}}

                            {{--                            </tbody>--}}
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </div>

@endsection

@section('scripts')

    <script type="text/javascript">

        function format(d) {
            // console.log(d);
            const json_data = d.details;
            let op = '';
            let good = '';
            let count = 1;

            // console.log(d);
            if (json_data.length === 0) {
                op = '<p style="padding-left: 60px; padding-top: 10px !important;">Nu s-a realizat nicio etapa pentru acest produs.</p>';
                return op;
            } else {
                $.each(json_data, function (key, value) {
                    if (key < value.articles_materials_rows) {
                        good = '';
                    } else {
                        good = '<i class="fa fa-exclamation" style="color: red;"></i>';
                    }

                    op += '<tr>' +
                        '<td>' + good + '</td>' +
                        '<td>' + count + '</td>' +
                        '<td>' + value.team_leader.first + ' ' + value.team_leader.last + '</td>' +
                        '<td>' + value.material.name + '</td>' +
                        '<td>' + value.material.unit + '</td>' +
                        '<td>' + value.pump.name + '</td>' +
                        '<td>' + value.quantity + '</td>' +
                        '</tr>';
                    count++;
                });
                op += '<tr>' +
                    '<td colspan="6" style="text-align: right;"><b>TOTAL: </td>' +
                    '<td style="text-align: left;"><b>' + d.total_resin + '</b></td>' +
                    ' </tr>';
                return '<table  cellpadding="5" cellspacing="0" border="0" style="padding-left:60px; border-collapse: separate; background-color: transparent;">\n' +
                    '<thead>\n' +
                    '<tr>\n' +
                    '<th></th>\n' +
                    '<th style="width: 40px; text-align: left; padding-top: 10px !important;">#</th>\n' +
                    '<th style="padding-top: 10px !important;">Team Leader</th>\n' +
                    '<th style="padding-top: 10px !important;">Material</th>\n' +
                    '<th style="width: 7%; padding-top: 10px !important;">U.M.</th>\n' +
                    '<th style="padding-top: 10px !important;">Pompa</th>\n' +
                    '<th style="width: 15%;padding-top: 10px !important;">Cantitate</th>\n' +
                    ' </tr>\n' +
                    '</thead>\n' +
                    '<tbody>' +
                    op +
                    '</tbody></table>';
            }
        }

        $(document).ready(function () {
            const table = $('#table-reports-info').DataTable({
                dom: 'Blrtipt',
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                pageLength: 100,
                processing: true,
                serverSide: true,
                ordering: false,
                ajax: {
                    url: "{{ route('ajaxdata.getdata') }}",
                    dataType: "json",
                    type: "POST",
                    data: function (data) {
                        data.from = $('#from').val();
                        data.to = $('#to').val();
                        data.group_id = $('#group_id').val();
                        data.worker_id = $('#worker_id').val();
                        data.status = $('#status').val();
                        data.pump = $('#pump').val();
                        data._token = "{{csrf_token()}}";

                    }
                },
                columnDefs: [
                    {
                        orderable: false,
                        className: 'select-checkbox',
                        targets: 0
                    }
                ],
                columns: [
                    {
                        className: 'details-control',
                        data: null,
                        defaultContent: '',
                        width: '3%',
                    },
                    {data: "article_name", width: "15%"},
                    {data: "flowopt_name"},
                    {data: "serial_no"},
                    {data: "sales_order"},
                    {data: "group_name"},
                    // {data: "workers_nr"},
                    {data: "production_date"},
                    {data: "taken_at"},
                    {data: "finished_at"},
                    {data: "status"},
                    {data: "exec_time"},
                    {data: "total_resin"},
                    {data: "action"},
                    {
                        data: null,
                        visible: false,
                        render: function (data, type, row, meta) {
                            return meta.row;
                        }
                    },
                ],
                buttons: [{
                    extend: 'excelHtml5',
                    text: 'Excel ALL',
                    title: '',
                    customize: function (xlsx) {
                        const table = $('#table-reports-info').DataTable();

                        // Get number of columns to remove last hidden index column.
                        const numColumns = table.columns().header().count();

                        // Get sheet.
                        const sheet = xlsx.xl.worksheets['sheet1.xml'];

                        const col = $('col', sheet);
                        // Set the column width.
                        $(col[1]).attr('width', 20);

                        // Get a clone of the sheet data.
                        const sheetData = $('sheetData', sheet).clone();

                        // Clear the current sheet data for appending rows.
                        $('sheetData', sheet).empty();

                        // Row count in Excel sheet.
                        let rowCount = 1;

                        // Itereate each row in the sheet data.
                        $(sheetData).children().each(function (index) {

                            let colCount;
                            let row;
                            // Used for DT row() API to get child data.
                            const rowIndex = index - 1;

                            // Don't process row if its the header row.
                            let childData;
                            let headerRow;
                            let child;
                            let childRow;
                            if (index > 0) {

                                // Get row
                                row = $(this.outerHTML);

                                // Set the Excel row attr to the current Excel row count.
                                row.attr('r', rowCount);

                                colCount = 1;

                                // Iterate each cell in the row to change the row number.
                                row.children().each(function (index) {
                                    const cell = $(this);

                                    // Set each cell's row value.
                                    let rc = cell.attr('r');
                                    rc = rc.replace(/\d+$/, "") + rowCount;
                                    cell.attr('r', rc);

                                    if (colCount === numColumns) {
                                        cell.html('');
                                    }

                                    colCount++;
                                });

                                // Get the row HTML and append to sheetData.
                                row = row[0].outerHTML;
                                $('sheetData', sheet).append(row);
                                rowCount++;

                                // Get the child data - could be any data attached to the row.
                                const childDataRow = table.row(':eq(' + rowIndex + ')').data();

                                if (childDataRow !== undefined) {
                                    childData = childDataRow.details;

                                    if (childData.length > 0) {
                                        // Prepare Excel formated row
                                        headerRow = '<row r="' + rowCount +
                                            '" s="2"><c t="inlineStr" r="A' + rowCount +
                                            '"><is><t>' +
                                            '</t></is></c><c t="inlineStr" r="B' + rowCount +
                                            '" s="2"><is><t>Team Ledear' +
                                            '</t></is></c><c t="inlineStr" r="C' + rowCount +
                                            '" s="2"><is><t>Material' +
                                            '</t></is></c><c t="inlineStr" r="D' + rowCount +
                                            '" s="2"><is><t>Pompa' +
                                            '</t></is></c><c t="inlineStr" r="E' + rowCount +
                                            '" s="2"><is><t>Cantitate' +
                                            '</t></is></c></row>';

                                        // Append header row to sheetData.
                                        $('sheetData', sheet).append(headerRow);
                                        rowCount++; // Inc excelt row counter.

                                    }

                                    // The child data is an array of rows
                                    for (let c = 0; c < childData.length; c++) {

                                        // Get row data.
                                        child = childData[c];

                                        // Prepare Excel formated row
                                        childRow = '<row r="' + rowCount +
                                            '"><c t="inlineStr" r="A' + rowCount +
                                            '"><is><t>' +
                                            '</t></is></c><c t="inlineStr" r="B' + rowCount +
                                            '"><is><t>' + child.team_leader.first + ' ' + child.team_leader.last +
                                            '</t></is></c><c t="inlineStr" r="C' + rowCount +
                                            '"><is><t>' + child.material.name +
                                            '</t></is></c><c t="inlineStr" r="D' + rowCount +
                                            '"><is><t>' + child.pump.name +
                                            '</t></is></c><c t="inlineStr" r="E' + rowCount +
                                            '"><is><t>' + child.quantity +
                                            '</t></is></c></row>';

                                        // Append row to sheetData.
                                        $('sheetData', sheet).append(childRow);
                                        rowCount++; // Inc excelt row counter.

                                    }
                                }
                                // Just append the header row and increment the excel row counter.
                            } else {
                                row = $(this.outerHTML);

                                colCount = 1;

                                // Remove the last header cell.
                                row.children().each(function (index) {
                                    const cell = $(this);

                                    if (colCount === numColumns) {
                                        cell.html('');
                                    }

                                    colCount++;
                                });
                                row = row[0].outerHTML;
                                $('sheetData', sheet).append(row);
                                rowCount++;
                            }
                        });
                    },
                    exportOptions: {
                        columns: [2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
                        modifier: {
                            selected: null
                        }
                    }
                },
                    {
                        extend: 'pdfHtml5',
                        orientation: 'landscape',
                        text: 'PDF ALL',
                        title: '',
                        exportOptions: {
                            columns: [2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
                            modifier: {
                                selected: null
                            }
                        }
                    },
                    {
                        extend: 'excelHtml5',
                        text: 'Excel selected row',
                        title: '',
                        customize: function (xlsx) {
                            // console.log(table.rows('.selected').indexes()[0]);

                            const table = $('#table-reports-info').DataTable();

                            // Get number of columns to remove last hidden index column.
                            const numColumns = table.columns().header().count();

                            // Get sheet.
                            const sheet = xlsx.xl.worksheets['sheet1.xml'];

                            const col = $('col', sheet);
                            // Set the column width.
                            $(col[1]).attr('width', 20);

                            // Get a clone of the sheet data.
                            const sheetData = $('sheetData', sheet).clone();

                            // Clear the current sheet data for appending rows.
                            $('sheetData', sheet).empty();

                            // Row count in Excel sheet.
                            let rowCount = 1;

                            // Itereate each row in the sheet data.
                            $(sheetData).children().each(function (index) {

                                let colCount;
                                let row;
                                // Used for DT row() API to get child data.
                                const rowIndex = table.rows('.selected').indexes()[0];
                                console.log(rowIndex);

                                // Don't process row if its the header row.
                                let childData;
                                let headerRow;
                                let child;
                                let childRow;
                                if (index > 0) {

                                    // Get row
                                    row = $(this.outerHTML);

                                    // Set the Excel row attr to the current Excel row count.
                                    row.attr('r', rowCount);

                                    colCount = 1;

                                    // Iterate each cell in the row to change the row number.
                                    row.children().each(function (index) {
                                        const cell = $(this);

                                        // Set each cell's row value.
                                        let rc = cell.attr('r');
                                        rc = rc.replace(/\d+$/, "") + rowCount;
                                        cell.attr('r', rc);

                                        if (colCount === numColumns) {
                                            cell.html('');
                                        }

                                        colCount++;
                                    });

                                    // Get the row HTML and append to sheetData.
                                    row = row[0].outerHTML;
                                    $('sheetData', sheet).append(row);
                                    rowCount++;

                                    // Get the child data - could be any data attached to the row.
                                    const childDataRow = table.row(':eq(' + rowIndex + ')').data();

                                    if (childDataRow !== undefined) {
                                        childData = childDataRow.details;

                                        if (childData.length > 0) {
                                            // Prepare Excel formated row
                                            headerRow = '<row r="' + rowCount +
                                                '" s="2"><c t="inlineStr" r="A' + rowCount +
                                                '"><is><t>' +
                                                '</t></is></c><c t="inlineStr" r="B' + rowCount +
                                                '" s="2"><is><t>Team Ledear' +
                                                '</t></is></c><c t="inlineStr" r="C' + rowCount +
                                                '" s="2"><is><t>Material' +
                                                '</t></is></c><c t="inlineStr" r="D' + rowCount +
                                                '" s="2"><is><t>Pompa' +
                                                '</t></is></c><c t="inlineStr" r="E' + rowCount +
                                                '" s="2"><is><t>Cantitate' +
                                                '</t></is></c></row>';

                                            // Append header row to sheetData.
                                            $('sheetData', sheet).append(headerRow);
                                            rowCount++; // Inc excelt row counter.

                                        }

                                        // The child data is an array of rows
                                        for (let c = 0; c < childData.length; c++) {

                                            // Get row data.
                                            child = childData[c];

                                            // Prepare Excel formated row
                                            childRow = '<row r="' + rowCount +
                                                '"><c t="inlineStr" r="A' + rowCount +
                                                '"><is><t>' +
                                                '</t></is></c><c t="inlineStr" r="B' + rowCount +
                                                '"><is><t>' + child.team_leader.first + ' ' + child.team_leader.last +
                                                '</t></is></c><c t="inlineStr" r="C' + rowCount +
                                                '"><is><t>' + child.material.name +
                                                '</t></is></c><c t="inlineStr" r="D' + rowCount +
                                                '"><is><t>' + child.pump.name +
                                                '</t></is></c><c t="inlineStr" r="E' + rowCount +
                                                '"><is><t>' + child.quantity +
                                                '</t></is></c></row>';

                                            // Append row to sheetData.
                                            $('sheetData', sheet).append(childRow);
                                            rowCount++; // Inc excelt row counter.

                                        }
                                    }
                                } else {
                                    row = $(this.outerHTML);

                                    colCount = 1;

                                    // Remove the last header cell.
                                    row.children().each(function (index) {
                                        const cell = $(this);

                                        if (colCount === numColumns) {
                                            cell.html('');
                                        }

                                        colCount++;
                                    });
                                    row = row[0].outerHTML;
                                    $('sheetData', sheet).append(row);
                                    rowCount++;
                                }
                            });

                        },
                        exportOptions: {
                            columns: [2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
                        }
                    }
                ],
                select: {
                    style: 'single',
                    selector: 'tr>td:nth-child(3), tr>td:nth-child(4), tr>td:nth-child(5), tr>td:nth-child(6), tr>td:nth-child(7), tr>td:nth-child(8), tr>td:nth-child(9), tr>td:nth-child(10), tr>td:nth-child(11)'
                }
            });
            $('#table-reports-info_filter input').addClass('form-control');

            $('#table-reports-info').on('click', 'td.details-control', function () {
                var tr = $(this).closest('tr');
                var row = table.row(tr);

                if (row.child.isShown()) {
                    // This row is already open - close it
                    row.child.hide();
                    tr.removeClass('shown');
                } else {
                    // Open this row
                    row.child(format(row.data())).show();
                    tr.addClass('shown');
                }
            });

            $('#from').on('change', function () {
                table.draw();
                $('#pump').prop('selectedIndex', 0);
            });

            $('#to').on('change', function () {
                table.draw();
                $('#pump').prop('selectedIndex', 0);
            });

            $('#group_id').on('change', function () {
                table.draw();
                $('#pump').prop('selectedIndex', 0);
            });

            $('#worker_id').on('change', function () {
                table.draw();
                $('#pump').prop('selectedIndex', 0);
            });

            $('#status').on('change', function () {
                table.draw();
                $('#pump').prop('selectedIndex', 0);
            });
            $('#pump').on('change', function () {
                table.draw();
            });
        });
        //-----------------------------------------------------
        $('#group_id').multiselect({
            columns: 1,
            placeholder: '--alege un grup--',
            search: true,
            selectAll: true
        });

        $('#worker_id').multiselect({
            columns: 1,
            placeholder: '--alege un TL--',
            search: true,
            selectAll: true
        });

        $('#status').multiselect({
            columns: 1,
            placeholder: '--alege un status--',
            search: true,
            selectAll: true
        });
        //-------------------------------------------------------
        /*$('tbody').delegate('.production_date', 'change', function () {
            var tr = $(this).parent().parent();
            var production_date = tr.find('.production_date').val();
            var product_id = tr.find('.production_date').attr("id");

            $.ajax({
                type: 'get',
                url: '{!! URL::route('productionDate') !!}',
                dataType: 'json',
                data: {'production_date': production_date, 'product_id': product_id},
                success: function (data) {
                    //console.log(data);
                }
            });
        });*/

        //------------------------------------------------------------------
        function resetFilters() {
            document.getElementById("filters").reset();
            location.reload();
        }

    </script>
@endsection

<style>
    label {
        color: #606060;
    }

    ul#material li {
        display: inline;
        padding-right: 10px;
    }

    ul, ol {
        margin-top: 0;
        margin-bottom: 10px;
        padding-left: 0;
    }

    td.details-control {
        background: url({{asset('images/details_open.png')}}) no-repeat center center;
        cursor: pointer;
    }

    tr.shown td.details-control {
        background: url({{asset('images/details_close.png')}}) no-repeat center center;
    }
</style>