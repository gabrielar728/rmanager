<table class="table table-striped" id="table-workers-info">
    <thead>
    <tr>
        <th style="font-size: 12px; padding-left: 10px;">Nr. <br> crt.</th>
        <th style="padding-left: 10px;">Nume</th>
        <th style="padding-left: 10px;">Prenume</th>
        <th style="padding-left: 10px;">Numar Card</th>
        <th style="padding-left: 10px;">Status</th>
        <th style="padding-left: 10px;">Creat la</th>
        <th style="padding-left: 10px;">Modificat la</th>
        <th style="padding-left: 10px;">Actiune</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $i=0;
    ?>
    @foreach($workers as $key => $worker)
        <tr>
            <td style="padding-left: 10px;">{{ ++$i}}</td>
            <td style="padding-left: 10px;">{{ $worker->first }}</td>
            <td style="padding-left: 10px;">{{ $worker->last }}</td>
            <td style="padding-left: 10px;">{{ $worker->card }}</td>
            <td style="padding-left: 10px;">
                <select class="form-control status"  id="{{ $worker->id }}" name="status">
                    <option value="{{ $worker->status }}" selected>
                        @if ($worker->status == 0)
                            <span>inactiv</span>
                        @else
                            <span>activ</span>
                        @endif
                    </option>
                    @if ($worker->status == 0)
                        <option value="1">activ</option>
                    @else
                        <option value="0">inactiv</option>
                    @endif
                </select>
            </td>
            <td style="padding-left: 10px;">{{ Carbon\Carbon::parse($worker->created_at)->format('d.m.Y H:i:s') }}</td>
            <td style="padding-left: 10px;">{{ Carbon\Carbon::parse($worker->updated_at)->format('d.m.Y H:i:s') }}</td>
            <td style="padding-left: 10px; width: 15%; vertical-align: middle;">
                @if($worker->exist == 0)
                    <a class="btn btn-warning btn-sm" href="{{ route('workers.edit',$worker->id) }}"><i class="fa fa-pencil"></i> Editare</a>
                @else
                    <span>Nu se poate edita.</span>
                @endif
            </td>
        </tr>
    @endforeach
    </tbody>
</table>



<script type="text/javascript">
    $(document).ready(function () {
        $('#table-workers-info').DataTable({

            dom: 'Blfrtip',
            buttons: [
                {
                    extend: 'copy',

                    exportOptions: {
                        columns: [1,2,3,4,5,6]
                    }
                },
                {
                    extend: 'excel',
                    exportOptions: {
                        columns: [1,2,3,4,5,6]
                    }
                },
                {
                    extend: 'pdf',
                    exportOptions: {
                        columns: [1,2,3,4,5,6]
                    }
                }
            ]
        } );
        $('#table-workers-info_filter input').addClass('form-control');
    });
    //=================================================
    $('tbody').delegate('.status', 'change', function () {
        console.log('changed');
        var tr = $(this).parent().parent();

        var status = tr.find('.status').val();
        var worker_id = tr.find('.status').attr("id");
        $.ajax({
            type: 'get',
            url: '{!! URL::route('workerStatus') !!}',
            dataType: 'json',
            data: { 'status':status, 'worker_id':worker_id },
            success: function(data)
            {
                //console.log(data);
            }
        });
    });


</script>
