@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <h3 class="page-header"><i class="fa fa-file-text-o"></i> Grupuri</h3>
            <ol class="breadcrumb">
                <li><i class="fa fa-home"></i><a href="{{ route('home') }}">Acasa</a></li>
                <li><i class="fa fa-cubes"></i>Grupuri</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <section class="panel panel-default">
                <header class="panel-heading">
                    <span class="panel-title">Adaugare Grup</span>
                </header>
                <form id="frm-create-group" class="form-horizontal" style="margin-bottom: 0;">
                    <div class="panel-body" style="border-bottom: 1px solid #ccc;">
                        <div class="form-group">
                            <div class="col-sm-1" style="margin-bottom: 5px; margin-top: 5px;">
                                <label for="name"><strong style="font-size: 16px;">Denumire:</strong></label>
                            </div>
                            <div class="col-sm-3" style="margin-bottom: 5px;">
                                <input type="text" class="form-control" name="name" id="name" onkeyup="checkName();" required autofocus>
                                <span id="name_message" style="color: #e02b27; font-size: 1.2rem;"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="col-sm-2" style="margin-bottom: 5px; margin-top: 5px;">
                                        <label for="articles" style="font-size: 16px;">Articole:</label>
                                    </div>
                                    <div class="col-sm-10" style="margin-bottom: 5px;">
                                        <div class="customTitle" style="background-color: #ECECEC; height: 25px; border: 1px solid #ccc;">
                                            <strong style="padding: 5px 5px 5px 15px;">Articole disponibile</strong>
                                        </div>
                                        <select name="articles[]" id="articles" multiple class="form-control articles" style="height: 200px; font-size: 16px;">
                                            @foreach($articles as $key => $article)
                                                <option value="{!! $article->id !!}">{!! $article->name !!}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6" style="padding-left: 0;">
                                <div class="col-sm-1" style="text-align: center; padding-left: 0; padding-right: 0; margin-top: 12%;">

                                    <div class="input-group" style="width: 100% !important;">
                                        <button type="button" value="" style="width: 100%;" class="btn btn-xs btn-success" id="add"><i class="fa fa-arrow-right" style="font-size: 25px;"></i> </button>
                                    </div>
                                    <div class="input-group" style="padding-top:10px; width: 100% !important;">
                                        <button type="button" value="" style="width: 100%;" class="btn btn-xs btn-danger" id="remove"><i class="fa fa-arrow-left" style="font-size: 25px;"></i> </button>
                                    </div>
                                </div>
                                <div class="col-sm-11" style="margin-bottom: 5px;">
                                    <div class="customTitle" style="background-color: #ABC6E1; height: 25px; color: #ffffff; border: 1px solid #ccc;">
                                        <strong style="padding: 5px 5px 5px 15px;">Articole selectate</strong>
                                    </div>
                                    <select name="selectedArticles[]" id="selected_articles" class="form-control selected_articles" style="height: 232px; font-size: 16px;" size="10">

                                    </select>
                                    <div class="select_length" style="font-size: 12px; color: red; font-style: italic;">
                                        Adaugati minim 2 articole in lista.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{ csrf_field() }}
                    <div class="panel-footer">
                        <button type="submit" id="submitButton" class="btn btn-default btn-sm">Creare Grup</button>
                    </div>
                </form>

                <div class="panel panel-default">
                    <div class="panel-heading">Informatii Grupuri</div>
                    <div class="panel-body" id="add-group-info">

                    </div>
                </div>
            </section>
        </div>
    </div>


@endsection

@section('scripts')

    <script type="text/javascript">
        $('.select_length').hide();
        showDropdown();
        showGroupInfo();
    //=====================================================================
        $('#frm-create-group').on('submit', function (e) {
            e.preventDefault();
            var length = $('#selected_articles option').length;
            if(length >= 2) {
                addGroup();
                showGroupInfo();
                $('.select_length').hide();
                $('#selected_articles option').remove();
                $(this).trigger('reset');
            } else {
                $('.select_length').show();
            }
        });
    //=====================================================================
        function showGroupInfo() {
            var data = $('#frm-create-group').serialize();
            $.get("{{ route('showGroupInformation') }}", data, function (data) {
                $('#add-group-info').empty().append(data);
            });
        }
    //=====================================================================
        function addGroup() {
            var name = $('#name').val();
            var values_id = $("#selected_articles>option").map(function() { return $(this).val(); }).get();

            $.post("{{ route('createGroup') }}", {'name':name, 'values_id':values_id}, function (data) {
                location.reload();
            });
        }
        //=====================================================================
        $(document).ready(function(){
            $('#add').click(function() {
                return !$('#articles option:selected')
                    .remove().appendTo('#selected_articles');
            });
            //---------------------------------------------------
            $('#remove').click(function() {
                var id = $('#selected_articles').val();
                $.post("{{ route('articles_selected') }}", { id:id}, function (data) {
                    $('#articles').append($("<option/>",{
                        value: id,
                        text: data[0].name
                    }));
                });
                return !$('#selected_articles option:selected')
                    .remove();
            });
            //---------------------------------------------------
        });
        //=====================================================================
        function checkName()
        {
            var name=document.getElementById( "name" ).value;

            if(name)
            {
                $.ajax({
                    type: 'post',
                    url: '{!! URL::route('verifGroupName') !!}',
                    data: {
                        name:name
                    },
                    success: function (response) {
                        if(response.msg === "OK")
                        {
                            $( '#name_message' ).html("");
                            document.getElementById("submitButton").disabled = false;
                            return true;
                        }
                        else
                        {
                            $( '#name_message' ).html(response.msg);
                            document.getElementById("submitButton").disabled = true;
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
        //=====================================================================
        function showDropdown() {
            var list = $("#articles").select2({
                placeholder: 'Cauta in articole...',
                width: '100%',
                closeOnSelect: false
            }).on("select2:closed", function(e) {
                list.select2("open");
            });
            //list.select2("open");
        }
    </script>
@endsection

<style>
    .panel.panel-default .panel-footer {
        background: #f5f5f5;
    }
    .input-group-addon {
        padding: 0px 10px 0px 0px !important;
        background-color: #ffffff !important;
        border: 0 !important;
    }
    .form-control {
        border-radius: 0 !important;
    }

    /*option:before { content: "☐ " }
    option:checked:before { content: "☑ " }*/

    td.details-control {
        background: url('../images/details_open.png') no-repeat center center;
        cursor: pointer;
    }
    tr.shown td.details-control {
        background: url('../images/details_close.png') no-repeat center center;
    }

    table.dataTable tbody th, table.dataTable tbody td {
        padding-top: 0 !important;
    }

</style>