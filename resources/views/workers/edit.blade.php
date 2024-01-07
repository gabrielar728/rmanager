@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <h3 class="page-header"><i class="fa fa-file-text-o"></i> Personal</h3>
            <ol class="breadcrumb">
                <li><i class="fa fa-home"></i><a href="{{ route('home') }}">Acasa</a></li>
                <li><i class="fa fa-users"></i>Personal</li>
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
        {!! Form::model($worker, [ 'route' => ['workers.update', $worker->id], 'method' => 'PATCH']) !!}
        <div class="col-lg-12">
            <section class="panel panel-default">
                <header class="panel-heading">
                    <span class="panel-title">Editare</span>
                    <a href="{{ route('administrare-personal') }}" class="btn btn-default pull-right" style="margin-top: 2px;">Inapoi</a>
                </header>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label style="font-weight: 600;">Nume</label>
                                {!! Form::text('first', null, array('placeholder' => 'Nume','class' => 'form-control', 'name' => 'first')) !!}
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label style="font-weight: 600;">Prenume</label>
                                {!! Form::text('last', null, array('placeholder' => 'Prenume','class' => 'form-control', 'name' => 'last' )) !!}
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label style="font-weight: 600;">Numar Card</label>
                                {!! Form::text('card', null, array('placeholder' => 'Numar Card','class' => 'form-control', 'name' => 'card', ' maxlength' => '11')) !!}
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label for="status" style="font-weight: 600;">Status</label>
                                <select class="form-control" id="status" name="status">
                                    <option value="{{ $worker->status }}" selected>
                                        @if ($worker->status == 0)
                                            <span>inactiv</span>
                                        @else
                                            <span>activ</span>
                                        @endif
                                    </option>
                                    @if ($worker->status == 0)
                                        <option value="1">activ</option>
                                    @else
                                        <option value="0">inactiv</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-footer">
                    <div class="form-group" style="margin-bottom: 0;">
                        <a href="{{route('administrare-personal')}}" class="btn btn-default">Anuleaza</a>
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