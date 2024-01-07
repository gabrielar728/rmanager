@extends('layouts.raspberryPi.pump')

@section('content')

@endsection
<div class="row">
    <div class="col-lg-12">

        <div class="panel panel-default">
            <header class="panel-heading" style="line-height: 40px; padding: 0 15px; min-height: 34px;">
                <span class="panel-title" style="margin-top: 0; margin-bottom: 0;"><strong>{{ Auth::user()->last }} {{ Auth::user()->first }}</strong> - Lista Produse</span>
                <a href="{{ route('raspberryPiWorker.logout') }}" class="btn btn-default pull-right" style="margin-top: 3px;"
                   onclick="event.preventDefault();
                                   document.getElementById('logout-form').submit();">Anulare</a>
                <form id="logout-form" action="{{ route('raspberryPiWorker.logout') }}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                </form>
            </header>
            <div class="panel-body" style="margin-bottom: 0; margin-top: 0px; text-align: center;">
                @if(session()->has('message'))
                    <div class="alert alert-danger alert-block" style="position: absolute; width: 93%;">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <strong>{{ session('message') }}</strong>
                    </div>
                @endif

                @if(count($products) === 0)
                    <p>Nu exista niciun produs lansat.</p>
                @else
                    <table class="table table-striped" style="margin-bottom: 0;">
                        <thead>
                        <tr>
                            <th style="font-size: 12px; padding-left: 10px;">Nr. <br> crt.</th>
                            <th style="padding-left: 10px;">Denumire</th>
                            <th style="padding-left: 10px;">Client</th>
                            <th style="padding-left: 10px;">Status</th>
                            <th style="padding-left: 10px;">Data Productie</th>
                            <th style="padding-left: 10px; text-align: center; width: 220px;">Actiune</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $i=0;
                        ?>
                        @foreach($products as $key => $product)
                            <tr style="margin-top: 10px;">
                                <td style="padding-left: 10px; vertical-align: middle;">{{ ++$i}}</td>
                                <td style="padding-left: 10px; vertical-align: middle;">{{ $product->name }}</td>
                                <td style="padding-left: 10px; vertical-align: middle;">{{ $product->client }}</td>
                                <td style="padding-left: 10px; vertical-align: middle;">
                                    @if ($product->status_id == 1)
                                        <span style="color: blue;">nou</span>
                                    @elseif ($product->status_id == 2)
                                        <span style="color: orange;">in lucru</span>
                                    @endif
                                </td>
                                <td style="padding-left: 10px; vertical-align: middle;">
                                    @if(Carbon\Carbon::parse($product->production_date)->setTime(23,59,59) < Carbon\Carbon::now()->setTime(7,30) && ($product->status_id == 1 || $product->status_id == 2))
                                        <span style="color: red;">{{ Carbon\Carbon::parse($product->production_date)->format('d.m.Y') }}</span>
                                    @elseif($product->production_date == NULL)
                                        <span> - </span>
                                    @else
                                        <span>{{ Carbon\Carbon::parse($product->production_date)->format('d.m.Y') }}</span>
                                    @endif
                                </td>
                                <td style="padding-left: 10px; vertical-align: middle;">
                                    @if($product->dosages >= $product->rows)
                                        <a href="{{ route('finishProduct', $product->product_id) }}" class="btn btn-success btn-lg" onclick="return confirm('Finalizati produsul?')" style="width: 110px;">Finalizare</a>
                                        <a href="{{ URL::route('moreResin', [$product->product_id, Auth::user()->id] )}}" class="btn btn-danger btn-lg" onclick="return confirm('Vreti sa turnati mai multa rasina?')" style="width: 87px;">Extra</a>
                                    @else
                                        <a href="{{ URL::route('selectWorkers', [$product->product_id, Auth::user()->id] )}}" class="btn btn-primary btn-lg" onclick="return confirm('Alegeti acest produs?')" style="width: 100%;">Alege</a>
                                    @endif

                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @endif
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