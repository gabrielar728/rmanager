<table class="table table-striped" id="table-pumps-info">
    <thead>
    <tr>
        <th style="font-size: 12px; padding-left: 10px;">Nr. <br> crt.</th>
        <th style="padding-left: 10px;">Denumire</th>
        <th style="padding-left: 10px;">Locatie</th>
        <th style="padding-left: 10px;">IP</th>
        <th style="padding-left: 10px;">Port</th>
        <th style="padding-left: 10px;">Material</th>
		<th style="padding-left: 10px;">Numar Pompe/UM</th>
        <th style="padding-left: 10px;">Creata la</th>
        <th style="padding-left: 10px;">Modificata la</th>
        <th style="padding-left: 10px;">Actiune</th>
    </tr>
    </thead>
    <tbody>
        <?php
        $i=0;
        ?>
        @foreach($pumps as $key => $pump)
            <tr>
                <td style="padding-left: 10px;">{{ ++$i}}</td>
                <td style="padding-left: 10px;">{{ $pump->name }}</td>
                <td style="padding-left: 10px;">{{ $pump->location }}</td>
                <td style="padding-left: 10px;">{{ $pump->ip }}</td>
                <td style="padding-left: 10px;">{{ $pump->port }}</td>
                <td style="padding-left: 10px;">{{ $pump->material['name'] }}</td>
				<td style="padding-left: 10px;">{{ $pump->ratio }}</td>
                <td style="padding-left: 10px;">{{ Carbon\Carbon::parse($pump->created_at)->format('d.m.Y H:i:s') }}</td>
                <td style="padding-left: 10px;">{{ Carbon\Carbon::parse($pump->updated_at)->format('d.m.Y H:i:s') }}</td>
                <td style="padding-left: 10px; width: 15%; vertical-align: middle;">
                    <a class="btn btn-warning btn-sm" href="{{ route('pumps.edit',$pump->id) }}"><i class="fa fa-pencil"></i> Editare</a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>



<script type="text/javascript">
        $(document).ready(function () {
            $('#table-pumps-info').DataTable({

                dom: 'Blfrtip',
                buttons: [
                    {
                        extend: 'copy',

                        exportOptions: {
                            columns: [1,2,3,4,5,6,7]
                        }
                    },
                    {
                        extend: 'excel',
                        exportOptions: {
                            columns: [1,2,3,4,5,6,7]
                        }
                    },
                    {
                        extend: 'pdf',
                        exportOptions: {
                            columns: [1,2,3,4,5,6,7]
                        }
                    }
                ]
            } );
            $('#table-pumps-info_filter input').addClass('form-control');
        });
    //=================================================



</script>
