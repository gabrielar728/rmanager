@if(count($articles))
    <table class="table table-striped" id="table-articles-info">
        <thead>
        <tr>
            <th style="font-size: 12px; padding-left: 10px; width: 30px;"></th>
            <th style="padding-left: 10px;">Nume Articol</th>
            <th style="padding-left: 10px;">Categorie Articol</th>
            <th style="padding-left: 10px;">Client</th>
            <th style="padding-left: 10px;">Status</th>
            <th style="padding-left: 10px;">Creat la</th>
            <th style="display: none;"></th>
            <th style="padding-left: 10px;">Actiune</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $i=0;
        ?>
        @foreach($articles as $key => $article)
            <tr>
                <td style="padding-top: 8px !important;"></td>
                <td style="padding-left: 10px; padding-top: 8px !important;">{{ $article->name }}</td>
                <td style="padding-left: 10px; padding-top: 8px !important;">{{ $article->article_category['name'] }}</td>
                <td style="padding-left: 10px; padding-top: 8px !important;">{{ $article->client['name'] }}</td>
                <td style="padding-left: 10px; padding-top: 8px !important;">
                    @if($article->status == 1)
                        <span style="color: green;">activ</span>
                    @else
                        <span style="color: red;">inactiv</span>
                    @endif
                </td>
                <td style="padding-left: 10px; padding-top: 8px !important;">{{ Carbon\Carbon::parse($article->created_at_article)->format('d.m.Y H:i:s') }}</td>
                <td style="display: none;">
                    <table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px; border-collapse: separate; background-color: white;">
                        <thead>
                        <tr>
                            <th style="width: 40px; text-align: center; padding-top: 10px !important;">#</th>
                            <th style="padding-top: 10px !important;">Material</th>
                            <th style="width: 15%;padding-top: 10px !important;">Cantitate</th>
                            <th style="padding-top: 10px !important;">Proces</th>
                            <th style="width: 70px;padding-top: 10px !important;">Extra</th>
                            <th style="width: 7%; padding-top: 10px !important;">U.M.</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php 
                        $n=0;
                        ?>
                        @foreach($article->articles_materials as $articles_material)
                            <tr style="background-color: transparent;">
                                <td style="text-align: center; padding-top: 10px !important;">{{ ++$n}}</td>
                                <td style="padding-top: 10px !important;"><span>{{$articles_material->material['name']}}</span></td>
                                <td style="padding-top: 10px !important;"><span>{{$articles_material->quantity}}</span></td>
                                <td style="padding-top: 10px !important;"><span>{{$articles_material->process['name']}}</span></td>
                                <td style="text-align: center; padding-top: 10px !important;">
                                    @if($articles_material->extra === 0)
                                        <input type="checkbox" class="input-control extra" value="0" id="extra" name="extra[]" style="height:20px; width:20px;" disabled>
                                    @else
                                        <input type="checkbox" class="input-control extra" value="1" checked id="extra" name="extra[]" style="height:20px; width:20px;" disabled>
                                    @endif
                                </td>
                                <td style="padding-top: 10px !important;"><span>{{ $articles_material->material['unit'] }}</span></td>
                        @endforeach
                        </tbody>
                    </table>
                </td>
                <td style="padding-left: 10px; padding-top: 8px !important;">
                    @if($article->exist == 0)
                        <a class="btn btn-warning btn-sm" href="{{ route('articles.edit', $article->article_id) }}"><i class="fa fa-pencil"></i> Editare</a>
                    @endif
                    @if($article->status == 1)
                        <a class="btn btn-success btn-sm" href="{{ route('changeArticleActiveStatus', $article->article_id) }}">Activ</a>
                    @else
                        <a class="btn btn-danger btn-sm" href="{{ route('changeArticleInactiveStatus', $article->article_id) }}">Inactiv</a>
                    @endif
                    <a href="{{ route('printArticle', $article->article_id) }}" target="_blank"><img src="{{ asset('images/pdf.png') }}" /></a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <script type="text/javascript">

        function format ( d ) {
            // `d` is the original data object for the row
            return d.details;
        }
        $(document).ready(function () {
            var table = $('#table-articles-info').DataTable({

                dom: 'Blfrtip',
                "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                "pageLength": 100,
                "columns": [
                    {
                        "className":      'details-control',
                        "orderable":      false,
                        "data":           null,
                        "defaultContent": ''
                    },
                    { "data": "article_category" },
                    { "data": "article_name" },
                    { "data": "client" },
                    { "data": "status" },
                    { "data": "created_at" },
                    { "data": "details" },
                    { "data": "action" }
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
        });
        //==================================================

    </script>

@else
    <h4><strong>Ne pare rau. Nu am gasit rezultate.</strong></h4>
    <p>Incearca sa modifici filtrele alese pentru a gasi articole.</p>
@endif