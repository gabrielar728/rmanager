@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <h3 class="page-header"><i class="fa fa-bell-o"></i> Notificari</h3>
            <ol class="breadcrumb">
                <li><i class="fa fa-home"></i><a href="{{ route('home') }}">Acasa</a></li>
                <li><i class="fa fa-bell-o"></i>Notificari</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <section class="panel panel-default">
                <header class="panel-heading">
                    <span class="panel-title">Configurare notificari</span>
                </header>
                <form action="{{ route('email.notifications') }}" class="form-horizontal" method="GET" id="frm-add-email-">
                    <div class="panel-body" style="border-bottom: 1px solid #ccc;">
                        <div class="form-group">
                            <div class="col-sm-6" style="margin-bottom: 10px;">
                                <label for="finish_product_email_notifications" style="font-weight: 600;">Adresa de e-mail pentru notificarea finalizarii unui produs</label>
                                <input type="email" value="{{ config('settings.finish_product_email_notifications') }}" class="form-control" name="finish_product_email_notifications" id="finish_product_email_notifications" autocomplete="off" required>
                            </div>
                        </div>
                    </div>

                    <div class="panel-footer">
                        {{ csrf_field() }}
                        <button type="submit" class="btn btn-default btn-sm">Salveaza</button>
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
    .panel.panel-default .panel-footer {
        background: #f5f5f5;
    }

    #frm-create-user {
        margin-bottom: 0;
    }


</style>