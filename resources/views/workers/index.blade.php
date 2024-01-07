@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <h3 class="page-header"><i class="fa fa-file-text-o"></i> Personal</h3>
            <ol class="breadcrumb">
                <li><i class="fa fa-home"></i><a href="{{ route('home') }}">Acasa</a></li>
                <li><i class="fa fa-users"></i>Personal</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <section class="panel panel-default" style="margin-bottom: 0;">
                <header class="panel-heading">
                    <span class="panel-title">Adaugare Personal</span>
                </header>
                <form action="{{ route('createWorker') }}" class="form-horizontal" method="POST" id="frm-create-worker" style="margin: 0;">
                    <div class="panel-body" style="border-bottom: 1px solid #ccc;">
                        <div class="form-group">
                            <div class="col-lg-4" style="margin-bottom: 10px;">
                                <label for="first" style="font-weight: 600;">Nume</label>
                                <input type="text" class="form-control" name="first" id="first" required>
                            </div>
                            <div class="col-lg-4" style="margin-bottom: 10px;">
                                <label for="last" style="font-weight: 600;">Prenume</label>
                                <input type="text" class="form-control" name="last" id="last" required>
                            </div>
                            <div class="col-lg-4" style="margin-bottom: 10px;">
                                <label for="card" style="font-weight: 600;">Numar Card</label>
                                <input type="text" class="form-control" name="card" id="card"  maxlength="11" onkeyup="checkCard();" required>
                                <span id="card_status" style="color: #e02b27; font-size: 1.2rem;"></span>
                            </div>
                        </div>
                    </div>

                    <div class="panel-footer">
                        {{ csrf_field() }}
                        <button type="submit" id="submitFrm" class="btn btn-default btn-sm">Creare</button>
                    </div>
                </form>
            </section>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">Informatii Personal</div>
                <div class="panel-body" id="add-workers-info">

                </div>
            </div>
        </div>
    </div>



@endsection

@section('scripts')

    <script type="text/javascript">
        showWorkersInfo();
        //=====================================================================

        $('#frm-create-worker').on('submit', function (e) {
            e.preventDefault();
            var data = $(this).serialize();
            var url = $(this).attr('action');
            $.post(url,data,function(data){
                showWorkersInfo();
            });
            $(this).trigger('reset');
        });
        //=====================================================================
        function showWorkersInfo() {
            var data = $('#frm-create-worker').serialize();
            $.get("{{ route('showWorkerInformation') }}", data, function (data) {
                $('#add-workers-info').empty().append(data);

            })
        }
        //====================================================================
            $('#frm-create-worker').on('keyup keypress', function(e) {
            var keyCode = e.keyCode || e.which;
            if (keyCode === 13) {
                e.preventDefault();
                return false;
            }
        });
        //====================================================================
        function checkCard(form)
        {
            var card=document.getElementById( "card" ).value;

            if(card)
            {
                $.ajax({
                    type: 'post',
                    url: '{!! URL::route('verifCard') !!}',
                    data: {
                        card:card
                    },
                    success: function (response) {
                        if(response.msg === "OK")
                        {
                            $( '#card_status' ).html("");
                            document.getElementById("submitFrm").disabled = false;
                            return true;
                        }
                        else
                        {
                            $( '#card_status' ).html(response.msg);
                            document.getElementById("submitFrm").disabled = true;
                            return false;
                        }
                    }
                });
            }
            else
            {
                $( '#card_status' ).html("");
                return false;
            }
        }



    </script>
@endsection

<style>
    .form-inline {
        display: inline-block;
    }

    .panel.panel-default .panel-footer {
        background: #f5f5f5;
    }

    #frm-create-personal {
        margin-bottom: 0;
    }

</style>