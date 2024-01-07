@extends('layouts.master')

@section('content')
    @include('articles.popup.article_category')
    @include('articles.popup.client')
    @include('articles.popup.process')
    <div class="row">
        <div class="col-lg-12">
            <h3 class="page-header"><i class="fa fa-file-text-o"></i> Articole</h3>
            <ol class="breadcrumb">
                <li><i class="fa fa-home"></i><a href="{{ route('home') }}">Acasa</a></li>
                <li><i class="icon_documents_alt"></i>Articole</li>
                <li><i class="fa fa-plus-circle"></i>Adaugare Articol</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <form action="{{ route('articles-insert') }}" id="frm-create-article" method="post">
            <div class="col-lg-12">
                <section class="panel panel-default">
                    <header class="panel-heading">
                        <span class="panel-title">Creare Articol</span>
                    </header>
                    <div class="panel-body">
                        @if(session()->has('message'))
                            <div class="alert alert-success alert-block">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                <strong>{{ session('message') }}</strong>
                            </div>
                        @endif
                        <div class="row">
                            <div class="col-lg-12">
                                <p><a href="#" style="text-decoration: underline;" id="add-more-process" data-target="process-show">Adauga procese</a></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-3">
                                <label for="category_id"><strong>Categorie</strong></label>
                                <div class="input-group">
                                    <select class="form-control" name="category_id" id="category_id" required>
                                        <option class="0" value="">--selecteaza o categorie--</option>
                                        @foreach($categories as $category)
                                            <option value="{{$category->id}}">{{$category->name}}</option>
                                        @endforeach
                                    </select>
                                    <div class="input-group-addon">
                                        <span class="fa fa-plus" id="add-more-category" data-target="#article-category-show"></span>
                                    </div>
                                    <div class="input-group-addon" id="del">
                                        <input type="button" id="btnDeleteCategory" class="btnDelete"  value="&times;" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <label for="name"><strong>Denumire</strong></label>
                                <div class="form-group">
                                    <input type="text" class="form-control" name="name" id="name" onkeyup="checkName();" required>
                                    <span id="name_message" style="color: #e02b27; font-size: 1.2rem;"></span>
                                </div>
                            </div>
                            <div class="col-lg-1">
                                <label for="workers_required"><strong>Nr. Lucratori</strong></label>
                                <div class="form-group">
                                    <input type="number" class="form-control" name="workers_required" id="workers_required" min="1" required>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <label for="client_id"><strong>Client</strong></label>
                                <div class="input-group">
                                    <select class="form-control" name="client_id" id="client_id" required>
                                        <option class="0" value="">--selecteaza un client--</option>
                                        @foreach($clients as $client)
                                            <option value="{{$client->id}}">{{$client->name}}</option>
                                        @endforeach
                                    </select>
                                    <div class="input-group-addon">
                                        <span class="fa fa-plus" id="add-more-client" data-target="client-show"></span>
                                    </div>
                                    <div class="input-group-addon" id="del">
                                        <input type="button" id="btnDeleteClient" class="btnDelete"  value="&times;" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <a href="{{ route('home') }}" class="btn btn-default">Inapoi</a>
                                    {{ Form::button('<i class="fa fa-floppy-o"></i> Salveaza', ['type' => 'submit', 'class' => 'btn btn-primary', 'id' => 'submitFrm'] )  }}
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6">
                                <table class="table table-bordered">
                                    <thead>
                                    <tr><th style="width: 30%; padding-left: 4%; text-align: center;" colspan="6"><strong style="color: black;">Etape obligatorii</strong></th></tr>
                                    <tr>
                                        <th style="width: 30%; padding-left: 4%;">Material</th>
                                        <th style="width: 15%;">Cantitate</th>
                                        <th style="width: 30%;">Proces</th>
                                        <th style="width: 7%;">U.M.</th>
                                        <th style="width:7%;text-align: center; background: #eee;"><a href="#" class="addRow" id="plusRow"><span class="fa fa-plus"></span></a></th>
                                    </tr>
                                    </thead>
                                    <tbody id="reqStages">
                                    <tr>
                                        <td>
                                            <i class="fa fa-arrows" style="padding-top: 10px; padding-left: 0;"></i>
                                            <select class="form-control material" id="material" name="material[]" style="width: 90%; float: right;" required>
                                                <option value="">--alege un material--</option>
                                                @foreach($material_lists as $key => $material)
                                                    <option value="{{ $material->id }}">{{ $material->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><input type="number" min="0.1" step="0.1" name="quantity[]" class="form-control quantity" required></td>
                                        <td>
                                            <select class="form-control process_id" name="process_id[]" id="process_id" required>
                                                <option value="">--alege un proces--</option>
                                                @foreach($processes as $key => $process)
                                                    <option value="{{ $process->id }}">{{ $process->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input class="form-control unit" type="text" name="unit[]" value="" style="border: none; padding-top: 0; padding-bottom: 0; height: 20px; background-color: transparent;" readonly> </td>
                                        <td style="text-align: center;"><a href="#" class="remove" style="font-size:20px; color: red; text-decoration:none;"><strong>&times;</strong></a></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="col-lg-6">
                                <table class="table table-bordered">
                                    <thead>
                                    <tr><th style="width: 30%; padding-left: 4%; text-align: center;" colspan="6"><strong style="color: black;">Etape optionale</strong></th></tr>
                                    <tr>
                                        <th style="width: 30%; padding-left: 4%;">Material</th>
                                        <th style="width: 15%;">Cantitate</th>
                                        <th style="width: 30%;">Proces</th>
                                        <th style="width: 7%;">U.M.</th>
                                        <th style="width:7%;text-align: center; background: #eee;"><a href="#" class="addExtra" id="addExtra"><span class="fa fa-plus"></span></a></th>
                                    </tr>
                                    </thead>
                                    <tbody id="supStages">

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            {{ csrf_field() }}
        </form>
    </div>


@endsection

@section('scripts')

    <script type="text/javascript">

        $('#reqStages').delegate('.material', 'change', function () {
            var tr = $(this).parent().parent();
            var id = tr.find('.material').val();
            var dataId = {'id':id};
            $.ajax({
                type: 'GET',
                url: '{!! URL::route('findUnit') !!}',
                dataType: 'json',
                data: dataId,
                success: function (data) {
                    tr.find('.unit').val(data.unit);
                }
            });
        });

        $('#supStages').delegate('.materialExtra', 'change', function () {
            var tr = $(this).parent().parent();
            var id = tr.find('.materialExtra').val();
            var dataId = {'id':id};
            $.ajax({
                type: 'GET',
                url: '{!! URL::route('findUnit') !!}',
                dataType: 'json',
                data: dataId,
                success: function (data) {
                    tr.find('.unitExtra').val(data.unit);
                }
            });
        });
        //============================================================
        $('#reqStages').delegate('.material', 'change', function () {
            var tr = $(this).parent().parent();
            tr.find('.quantity').focus();
        });
        //============================================================
        $('.addRow').on('click', function () {
            addRow();
        });

        function addRow()
        {
            var tr = '<tr>\n' +
                '<td>\n' +
                '<i class="fa fa-arrows" style="padding-top: 10px; padding-left: 0;"></i>\n' +
                '<select class="form-control material" id="material" name="material[]" style="width: 90%; float: right;" required>\n' +
                '<option value="">--alege un material--</option>\n' +
                '@foreach($material_lists as $key => $material)\n' +
                '<option value="{{ $material->id }}">{{ $material->name }}</option>\n' +
                '@endforeach\n' +
                '</select>\n' +
                '</td>\n' +
                '<td><input type="number" min="0.1" step="0.1" name="quantity[]" class="form-control quantity" required></td>\n' +
                '<td>\n' +
                '<select class="form-control process_id" name="process_id[]" id="process_id" required>\n' +
                '<option value="">--alege un proces--</option>\n' +
                '@foreach($processes as $key => $process)\n' +
                '<option value="{{ $process->id }}">{{ $process->name }}</option>\n' +
                '@endforeach\n' +
                '</select>\n' +
                '</td>\n' +
                '<td>\n' +
                '<input class="form-control unit" type="text" name="unit[]" value="" style="border: none; padding-top: 0; padding-bottom: 0; height: 20px; background-color: transparent;" readonly> </td>\n' +
                '<td style="text-align: center;"><a href="#" class="remove" style="font-size:20px; color: red; text-decoration:none;"><strong>&times;</strong></a></td>\n' +
                '</tr>';

            $('#reqStages').append(tr);
        }

        $(document).on('click', '.remove', function () {
            var l=$('#reqStages tr').length;
            if(l==1)
            {
                alert('Nu puteti sterge primul rand!');
            }else {
                $(this).parent().parent().remove();
            }
        });
        //============================================================
            $('.addExtra').on('click', function () {
                addExtra();
            });

            function addExtra()
            {
                var tr = '<tr>\n' +
                    '<td>\n' +
                    '<i class="fa fa-arrows" style="padding-top: 10px; padding-left: 0;"></i>\n' +
                    '<select class="form-control materialExtra" id="materialExtra" name="materialExtra[]" style="width: 90%; float: right;" required>\n' +
                    '<option value="">--alege un material--</option>\n' +
                    '@foreach($material_lists as $key => $material)\n' +
                    '<option value="{{ $material->id }}">{{ $material->name }}</option>\n' +
                    '@endforeach\n' +
                    '</select>\n' +
                    '</td>\n' +
                    '<td><input type="number" min="0.1" step="0.1" name="quantityExtra[]" class="form-control quantityExtra" required></td>\n' +
                    '<td>\n' +
                    '<select class="form-control process_idExtra" name="process_idExtra[]" id="process_idExtra" required>\n' +
                    '<option value="">--alege un proces--</option>\n' +
                    '@foreach($processes as $key => $process)\n' +
                    '<option value="{{ $process->id }}">{{ $process->name }}</option>\n' +
                    '@endforeach\n' +
                    '</select>\n' +
                    '</td>\n' +
                    '<td>\n' +
                    '<input class="form-control unitExtra" type="text" name="unitExtra[]" value="" style="border: none; padding-top: 0; padding-bottom: 0; height: 20px; background-color: transparent;" readonly> </td>\n' +
                    '<td style="text-align: center;"><a href="#" class="removeExtra" style="font-size:20px; color: red; text-decoration:none;"><strong>&times;</strong></a></td>\n' +
                    '</tr>';

                $('#supStages').append(tr);
            }
            $(document).on('click', '.removeExtra', function () {
                $(this).parent().parent().remove();
            });
        //============================================================
        $('#add-more-category').on('click', function () {
            $('#article-category-show').modal();
        });

        //==============================================================
        $('.btn-save-category').on('click', function () {
            var name = $('#new-category').val();
            $.post("{{ route('postInsertArticleCategory') }}", { name:name}, function (data) {
                $('#category_id  ').append($("<option/>",{
                    value: data.id,
                    text: data.name
                }))
            });
            $( '#form-category' ).each(function(){
                this.reset();
            });
            $('#new-category').focus();
        });
        //==============================================================
        $('#add-more-client').on('click', function () {
            $('#client-show').modal();
        });
        //==============================================================
        $('.btn-save-client').on('click', function () {
            var name = $('#client').val();
            $.post("{{ route('postInsertClient') }}", { name:name}, function (data) {
                $('#client_id ').append($("<option/>",{
                    value: data.id,
                    text: data.name
                }))
            });
            $( '#form_client' ).each(function(){
                this.reset();
            });
            $('#client').focus();
        });
        //===================================================================
        $(function () {
            $("#btnDeleteCategory").bind("click", function () {
                var id = $( "#category_id" ).val();
                var dataId = {'id':id};

                $.ajax
                ({
                    type: 'POST',
                    url: '{!! URL::route('deleteCategory') !!}',
                    dataType: 'json',
                    data: dataId,
                    success: function (data) {
                        if(data.msg)
                        {
                            $("#category_id option:selected").remove();
                            alert('Stergere cu succes.');
                        }
                    },
                    error: function() {
                        alert("Nu puteti sterge. Categoria exista in cel putin un articol.");
                    }
                });
            });
        });
        //===============================================================
        $(function () {
            $("#btnDeleteClient").bind("click", function () {
                var id = $( "#client_id" ).val();
                var dataId = {'id':id};

                $.ajax
                ({
                    type: 'POST',
                    url: '{!! URL::route('deleteClient') !!}',
                    dataType: 'json',
                    data: dataId,
                    success: function (data) {
                        if(data.msg)
                        {
                            $("#client_id option:selected").remove();
                            alert('Stergere cu succes.');
                        }
                    },
                    error: function() {
                        alert("Nu puteti sterge. Clientul exista in cel putin un articol.");
                    }
                });
            });
        });
        //===============================================================
        $('#category_id').select2({ placeholder: '--selecteaza o categorie--', width: '100%'});
        $('#client_id').select2({ placeholder: '--selecteaza un client--', width: '100%'});
        //==========================================================================================
        $('#article-category-show').on('shown.bs.modal', function() {
            $('#new-category').focus();
        });
        //==========================================================================================
        $('#client-show').on('shown.bs.modal', function() {
            $('#client').focus();
        });
        //==========================================================================================
        $('#process-show').on('shown.bs.modal', function() {
            $('#new-process').focus();
        });
        //==============================================================
        $('#add-more-process').on('click', function () {
            $('#process-show').modal();
        });
        //==============================================================
        $('.btn-save-process').on('click', function () {
            var process = $('#new-process').val();
            $.post("{{ route('postInsertProcess') }}", { name:process}, function (data) {
                $('#process_id ').append($("<option/>",{
                    value: data.id,
                    text: data.name
                }))
            });
            $( '#form-process' ).each(function(){
                this.reset();
            });
            $('#new-process').focus();
        });
        $('.close-modal').on('click', function () {
            location.reload();
        });
        //===============================================================
        $(function() {
            $( "#reqStages" ).sortable();
        });
        $(function() {
            $( "#supStages" ).sortable();
        });
        //===============================================================

        function checkName()
        {
            var name=document.getElementById( "name" ).value;

            if(name)
            {
                $.ajax({
                    type: 'post',
                    url: '{!! URL::route('verifName') !!}',
                    data: {
                        name:name
                    },
                    success: function (response) {
                        if(response.msg === "OK")
                        {
                            $( '#name_message' ).html("");
                            document.getElementById("submitFrm").disabled = false;
                            return true;
                        }
                        else
                        {
                            $( '#name_message' ).html(response.msg);
                            document.getElementById("submitFrm").disabled = true;
                            return false;
                        }
                    }
                });
            }
            else
            {
                $( '#name_message' ).html("");
                return false;
            }
        }

    </script>
@endsection

<style>
    #add-more-category:hover {color: #0055b3;}
    #add-more-client:hover {color: #0055b3;}
    #add-more-process:hover {color: #0055b3;}

    input[type="file"] {
        display: block;
    }

    #del {
        background-color: #ff6666;
        color: white;
        border: none;

    }
    #del:hover {
        background-color: #ff0000;
    }

    .btnDelete {
        background: transparent;
        text-decoration: none;
        border: none;
        color: #ffffff;
        padding: 0;
        font-size: 20px;
    }
</style>