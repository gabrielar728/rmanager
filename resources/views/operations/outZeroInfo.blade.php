<table class="table table-striped" id="table-out-info">
    <thead>
    <tr>
        <th style="font-size: 12px; padding-left: 10px; width: 50px;">Nr. <br> crt.</th>
        <th style="padding-left: 10px;">Material</th>
        <th style="padding-left: 10px;">Numar Pompe/U.M.</th>
        <th style="padding-left: 10px;">U.M.</th>
        <th style="padding-left: 10px;">Consum</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $i=0;
    ?>
    @foreach($inventories as $key => $inventory)
        @if($inventory->consum > 0)
            <tr>
                <td style="padding-left: 10px;">{{ ++$i}}</td>
                <td style="padding-left: 10px;">{{ $inventory->name }}</td>
                <td style="padding-left: 10px;">{{ $inventory->ratio }}</td>
                <td style="padding-left: 10px;">
                    @if($inventory->unit == 'm2')
                        <span>m&sup2;</span>
                    @else
                        <span>{{ $inventory->unit }}</span>
                    @endif
                </td>
                <td style="padding-left: 10px;">{{ $inventory->consum  }}</td>
            </tr>
        @endif
    @endforeach
    </tbody>
</table>

<script type="text/javascript">
    $(document).ready(function () {
        $('#table-out-info').DataTable({

            dom: 'Blfrtip',
            buttons: [
                {
                    extend: 'copy',

                    exportOptions: {
                        columns: [1,2,3,4]
                    }
                },
                {
                    extend: 'excel',
                    exportOptions: {
                        columns: [1,2,3,4]
                    }
                },
                {
                    extend: 'pdf',
                    exportOptions: {
                        columns: [1,2,3,4]
                    }
                }
            ]
        } );
        $('#table-out-info_filter input').addClass('form-control');
    });
    //=================================================


</script>
