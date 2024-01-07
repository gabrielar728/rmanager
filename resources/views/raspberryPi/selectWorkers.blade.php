@extends('layouts.raspberryPi.pump')

@section('content')

@endsection
<div class="row">
    <div class="col-lg-12">

        <div class="panel panel-default">
            <header class="panel-heading" style="line-height: 40px; padding: 0 15px; min-height: 34px;">
                <span class="panel-title" style="margin-top: 0; margin-bottom: 0;"><strong>{{ $worker_lastname }} {{ $worker_firstname }}</strong> - Selectare lucrători</span>
                <a href="{{ route('raspberryPiWorker.logout') }}" class="btn btn-default pull-right" style="margin-top: 3px;"
                   onclick="event.preventDefault();
                                   document.getElementById('logout-form').submit();">Anulare</a>
                <form id="logout-form" action="{{ route('raspberryPiWorker.logout') }}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                </form>
            </header>
            <div class="panel-body" style="margin-bottom: 0;">
                <h3 style="text-align: left; margin-bottom: 20px;">Alege numărul persoanelor care vor lucra la acest produs:</h3>
                <form method="POST" action="{{ route('addSelectedWorkers', [$id, Auth::user()->id]) }}" id="addSelectedWorkers" name="addSelectedWorkers">
                    @for($i=1; $i<=9; $i++)
                        <div class="col-sm-4"><input type="radio" class="input-control more" value="{{ $i }}" id="workers" name="workers" style="height:50px; width:50px; margin-right: 5px;" required><span style="font-size: 50px;">{{ $i }}</span></div>
                    @endfor
                    <div class="form-group" style="text-align: left;">
                        <button class="btn btn-primary btn-lg dosing" id="dosing" style="width: 85%; margin-top: 20px;">Turnare</button>
                    </div>
                    {{csrf_field()}}
                </form>
            </div>
        </div>

    </div>
</div>
@section('scripts')

    <script type="text/javascript">


    </script>
@endsection

<style>

</style>