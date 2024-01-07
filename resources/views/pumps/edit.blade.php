@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <h3 class="page-header"><i class="fa fa-file-text-o"></i> Pompe</h3>
            <ol class="breadcrumb">
                <li><i class="fa fa-home"></i><a href="{{ route('home') }}">Acasa</a></li>
                <li><i class="fa fa-sitemap"></i>Pompe</li>
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
        {!! Form::model($pump, [ 'route' => ['pumps.update', $pump->id], 'method' => 'PATCH']) !!}
        <div class="col-lg-12">
            <section class="panel panel-default">
                <header class="panel-heading">
                    <span class="panel-title">Editare Pompa</span>
                    <a href="{{ route('administrare-pompe') }}" class="btn btn-default pull-right" style="margin-top: 2px;">Inapoi</a>
                </header>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label style="font-weight: 600;">Nume</label>
                                {!! Form::text('name', null, array('placeholder' => 'Nume','class' => 'form-control', 'name' => 'name')) !!}
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label style="font-weight: 600;">Locatie</label>
                                {!! Form::text('location', null, array('placeholder' => 'Locatie','class' => 'form-control', 'name' => 'location' )) !!}
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label style="font-weight: 600;">IP</label>
                                {!! Form::text('ip', null, array('placeholder' => 'IP','class' => 'form-control', 'name' => 'ip' )) !!}
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label style="font-weight: 600;">Port</label>
                                {!! Form::text('port', null, array('placeholder' => 'Port','class' => 'form-control', 'name' => 'port' )) !!}
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="material_id" style="font-weight: 600;">Material</label>
                                <select class="form-control material_id" id="material_id" name="material_id">
                                    @foreach($materials as $material)
                                        <option
                                                value="{{$material->id}}"
                                                @if($material->id === $pump->material_id)
                                                selected
                                                @endif>
                                            {{$material->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
						<div class="col-sm-2">
                            <div class="form-group">
                                <label style="font-weight: 600;">Numar Pompe/UM</label>
                                {!! Form::number('ratio', null, array('placeholder' => 'Numar Pompe/UM','class' => 'form-control', 'name' => 'ratio', 'min'=>'1' )) !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-footer">
                    <div class="form-group" style="margin-bottom: 0;">
                        <a href="{{route('administrare-pompe')}}" class="btn btn-default">Anuleaza</a>
                        {{ Form::button('<i class="fa fa-floppy-o"></i> Actualizeaza', ['type' => 'submit', 'class' => 'btn btn-primary'] )  }}
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

    </script>
@endsection

<style>
    strong {color: #333333;}

</style>