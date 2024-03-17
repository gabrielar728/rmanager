<table class="table table-striped" id="table-group-info">
    <thead>
        <tr>
            <th style="font-size: 12px; padding-left: 10px; width: 30px;"></th>
            <th style="font-size: 12px; padding-left: 10px; width: 30px;">Nr. <br>crt.</th>
            <th style="padding-left: 10px;">Denumire</th>
            <th style="padding-left: 10px;">Creat la</th>
            <th style="display: none;"></th>
            <th style="padding-left: 10px;">Actiune</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $n = 0;
        ?>
        @foreach($groups as $key => $group)
        <tr>
            <td style="padding-left: 10px; padding-top: 8px !important;"></td>
            <td style="padding-left: 10px; padding-top: 8px !important;">{{ ++$n }}</td>
            <td style="padding-left: 10px; padding-top: 8px !important;">{{ $group->name }}</td>
            <td style="padding-left: 10px; padding-top: 8px !important;">{{ Carbon\Carbon::parse($group->created_at_group)->format('d.m.Y H:i:s') }}</td>
            <td style="display: none;">
                <table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px; border-collapse: separate; background-color: transparent;">
                    <?php
                    $i = 0;
                    ?>
                    @foreach($group->articles as $article)
                    <tr style="background-color: transparent;">
                        <td style="padding-top: 10px !important;">Articol {{ ++$i}}:</td>
                        <td style="padding-top: 10px !important;">{{ $article->name }}</td>
                    </tr>
                    @endforeach
                </table>
            </td>
            <td style="padding-left: 10px; padding-top: 8px !important;">
                @if($group->status == 1)
                <a class="btn btn-danger btn-sm" href="{{ route('changeGroupActiveStatus', $group->id) }}">Seteaza Inactiv</a>
                <button class="btn btn-primary btn-sm sorting" id="{{ $group->id }}">Ordonare</button>
                @elseif($group->status)
                <a class="btn btn-success btn-sm" href="{{ route('changeGroupInactiveStatus', $group->id) }}">Seteaza Activ</a>
                @else
                Nu se poate edita
                @endif
            </td>
        </tr>
        <tr style="display: none;" id="group_sort{{ $group->id }}">
            <td class="hide"></td>
            <td class="hide"></td>
            <td style="width: 60%;" id="td_sort{{ $group->id }}"></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        @endforeach
    </tbody>
</table>


<script type="text/javascript">
    function format(d) {
        // `d` is the original data object for the row
        return d.articles_name;
    }

    $(document).ready(function() {
        var table = $('#table-group-info').DataTable({

            dom: 'Blfrtip',
            "lengthMenu": [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "All"]
            ],
            "pageLength": 100,
            ordering: false,
            "columns": [{
                    "className": 'hide',
                    "orderable": false,
                    "data": null,
                    "defaultContent": ''
                },
                {
                    "data": "name"
                },
                {
                    "data": "nr_crt"
                },
                {
                    "data": "created_at_group"
                },
                {
                    "data": "articles_name"
                },
                {
                    "data": "action_group"
                }
            ],
            buttons: [{
                    extend: 'copy',

                    exportOptions: {
                        columns: [0, 1, 2]
                    }
                },
                {
                    extend: 'excel',
                    exportOptions: {
                        columns: [0, 1, 2]
                    }
                },
                {
                    extend: 'pdf',
                    exportOptions: {
                        columns: [0, 1, 2]
                    }
                }
            ]
        });
        $('#table-group-info_filter input').addClass('form-control');

        $('#table-group-info tbody').on('click', 'td.details-control', function() {
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
        //-----------------------------------------------------
        $('.sorting').each(function() {
            $(this).click(function() {
                var id = $(this).attr('id');
                $.ajax({
                    type: "POST",
                    url: "{{ route('group.sorting') }}",
                    data: {
                        'id': id
                    },
                    success: function(articles) {
                        $(".hide").attr('class', 'hinding');
                        $("#td_sort" + id).html(articles);
                        $("#group_sort" + id).show();
                    }
                });
            });
        });
    });
    //=================================================

    $(document).on('click', '.btn-del', function(e) {
        var id = $(this).val();
        $.ajax({
            type: "post",
            url: "{{ url('/facturi/sterge-material') }}",
            data: {
                'id': id
            },
            success: function(data) {
                if (data.msg) {
                    $('.id' + id).remove();
                } else {
                    alert("Nu puteti sterge. Materialul exista in cel putin o factura sau un articol.");
                }
            }
        });
    });
    //=========================================================
</script>