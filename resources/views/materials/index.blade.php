@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <h3 class="page-header"><i class="fa fa-file-text-o"></i> Materiale</h3>
            <ol class="breadcrumb">
                <li><i class="fa fa-home"></i><a href="{{ route('home') }}">Acasa</a></li>
                <li><i class="fa fa-cubes"></i>Materiale</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <section class="panel panel-default">
                <header class="panel-heading">
                    <span class="panel-title">Adaugare Material</span>
                </header>
                <form action="{{ route('createMaterial') }}" class="form-horizontal" method="POST" id="frm-create-material">
                    <div class="panel-body" style="border-bottom: 1px solid #ccc;">
                        <div class="form-group">
                            <div class="col-sm-6" style="margin-bottom: 10px;">
                                <label for="name"><strong>Denumire</strong></label>
                                <input type="text" class="form-control" name="name" id="name" required autofocus>
                            </div>

                            <div class="col-sm-6" style="margin-bottom: 10px;">
                                <label for="unit"><strong>U.M.</strong></label>
                                <select class="form-control unit" name="unit" id="unit" required>
                                    <option class="0" value="">--selecteaza o unitate--</option>
                                    <option value="kg">kg</option>
                                    <option value="L">L</option>
                                    <option value="m2">m&sup2;</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="panel-footer">
                        {{ csrf_field() }}
                        <button type="submit" class="btn btn-default btn-sm">Creare Material</button>
                    </div>
                </form>

                <div class="panel panel-default">
                    <div class="panel-heading">Informatii Materiale</div>
                    <div class="panel-body" id="add-material-info">

                    </div>
                </div>
            </section>
        </div>
    </div>


@endsection

@section('scripts')

    <script type="text/javascript">
        showMaterialInfo();
    //=====================================================================
        $('#frm-create-material').on('submit', function (e) {
            e.preventDefault();
            var data = $(this).serialize();
            var url = $(this).attr('action');
            $.post(url,data,function(data){
                showMaterialInfo();
            });
            $(this).trigger('reset');
        });
    //=====================================================================
        function showMaterialInfo() {
            var data = $('#frm-create-material').serialize();
            $.get("{{ route('showMaterialInformation') }}", data, function (data) {
                $('#add-material-info').empty().append(data);
            })
        }
        //====================================================
    </script>
@endsection

<style>
    .form-inline {
        display: inline-block;
    }

    .panel.panel-default .panel-footer {
        background: #f5f5f5;
    }

    #frm-create-material {
        margin-bottom: 0;
    }

</style>