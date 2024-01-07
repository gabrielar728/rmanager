<table class="table table-striped" id="table-material-info">
    <thead>
    <tr>
        <th style="font-size: 12px; padding-left: 10px; width: 50px;">Nr. <br> crt.</th>
        <th style="padding-left: 10px;">Denumire</th>
        <th style="padding-left: 10px;">U.M.</th>
        <th style="padding-left: 10px;">Actiune</th>
    </tr>
    </thead>
    <tbody>
        <?php
        $i=0;
        ?>
        @foreach($materials as $key => $material)
            <tr class="id{{$material->id}}">
                <td style="padding-left: 10px;">{{ ++$i}}</td>
                <td style="padding-left: 10px;">{{ $material->name }}</td>
                <td style="padding-left: 10px;">
                    @if($material->unit == 'm2')
                        <span>m&sup2;</span>
                    @else
                        <span>{{ $material->unit }}</span>
                    @endif
                </td>
                <td style="padding-left: 10px; width: 15%; vertical-align: middle;">
                    <a class="btn btn-warning btn-sm" href="{{ route('materials.edit', $material->id) }}"><i class="fa fa-pencil"></i> Editare</a>
                    {{--<button class="btn btn-danger btn-sm btn-del" value="{{ $material->id }}"><i class="fa fa-trash-o"></i> Sterge</button>--}}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<script type="text/javascript">
    $(document).ready(function () {
        $('#table-material-info').DataTable({

            dom: 'Blfrtip',
            buttons: [
                {
                    extend: 'copy',

                    exportOptions: {
                        columns: [0,1,2,3]
                    }
                },
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
                }
            ]
        } );
        $('#table-material-info_filter input').addClass('form-control');
    });
    //=================================================

    $(document).on('click', '.btn-del', function (e) {
        var id = $(this).val();
            $.ajax({
                type: "post",
                url: "{{ url('/facturi/sterge-material') }}",
                data: {'id':id},
                success:function (data) {
                    if(data.msg)
                    {
                        $('.id'+id).remove();
                    }
                    else{
                        alert("Nu puteti sterge. Materialul exista in cel putin o factura sau un articol.");
                    }
                }
            })
    })

</script>
