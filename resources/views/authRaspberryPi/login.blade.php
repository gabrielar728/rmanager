@extends('layouts.raspberryPi.pump')

@section('content')
    <div class="row">
        <form method="POST" action="{{ route('raspberryPiWorker.login.submit') }}" >
            {{ csrf_field() }}
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <header class="panel-heading" style="line-height: 40px; padding: 0 15px; min-height: 34px;">
                        <span class="panel-title" style="margin-top: 0; margin-bottom: 0;">Logare</span>
                    </header>
                    <div class="panel-body" style="height: 380px; margin-bottom: 20px;">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="multi-field-wrapper">
                                    <div class="multi-fields">
                                        <div class="multi-field" style="margin-bottom: 10px;">
                                            <div class="form-group{{ $errors->has('card') ? ' has-error' : '' }}">
                                                <input id="card" type="text" class="input-control card" name="card" value="{{ old('card') }}" placeholder="Scanati Cardul" required autofocus autocomplete="off">
                                                @if ($errors->has('card'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('card') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                    <div class="col-md-6">
                                        <input id="password" type="hidden" class="form-control" name="password" value="password" required>

                                        @if ($errors->has('password'))
                                            <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection


@section('scripts')

    <script type="text/javascript">
        $(document).click(function() {
            $( "#card" ).focus();
        });
    </script>
@endsection

<style>
    .form-group {
        margin-top: 37%;
        width: 68%;
        margin-left: 26%;
    }

    .panel-body {
        background-image: url("{{ asset('images/card.jpg')}}");
        background-repeat: no-repeat;
        background-position: center;
    }

    .panel-heading {
        font-size: 16px;
    }

    .card {
        width: 70%;
        height: 34px;
        padding: 6px 12px;
        font-size: 14px;
        line-height: 1.42857143;
        color: #555;
        background-color: #fff;
        background-image: none;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
        transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
    }

    .card:focus {
        background-color: transparent;
        border-radius: 4px;
    }

    label {
        font-size: 16px;
        padding-top: 10px;
    }


</style>