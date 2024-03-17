<div class="row">
    <div class="col-sm-3" style="padding-right: 0;">
        <label for="name"><strong>Nume Grup:</strong></label>
    </div>
    <div class="col-sm-9">
        <span style="font-weight: 500;">{{ $group_name }}</span>
    </div>
</div>
<div class="row" style="margin-bottom: 20px;">
    <table class="table table-striped" id="articles_details" style="margin-top: 10px; font-size: 14px !important;">
        <thead>
        <tr>
            <th style=""></th>
            <th>Nume Articol</th>
            <th>Categorie</th>
            <th>Client</th>
            <th style="display: none;"></th>
        </tr>
        </thead>
        <tbody>
        @foreach($articles_groups as $articles_group)
            <tr>
                <td style=""></td>
                <td><span>{{ $articles_group->article['name'] }}</span></td>
                <td><span>{{ $articles_group->article->article_category['name'] }}</span></td>
                <td><span>{{ $articles_group->article->client['name'] }}</span></td>
                <td style="display: none;">
                    <table class="table table-bordered table-striped" cellpadding="5" cellspacing="0" border="0" style=" font-size: 14px !important; border-collapse: separate; background-color: transparent;">
                        <thead>
                        <tr style="background-color: transparent;">
                            <th style="width: 40px; text-align: center; padding-top: 10px !important;">#</th>
                            <th style="padding-top: 10px !important;">Material</th>
                            <th style="width: 15%;padding-top: 10px !important;">Cantitate</th>
                            <th style="padding-top: 10px !important;">Proces</th>
{{--                            <th style="width: 70px;padding-top: 10px !important;">Extra</th>--}}
                            <th style="width: 7%; padding-top: 10px !important;">U.M.</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $n=0;
                        ?>
                        @foreach($articles_group->article->articles_materials as $articles_material)
                            <tr style="background-color: transparent;">
                                <td style="text-align: center; padding-top: 10px !important;">{{ ++$n}}</td>
                                <td style="padding-top: 10px !important;"><span>{{$articles_material->material['name']}}</span></td>
                                <td style="padding-top: 10px !important;"><span>{{$articles_material->quantity}}</span></td>
                                <td style="padding-top: 10px !important;"><span>{{$articles_material->process['name']}}</span></td>
                               {{-- <td style="text-align: center; padding-top: 10px !important;">
                                    @if($articles_material->extra === 0)
                                        <input type="checkbox" class="input-control extra" value="0" id="extra" name="extra[]" style="height:20px; width:20px;" disabled>
                                    @else
                                        <input type="checkbox" class="input-control extra" value="1" checked id="extra" name="extra[]" style="height:20px; width:20px;" disabled>
                                    @endif
                                </td>--}}
                                <td style="padding-top: 10px !important;"><span>{{ $articles_material->material['unit'] }}</span></td>
                        @endforeach
                        </tbody>
                    </table>
                </td>
        @endforeach
        </tbody>
    </table>
</div>

<script type="text/javascript">

    function format ( d ) {
        // `d` is the original data object for the row
        return d.articles_details;
    }
    $(document).ready(function () {
        var table = $('#articles_details').DataTable({
            "paging":   false,
            "ordering": false,
            "info":     false,
            "searching": false,
            "columns": [
                {
                    "className":      'details-control',
                    "orderable":      false,
                    "data":           null,
                    "defaultContent": ''
                },
                { "data": "article_name" },
                { "data": "category" },
                { "data": "client" },
                { "data": "articles_details" }
            ]
        } );

        $('#articles_details tbody').on('click', 'td.details-control', function () {
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
    });
    //==================================================

</script>

<style>
    hr {
        margin-top: 10px;
        margin-bottom: 10px;
        border: 0;
        border-top: 1px solid #f2f2f2;
    }

    td.details-control {
        background: url({{ asset('../images/details_open.png') }}) no-repeat center center;
        cursor: pointer;
    }
    tr.shown td.details-control {
        background: url({{ asset('../images/details_close.png') }}) no-repeat center center;
    }

</style>