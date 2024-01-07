<table class="table table-striped" id="table-products-info">
    <thead>
    <tr>
        <th style="font-size: 12px; padding-left: 10px;">Nr. <br> crt.</th>
        <th style="padding-left: 10px;">Produs</th>
        <th style="padding-left: 10px;">Client</th>
        <th style="padding-left: 10px;">Creat la</th>
        <th style="padding-left: 10px;">Alocat lui</th>
        <th style="padding-left: 10px;">Data Productie</th>
        <th style="padding-left: 10px;">Actiune</th>
    </tr>
    </thead>
    <tbody>
        <?php
        $i=0;
        ?>
        @foreach($products as $key => $product)
            <tr id="{{ $product->id }}">
                <td style="padding-left: 10px;">{{ ++$i}}</td>
                <td style="padding-left: 10px;">{{ $product->article['name'] }}</td>
                <td style="padding-left: 10px;">{{ $product->article->client['name'] }}</td>
                <td style="padding-left: 10px;">{{ Carbon\Carbon::parse($product->created_at)->format('d.m.Y H:i:s') }}</td>
                <td style="padding-left: 10px;">
                    <select style="color: blue;" class="form-control assigned_to" id="{{ $product->id }}" name="assigned_to">
                        @foreach($workers as $worker)
                            <option style="color: #797979;"
                                    value="{{$worker->id}}"
                                    @if($worker->id === $product->worker_id)
                                    selected
                                    @endif>
                                {{$worker->first}} {{ $worker->last }}</option>
                        @endforeach
                    </select>
                </td>
                <td style="padding-left: 10px;">
                    <input type="date" id="{{ $product->id }}" name="production_date" class="form-control production_date" value="{{ $product->production_date }}">
                </td>
                <td>
                    <button id="{{ $product->id }}" class="btn btn-danger btn-sm cancel"> Anulare</button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<script type="text/javascript">

    $(document).ready(function () {
        $('.cancel').each(function() {
            $(this).click(function(){
                var id = $(this).attr('id');
                var url = '{{ route("products.cancel", ":id") }}';
                url = url.replace(':id', id);
                var data = {'id':id};
                $.get(url, data, function (data) {
                    //
                });
                $(this).parent().parent().remove();
            });
        });
        //---------------------------------------------------------------
        $('#table-products-info').DataTable({

            dom: 'Blfrtip',
			"lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
			"pageLength": 100,
            buttons: [
                {
                    extend: 'copy',

                    exportOptions: {
                        columns: [1,2,3]
                    }
                },
                {
                    extend: 'excel',
                    exportOptions: {
                        columns: [1,2,3]
                    }
                },
                {
                    extend: 'pdf',
                    exportOptions: {
                        columns: [1,2,3]
                    }
                }
            ]
        } );
        $('#table-products-info_filter input').addClass('form-control');
    //===================================================================
        $('tbody').delegate('.assigned_to', 'change', function () {
            var tr = $(this).parent().parent();

            var assignee_id = tr.find('.assigned_to').val();
            var product_id = tr.find('.assigned_to').attr("id");
            $.ajax({
                type: 'get',
                url: '{!! URL::route('assignedTo') !!}',
                dataType: 'json',
                data: { 'assignee_id':assignee_id, 'product_id':product_id },
                success: function(data)
                {
                    //console.log(data);
                }
            });
        });
    //===================================================================
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
    //===================================================================
        $('.assigned_to').select2({ width: '100%'});
    });
    
</script>

<style>
    #assigned_to {
        
    }
</style>