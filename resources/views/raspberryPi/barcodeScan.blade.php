@extends('layouts.raspberryPi.pump')

@section('content')

@endsection
<div class="row">
    <div class="col-lg-12">

        <div class="panel panel-default">
            <header class="panel-heading" style="line-height: 40px; padding: 0 15px; min-height: 34px;">
                <span class="panel-title"
                      style="margin-top: 0; margin-bottom: 0;"><strong>{{ $worker_lastname }} {{ $worker_firstname }}</strong> - Scanare cod de bare</span>
                <a href="{{ route('raspberryPiWorker.logout') }}" class="btn btn-default pull-right"
                   style="margin-top: 3px;"
                   onclick="event.preventDefault();
                                   document.getElementById('logout-form').submit();">Anulare</a>
                <form id="logout-form" action="{{ route('raspberryPiWorker.logout') }}" method="POST"
                      style="display: none;">
                    {{ csrf_field() }}
                </form>
            </header>
            <div class="panel-body" style="margin-bottom: 0;">
                <h4 style="text-align: left; margin-bottom: 20px;">Scaneaza codul de bare pentru
                    produsul <strong>{{ $product->article['name'] }}</strong>:</h4>
                <form method="POST" action="{{ route('addBarcodeFields', [$id, Auth::user()->id]) }}"
                      id="addBarcodeFields" name="addBarcodeFields">
                    @if(session()->has('message'))
                        <div class="alert alert-danger alert-block m-bot15">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <strong>{{ session('message') }}</strong>
                        </div>
                    @endif
                    <label for="bar_code_url"></label>
                    <textarea class="form-control bar_code_url" id="bar_code_url" name="bar_code_url"
                              placeholder="Scanati codul de bare aici" required autofocus autocomplete="off"></textarea>
                    {{csrf_field()}}
                </form>
            </div>
        </div>

    </div>
</div>
@section('scripts')

    <script type="text/javascript">
        $(document).ready(function () {
            $(function () {
                const barCode = $('#bar_code_url');
                barCode.keyup(function (e) {
                    e.preventDefault();
                    if (barCode.val().endsWith('st=1')) {
                        $(this.form).submit();
                    }
                });
            });
        });
    </script>
@endsection

<style>

</style>