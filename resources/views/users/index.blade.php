@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <h3 class="page-header"><i class="fa fa-file-text-o"></i> Utilizatori</h3>
            <ol class="breadcrumb">
                <li><i class="fa fa-home"></i><a href="{{ route('home') }}">Acasa</a></li>
                <li><i class="fa fa-adn"></i>Administrare</li>
                <li><i class="fa fa-plus-circle"></i>Adaugare Utilizatori</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <section class="panel panel-default">
                <header class="panel-heading">
                    <span class="panel-title">Adaugare Utilizator</span>
                </header>
                <form action="{{ route('createUser') }}" class="form-horizontal" method="POST" id="frm-create-user">
                    <div class="panel-body" style="border-bottom: 1px solid #ccc;">
                        <div class="form-group">
                            <div class="col-sm-3" style="margin-bottom: 10px;">
                                <label for="first_name" style="font-weight: 600;">Nume</label>
                                <input type="text" class="form-control" name="first_name" id="first_name" required>
                            </div>
                            <div class="col-sm-3" style="margin-bottom: 10px;">
                                <label for="last_name" style="font-weight: 600;">Prenume</label>
                                <input type="text" class="form-control" name="last_name" id="last_name" required>
                            </div>
                            <div class="col-sm-3" style="margin-bottom: 10px;">
                                <label for="email" style="font-weight: 600;">Email</label>
                                <input type="email" class="form-control" name="email" id="email" required>
                            </div>
                            <div class="col-sm-3" style="margin-bottom: 10px;">
                                <label for="password" style="font-weight: 600;">Parola</label>
                                <input type="text" class="form-control" name="password" id="password" required>
                            </div>
                        </div>
                    </div>

                    <div class="panel-footer">
                        {{ csrf_field() }}
                        <button type="submit" class="btn btn-default btn-sm">Creare Utilizator</button>
                    </div>
                </form>

            </section>
        </div>
    </div>


@endsection

@section('scripts')

    <script type="text/javascript">


    </script>
@endsection

<style>
    .form-inline {
        display: inline-block;
    }

    .panel.panel-default .panel-footer {
        background: #f5f5f5;
    }

    #frm-create-user {
        margin-bottom: 0;
    }


</style>