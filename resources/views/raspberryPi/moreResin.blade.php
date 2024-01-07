@extends('layouts.raspberryPi.pump')

@section('content')

@endsection
<div class="row">
    <div class="col-lg-12">

        <div class="panel panel-default">
            <header class="panel-heading" style="line-height: 40px; padding: 0 15px; min-height: 34px;">
                <span class="panel-title" style="margin-top: 0; margin-bottom: 0;"><strong>{{ $worker_lastname }} {{ $worker_firstname }}</strong> - Completare</span>
                <a href="{{ route('raspberryPiWorker.logout') }}" class="btn btn-default pull-right" style="margin-top: 3px;"
                   onclick="event.preventDefault();
                                   document.getElementById('logout-form').submit();">Anulare</a>
                <form id="logout-form" action="{{ route('raspberryPiWorker.logout') }}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                </form>
            </header>
            <div class="panel-body" style="margin-bottom: 0;">
                <form method="post" action="{{ route('productExtraDosage', $product_id) }}" id="productExtraDosage" name="productExtraDosage">
                    <h3 style="text-align: center;">Alege o cantitate:</h3>
                    <div style="margin-left: 45%;">
                        @foreach($products as $product)
                            <input type="radio" class="input-control more" value="{{ $product->quantity }}" id="more" name="more" style="height:20px; width:20px; margin-right: 5px;" required><span style="font-size: 30px;">{{ $product->quantity }}</span><span style="font-size: 20px;">{{ $product->material['unit'] }}</span> <br>
                        @endforeach
                    </div>
                    <div class="form-group" style="margin-top: 20px; text-align: center;">
                        <button class="btn btn-primary btn-lg dosing" id="dosing" style="width: 27%;">Turnare</button>
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