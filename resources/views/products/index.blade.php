@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <h3 class="page-header"><i class="fa fa-file-text-o"></i> Productie</h3>
            <ol class="breadcrumb">
                <li><i class="fa fa-home"></i><a href="{{ route('home') }}">Acasa</a></li>
                <li><i class="fa fa-database"></i>Productie</li>
                <li><i class="fa fa-plus-circle"></i>Lansare</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <section class="panel panel-default">
                <header class="panel-heading">
                    <span class="panel-title">Adaugare</span>
                </header>
                <form class="form-horizontal" id="frm-create-product" onsubmit="get_action(this);">
                    <div class="panel-body" style="border-bottom: 1px solid #ccc;">
                        <div class="row" style="margin-bottom: 10px;">
                            <div class="col-lg-2" style="margin-left: 5px;">
                                <input type="radio" id="group_check"
                                       name="item_check" value="groups" checked />
                                <label for="group_check">Grupuri</label>
                            </div>

                            <div class="col-lg-2">
                                <input type="radio" id="article_check"
                                       name="item_check" value="articles" />
                                <label for="article_check">Articole</label>
                            </div>

                            <div class="col-lg-2">
                                <input type="radio" id="both_check"
                                       name="item_check" value="both" />
                                <label for="both_check">Toate</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <label id="item_label" for="item_id" style="font-weight: 600;">Lista Grupuri <span style="color: red;">*</span></label>
                                <select name="item_id" class="form-control item_id" id="item_id" onchange="displayVals(this.value)" required>
                                    <option value="">--selecteaza un grup--</option>
                                    @foreach($groups as $key => $group)
                                        <option value="{{ $group->id }}" name="group">{{ $group->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-sm-3" style="margin-bottom: 10px;">
                                <label for="worker_id" style="font-weight: 600;">Lista Lucratori <span style="color: red;">*</span></label>
                                <select name="worker_id" class="form-control worker_id" id="worker_id" required>
                                    <option value="">--selecteaza un lucrator--</option>
                                    @foreach($workers as $key => $worker)
                                        <option value="{{ $worker->id }}">{{ $worker->first }} {{ $worker->last }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-sm-3">
                                <label for="production_date" style="font-weight: 600;">Data Productie <span style="color: red;">*</span></label>
                                <input type="date" id="production_date" name="production_date" class="form-control" required>
                            </div>
                        </div>


                    </div>

                    <div class="panel-footer">
                        {{ csrf_field() }}
                        <button type="submit" class="btn btn-default btn-sm">Lansare</button>
                    </div>
                </form>

                <div class="panel panel-default">
                    <div class="panel-heading">Informatii Lansari in Productie</div>
                    <div class="panel-body table-responsive" id="add-product-info">

                    </div>
                </div>
            </section>
        </div>
        <div class="col-lg-4">
            <section class="panel panel-default">
                <header class="panel-heading">
                    <span class="panel-title">Detalii Item Selectat</span>
                </header>
                <div class="panel-body" style="padding-bottom: 0;">
                    <div id="items">
                    </div>
                </div>
            </section>
        </div>
    </div>


@endsection

@section('scripts')

    <script type="text/javascript">
        function get_action(form) {
            var item_name = $('#item_id').find('option:selected').attr("name");
            var url = '{{ route("createProduct", ":item_name") }}';
            url = url.replace(':item_name', item_name);
            form.action = url;
        }
    //======================================================================
        $(document).ready(function () {
            showProductInfo();
        //-------------------------------------------------
            $('input:radio[name="item_check"]').change(function(){
                if($(this).val() === 'groups'){
                    displayGroups()
                } else if($(this).val() === 'articles'){
                    displayArticles();
                } else if($(this).val() === 'both'){
                    displayGroupsAndArticles()
                }
            });
        });
    //======================================================================
        $('#frm-create-product').on('submit', function (e) {
            e.preventDefault();
            var data = $(this).serialize();
            var url = $(this).attr('action');

            $.post(url,data,function(data){
                showProductInfo();
            });
            $(this).trigger('reset');
            $("#worker_id").val(0).trigger('change');
            displayGroups();
            $("#items").html("");
        });
    //======================================================================
        function showProductInfo() {
            var data = $('#frm-create-product').serialize();
            $.get("{{ route('showProductInformation') }}", data, function (data) {
                $('#add-product-info').empty().append(data);

            });
        }
    //======================================================================
        $('#item_id').select2({ placeholder: '--selecteaza un grup--', width: '100%'});
        $('#worker_id').select2({ placeholder: '--selecteaza un lucrator--', width: '100%'});
    //======================================================================
        function displayVals(data)
        {
            var id = data;
            var item_name = $('#item_id').find('option:selected').attr("name");
            $.ajax({
                type: "POST",
                url: "afisare-articol",
                data: { 'id':id, 'item_name':item_name },
                success:function(articles)
                {
                    $("#items").html(articles);
                }
            });
        }
        //-------------------------------------------------------------------
            function displayGroups() {
                $('#item_id').select2({ placeholder: '--selecteaza un grup--', width: '100%'});
                var id = $('#item_id').val();
                var options = '<option value="">--selecteaza un grup--</option>';

                $.ajax({
                    type: 'get',
                    url: '{!! URL::route('showGroups') !!}',
                    dataType: 'json',
                    data: {'id': id},
                    success: function (data) {

                        $.each(data.groups, function (i, group) {
                            options += '<option value="' + group.id + '" name="group">' + group.name + '</option>';
                        });
                        $('#item_id').html(options);

                    },
                    error: function () {
                        alert('A aparut o problema. Va rog sa dati refresh la aplicatie.')
                    }
                });
                document.getElementById('item_label').innerHTML = 'Lista Grupuri';
            }
        //----------------------------------------------------------------------
            function displayArticles() {
                $('#item_id').select2({ placeholder: '--selecteaza un articol--', width: '100%'});
                var id = $('#item_id').val();
                var options = '<option value="">--selecteaza un articol--</option>';

                $.ajax({
                    type: 'get',
                    url: '{!! URL::route('showArticle') !!}',
                    dataType: 'json',
                    data: {'id': id},
                    success: function (data) {

                        $.each(data.articles, function (i, article) {
                            options += '<option value="' + article.id + '" name="article">' + article.name + '</option>';
                        });
                        $('#item_id').html(options);

                    },
                    error: function () {
                        alert('A aparut o problema. Va rog sa dati refresh la aplicatie.')
                    }
                });
                document.getElementById('item_label').innerHTML = 'Lista Articole';
            }
        //-----------------------------------------------------------------------------
            function displayGroupsAndArticles() {
                $('#item_id').select2({ placeholder: '--selecteaza un grup sau articol--', width: '100%'});
                var id = $('#item_id').val();
                var options =  '<option value="">--selecteaza un grup sau articol--</option>';

                $.ajax({
                    type: 'get',
                    url: '{!! URL::route('showGroupsArticles') !!}',
                    dataType: 'json',
                    data: {'id':id},
                    success: function (data) {

                        $.each(data.groups, function (i, group) {
                            options += '<option value="'+group.id+'" name="group">'+group.name+'</option>';
                        });

                        $.each(data.articles, function (i, article) {
                            options += '<option value="'+article.id+'" name="article">'+article.name+'</option>';
                        });
                        $('#item_id').html(options);

                    },
                    error: function () {
                        alert('A aparut o problema. Va rog sa dati refresh la aplicatie.')
                    }
                });
                document.getElementById('item_label').innerHTML = 'Lista Grupuri & Articole';
            }


    </script>
@endsection

<style>
    .form-inline {
        display: inline-block;
    }

    .panel.panel-default .panel-footer {
        background: #f5f5f5;
    }

    #frm-create-product {
        margin-bottom: 0;
    }

</style>