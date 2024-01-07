@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <h3 class="page-header"><i class="fa fa-file-text-o"></i> Materiale</h3>
            <ol class="breadcrumb">
                <li><i class="fa fa-home"></i><a href="{{ route('home') }}">Acasa</a></li>
                <li><i class="fa fa-sitemap"></i>Materiale</li>
                <li><i class="fa fa-file-text-o"></i>Editare</li>
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
        {!! Form::model($material, [ 'route' => ['materials.update', $material->id], 'method' => 'PATCH']) !!}
        <div class="col-lg-12">
            <section class="panel panel-default">
                <header class="panel-heading">
                    <span class="panel-title">Editare</span>
                    <a href="{{ route('administrare-materiale') }}" class="btn btn-default pull-right" style="margin-top: 2px;">Inapoi</a>
                </header>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label style="font-weight: 600;">Denumire</label>
                                {!! Form::text('name', null, array('placeholder' => 'Denumire','class' => 'form-control', 'name' => 'name')) !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-footer">
                    <div class="form-group" style="margin-bottom: 0;">
                        <a href="{{route('administrare-materiale')}}" class="btn btn-default">Anuleaza</a>
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