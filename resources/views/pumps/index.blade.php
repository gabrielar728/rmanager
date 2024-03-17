@extends('layouts.master')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <h3 class="page-header"><i class="fa fa-file-text-o"></i> Pompe</h3>
        <ol class="breadcrumb">
            <li><i class="fa fa-home"></i><a href="{{ route('home') }}">Acasa</a></li>
            <li><i class="fa fa-sitemap"></i>Pompe</li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <section class="panel panel-default">
            <header class="panel-heading">
                <span class="panel-title">Adaugare Pompa</span>
            </header>
            <form action="{{ route('createPump') }}" class="form-horizontal" method="POST" id="frm-create-pump">
                <div class="panel-body" style="border-bottom: 1px solid #ccc;">
                    <div class="form-group">
                        <div class="col-lg-6" style="margin-bottom: 10px;">
                            <label for="name" style="font-weight: 600;">Denumire</label>
                            <input type="text" class="form-control" name="name" id="name" required>
                        </div>
                        <div class="col-lg-3" style="margin-bottom: 10px;">
                            <label for="location" style="font-weight: 600;">Locatie</label>
                            <input type="text" class="form-control" name="location" id="location" required>
                        </div>
                        <div class="col-lg-3" style="margin-bottom: 10px;">
                            <label for="ip" style="font-weight: 600;">IP</label>
                            <input type="text" class="form-control" name="ip" id="ip" required>
                        </div>
                        <div class="col-lg-4" style="margin-bottom: 10px;">
                            <label for="port" style="font-weight: 600;">Port</label>
                            <input type="text" class="form-control" name="port" id="port" required>
                        </div>
                        <div class="col-lg-6" style="margin-bottom: 10px;">
                            <label for="material_id" style="font-weight: 600;">Material</label>
                            <select name="material_id" class="form-control material" id="material_id" required>
                                <option value="0" selected disabled>--selecteaza un material--</option>
                                @foreach($materials as $key => $material)
                                <option value="{!! $material->id !!}">{!! $material->name !!}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2" style="margin-bottom: 10px;">
                            <label for="ratio"><strong>Numar Pompe/U.M.</strong></label>
                            <input type="number" min="1" class="form-control" name="ratio" id="ratio" required autofocus>
                        </div>
                    </div>
                </div>

                <div class="panel-footer">
                    {{ csrf_field() }}
                    <button type="submit" class="btn btn-default btn-sm">Creare Pompa</button>
                </div>
            </form>

            <div class="panel panel-default">
                <div class="panel-heading">Informatii Pompe</div>
                <div class="panel-body" id="add-pump-info">

                </div>
            </div>
        </section>
    </div>
</div>


@endsection

@section('scripts')

<script type="text/javascript">
    showPumpInfo();
    //=====================================================================
    $('#frm-create-pump').on('submit', function(e) {
        e.preventDefault();
        var data = $(this).serialize();
        var url = $(this).attr('action');
        $.post(url, data, function(data) {
            showPumpInfo();
        });
        $('#material_id').val(0).trigger('change');
        $(this).trigger('reset');
    });
    //=====================================================================
    function showPumpInfo() {
        var data = $('#frm-create-pump').serialize();
        $.get("{{ route('showPumpInformation') }}", data, function(data) {
            $('#add-pump-info').empty().append(data);

        })
    }
    //====================================================================
    $('#material_id').select2({
        placeholder: 'cauta...',
        width: '100%'
    });
    //====================================================================
</script>
@endsection

<style>
    .form-inline {
        display: inline-block;
    }

    .panel.panel-default .panel-footer {
        background: #f5f5f5;
    }

    #frm-create-pump {
        margin-bottom: 0;
    }
</style>