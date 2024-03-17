@extends('layouts.raspberryPi.pump')

@section('content')

@endsection
<div class="row">
    <div class="col-lg-12">

        <div class="panel panel-default">
            <header class="panel-heading" style="line-height: 40px; padding: 0 15px; min-height: 34px;">
                <span class="panel-title" style="margin-top: 0; margin-bottom: 0;"><strong>{{ $worker_lastname }} {{ $worker_firstname }}</strong> - Selectare cantitate</span>
                <a href="{{ route('raspberryPiWorker.logout') }}" class="btn btn-default pull-right" style="margin-top: 3px;"
                   onclick="event.preventDefault();
                                   document.getElementById('logout-form').submit();">Anulare</a>
                <form id="logout-form" action="{{ route('raspberryPiWorker.logout') }}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                </form>
            </header>
            <div class="panel-body" style="margin-bottom: 0;">
                <form method="post" action="{{ route('productDosageOptions', [$product->id, Auth::user()->id]) }}" id="productDosageOptions" name="productDosageOptions">
                    <h3>Alege o cantitate pentru produsul <strong>{{ $product->article['name'] }}</strong>:</h3>
                    <div style="padding-left: 1rem">
                        @foreach($quantities as $item)
                            <input type="radio" class="input-control quantity" value="{{ $item->quantity }}" id="quantity" name="quantity" style="height:25px; width:25px; margin-right: 5px;" required><span style="font-size: 30px; line-height: 1.5;">{{ $item->quantity }}</span><span style="font-size: 20px;"> {{ $item->material['unit'] }} {{ $item->material['name'] }}</span> <br>
                        @endforeach
                    </div>
                    <div class="form-group" style="margin-top: 20px;">
                        <button class="btn btn-primary btn-lg dosing" id="dosing" style="width:100%;">Turnare</button>
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