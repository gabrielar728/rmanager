@extends('layouts.raspberryPi.pump')

@section('content')

@endsection
<div class="row">
    <div class="col-lg-12">

        <div class="panel panel-default">
            <header class="panel-heading" style="line-height: 40px; padding: 0 15px; min-height: 34px;">
                <span class="panel-title"
                      style="margin-top: 0; margin-bottom: 0;"><strong>{{ Auth::user()->last }} {{ Auth::user()->first }}</strong> - Lista Produse</span>
                <a href="{{ route('raspberryPiWorker.logout') }}" class="btn btn-default pull-right"
                   style="margin-top: 3px;"
                   onclick="event.preventDefault();
                                   document.getElementById('logout-form').submit();">Anulare</a>
                <form id="logout-form" action="{{ route('raspberryPiWorker.logout') }}" method="POST"
                      style="display: none;">
                    {{ csrf_field() }}
                </form>
            </header>
            <div class="panel-body" style="margin-bottom: 0; margin-top: 0; text-align: center; overflow: auto;">
                @if(count($products) === 0)
                    <p>Nu exista niciun produs lansat.</p>
                @else
                    <div class="row">
                        <div class="col-xs-10">
                            <input type="text" class="form-control" id="search" name="search"
                                   placeholder="Cauta un produs prin scanarea unui cod de bare" autofocus
                                   autocomplete="off"/>
                            <p id="msg" style="color: red;"></p>
                        </div>
                        <div class="col-xs-2">
                            <button class="btn btn-warning btn-md" onclick="location.reload()">Reseteaza</button>
                        </div>
                    </div>

                    @if(session()->has('message'))
                        <div class="alert alert-danger alert-block m-bot15">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <strong>{{ session('message') }}</strong>
                        </div>
                    @endif

                    <table class="table" style="margin-bottom: 0;">
                        <thead>
                        <tr>
                            <th style="font-size: 12px; padding-left: 10px;">Nr. <br> crt.</th>
                            <th style="padding-left: 10px;">Denumire</th>
                            <th style="padding-left: 10px;">Client</th>
                            <th style="padding-left: 10px;">Status</th>
                            <th style="padding-left: 10px;">Saptamana</th>
                            <th style="padding-left: 10px; text-align: center; width: 220px;">Actiuni</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $i = 0;
                        ?>
                        @foreach($products as $key => $product)
                            <tr style="margin-top: 2%;">
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
                                    @if(($product->status_id == 1 || $product->status_id == 2) && (Carbon\Carbon::parse($product->production_date)->weekOfYear === Carbon\Carbon::now()->weekOfYear) && (Carbon\Carbon::parse($product->production_date)->year === Carbon\Carbon::now()->year ))
                                        <span style="color: green;">{!! Carbon\Carbon::parse($product->production_date)->year . ', ' . Carbon\Carbon::parse($product->production_date)->weekOfYear !!}</span>
                                    @else
                                        <span style="color: red;">{!! Carbon\Carbon::parse($product->production_date)->year . ', ' . Carbon\Carbon::parse($product->production_date)->weekOfYear !!}</span>
                                    @endif
                                </td>
                                <td style="vertical-align: middle;">
                                    @if($product->scanned_barcode == 0)
                                        <a href="{{ URL::route('barcodeScan', [$product->product_id, Auth::user()->id] )}}"
                                           class="btn btn-primary btn-lg">Scanare cod de bare</a>
                                    @endif
                                    @if($product->scanned_barcode == 1 && $product->status_id == 1)
                                        <h3 style="text-align: center"><span class="label label-default">Cod de bare scanat</span>
                                        </h3>
                                        {{--                                        <a href="{{ route('finishProduct', $product->product_id) }}"--}}
                                        {{--                                           class="btn btn-success btn-lg" style="margin-right: 5px;">Finalizare</a>--}}
                                        {{--                                        <a href="{{ URL::route('selectQuantity', [$product->product_id, Auth::user()->id] )}}"--}}
                                        {{--                                           class="btn btn-primary btn-lg">Alege</a>--}}
                                    @endif
                                    @if($product->scanned_barcode == 1 && $product->status_id == 2)
                                        <h3 style="text-align: center"><span
                                                    class="label label-default">Produs in lucru</span></h3>
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
        $('#search').on('keyup', function () {
            const $value = $(this).val();
            if ($value.endsWith('&st=1')) {
                $.ajax({
                    type: 'get',
                    url: '{!! URL::route('searchProducts') !!}',
                    dataType: 'json',
                    data: {'search': $value},
                    success: function (data) {
                        if (data?.msg) {
                            document.getElementById('msg').innerHTML = data.msg;
                            if (document.getElementById('search').value === '') {
                                document.getElementById('msg').innerHTML = '';
                            }
                        }
                        let product = '';
                        let finish_url = '{{ route('finishProduct', ":product_id") }}';
                        let choose_url = '{{ route('selectQuantity', [":product_id",":user_id"]) }}';
                        finish_url = finish_url.replace(':product_id', data.product.id);
                        choose_url = choose_url.replace(':product_id', data.product.id).replace(':user_id', data.product.user_id);

                        product += data.output +
                            '<td style="vertical-align: middle;"><a class="btn btn-success btn-lg finish_confirm" href="' + finish_url + '" style="margin-right: 9px;">Finalizare</a><a class="btn btn-primary btn-lg choose_confirm" href="' + choose_url + '">Alege</a></td>' +
                            '</tr>';
                        $('tbody').html(product);

                        document.getElementById('msg').innerHTML = '';
                        document.getElementById('search').value = '';
                        $('#search').blur();

                    }
                });
            }
        })
    </script>
@endsection

<style>

</style>