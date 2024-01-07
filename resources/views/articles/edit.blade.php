@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <h3 class="page-header"><i class="fa fa-file-text-o"></i> Articole</h3>
            <ol class="breadcrumb">
                <li><i class="fa fa-home"></i><a href="{{ route('home') }}">Acasa</a></li>
                <li><i class="icon_documents_alt"></i>Articole</li>
                <li><i class="fa fa-pencil-square-o"></i>Editare</li>
            </ol>
        </div>
    </div>

    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <strong>Atentie!</strong> Exista probleme cu datele introduse.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        {!! Form::model($article, [ 'route' => ['articles.update', $article->id], 'method' => 'POST', 'files' => true]) !!}
        <div class="col-lg-12">
            <section class="panel panel-default">
                <header class="panel-heading">
                    <span class="panel-title">Editare Articol</span>
                    <a href="{{ route('informatii-rapoarte-articole') }}" class="btn btn-default pull-right" style="margin-top: 2px;">Inapoi</a>
                </header>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-4" style="margin-bottom: 10px;">
                            <label for="category_id"><strong>Categorie</strong></label>
                            <select class="form-control category_id" id="category_id" name="category_id">
                                @foreach($categories as $category)
                                    <option
                                            value="{{$category->id}}"
                                            @if($category->id === $article->category_id)
                                                selected
                                                @endif>
                                            {{$category->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-4" style="margin-bottom: 10px;">
                            <label><strong>Denumire</strong></label>
                            {!! Form::text('name', null, array('placeholder' => 'Nume','class' => 'form-control', 'name' => 'name')) !!}
                        </div>
                        <div class="col-sm-1" style="margin-bottom: 10px;">
                            <label><strong>Nr. Lucratori:</strong></label>
                            {!! Form::number('workers_required', null, array('placeholder' => 'Lucratori','class' => 'form-control', 'name' => 'workers_required' )) !!}
                        </div>

                        <div class="col-sm-3" style="margin-bottom: 10px;">
                            <label for="client_id"><strong>Client:</strong></label>
                            <select class="form-control client_id" id="client_id" name="client_id">
                                @foreach($clients as $client)
                                    <option
                                            value="{{$client->id}}"
                                            @if($client->id === $article->client_id)
                                            selected
                                            @endif>
                                        {{$client->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12" style="margin-bottom: 10px;">
                            <div class="form-group">
                                <a href="{{route('informatii-rapoarte-articole')}}" class="btn btn-default">Anuleaza</a>
                                {{ Form::button('<i class="fa fa-floppy-o"></i> Actualizeaza', ['type' => 'submit', 'class' => 'btn btn-primary'] )  }}
                            </div>
                        </div>
                    </div>

                    <div class="row" style="margin-top: 10px;">
                        <table class="table table-bordered" id="tablelist">
                            <thead>
                            <tr>
                                <th style="width: 30%; padding-left: 4%;">Material</th>
                                <th style="width: 15%;">Cantitate</th>
                                <th style="width: 30%;">Proces</th>
                                <th style="width: 10%;">Extra</th>
                                <th style="width: 7%; text-align: center; background: #eee;"><a href="#" class="addRow" id="plusRow"><span class="fa fa-plus"></span></a></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($article->articles_materials as $key => $articles_material)
                                <tr>
                                    <td>
                                        <input type="hidden" value="{{ $rows }}" id="row">
                                        <i class="fa fa-arrows" style="padding-top: 3%;"></i>
                                        <select class="form-control material" id="material" name="material[]" style="width: 90%; float: right;">
                                            @foreach($materials as $material)
                                                <option
                                                        value="{{$material->id}}"
                                                        @if($material->id === $articles_material->material_id)
                                                        selected
                                                        @endif>
                                                    {{$material->name}}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><input type="number" min="0.1" step="0.1" name="quantity[]" class="form-control quantity"  value="{{ $articles_material->quantity }}"></td>
                                    <td>
                                        <select class="form-control process_id" id="process_id" name="process_id[]">
                                            @foreach($processes as $process)
                                                <option
                                                        value="{{$process->id}}"
                                                        @if($process->id === $articles_material->process_id)
                                                        selected
                                                        @endif>
                                                    {{$process->name}}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td style="text-align: center;">
                                        <select class="form-control extra" id="extra" name="extra[]">
                                            <option value="{{ $articles_material->extra }}" selected>
                                                @if ($articles_material->extra == 0)
                                                    <span>Nu</span>
                                                @else
                                                    <span>Da</span>
                                                @endif
                                            </option>
                                            @if ($articles_material->extra == 0)
                                                <option value="1">Da</option>
                                            @else
                                                <option value="0">Nu</option>
                                            @endif
                                        </select>
                                    </td>
                                    <td style="text-align: center;"><a href="#" class="remove" style="font-size:20px; color: red; text-decoration:none;"><strong>&times;</strong></a></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>
        {!! Form::hidden('_token', csrf_token()) !!}
        {!! Form::close() !!}
    </div>


@endsection

@section('scripts')

    <script type="text/javascript">
        $('tbody').delegate('.material', 'change', function () {
            var tr = $(this).parent().parent();
            tr.find('.quantity').focus();
        });

        $('tbody').delegate('.quantity', 'keyup', function () {
            var tr = $(this).parent().parent();
        });
    //==============================================================
        $('.addRow').on('click', function () {
            addRow();
        });

        function addRow()
        {
            var tr = '<tr>\n' +
                '<td>\n' +
                '<i class="fa fa-arrows" style="padding-top: 2%; padding-left: 5px;"></i>\n' +
                '<select class="form-control material" id="material" name="material[]" style="width: 90%; float: right;" required>\n' +
                '<option value="">--selecteaza un material--</option>\n' +
                '@foreach($materials as $key => $material)\n' +
                '<option value="{{ $material->id }}">{{ $material->name }}</option>\n' +
                '@endforeach\n' +
                '</select>\n' +
                '</td>\n' +
                '<td><input type="number" min="0.1" step="0.1" name="quantity[]" class="form-control quantity" required></td>\n' +
                '<td>\n' +
                '<select class="form-control process_id" name="process_id[]" id="process_id" required>\n' +
                '<option value="">--selecteaza un proces--</option>\n' +
                '@foreach($processes as $key => $process)\n' +
                '<option value="{{ $process->id }}">{{ $process->name }}</option>\n' +
                '@endforeach\n' +
                '</select>\n' +
                '</td>\n' +
                '<td style="text-align: center;">\n' +
                '<select class="form-control extra" id="extra" name="extra[]">\n' +
                '<option value="0" selected>Nu</option>\n' +
                '<option value="1">Da</option>\n' +
                '</select>\n' +
                '</td>\n' +
                '<td style="text-align: center;"><a href="#" class="remove" style="font-size:20px; color: red; text-decoration:none;"><strong>&times;</strong></a></td>\n' +
                '</tr>';

            $('tbody').append(tr);

        }

        $(document).on('click', '.remove', function () {
            var l=$('tbody tr').length;
            if(l==1)
            {
                alert('Nu puteti sterge primul rand!');
            }else {
                $(this).parent().parent().remove();
            }
        });
    //===================================================================
        $(function() {
            $( "tbody" ).sortable();
        });
    </script>


@endsection

<style>

</style>