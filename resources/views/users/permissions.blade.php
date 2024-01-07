@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <h3 class="page-header"><i class="fa fa-file-text-o"></i> Permisii</h3>
            <ol class="breadcrumb">
                <li><i class="fa fa-home"></i><a href="{{ route('home') }}">Acasa</a></li>
                <li><i class="fa fa-adn"></i>Administrare</li>
                <li><i class="fa fa-lock"></i>Permisii</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <section class="panel panel-default">
                <header class="panel-heading">
                    <span class="panel-title">Adaugare Permisii</span>
                </header>
                <div class="panel panel-body" style="margin-bottom: 0; padding-bottom: 10px;">
                    <table class="table table-striped table-hover" id="table-users-info">
                        <thead>
                        <tr>
                            <th style="font-size: 12px; padding-left: 10px;">Nr. <br> crt.</th>
                            <th style="padding-left: 10px;">Nume</th>
                            <th style="padding-left: 10px;">Prenume</th>
                            <th style="padding-left: 10px;">E-Mail</th>
                            <th style="padding-left: 10px;">Admin</th>
                            <th style="padding-left: 10px;">Ingineri</th>
                            <th style="padding-left: 10px;">HR</th>
                            <th style="padding-left: 10px;">Magazie</th>
                            <th style="padding-left: 10px;">Actiune</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $i=0;
                        ?>
                        @foreach($users as $user)
                            <tr>
                                <form action="{{ route('admin.assign') }}" method="post">
                                    <td style="padding-left: 10px;">{{ ++$i}}</td>
                                    <td style="padding-left: 10px;">{{ $user->first_name }}</td>
                                    <td style="padding-left: 10px;">{{ $user->last_name }}</td>
                                    <td style="padding-left: 10px;">{{ $user->email }} <input type="hidden" name="email" value="{{ $user->email }}"></td>
                                    <td style="padding-left: 10px;"><input type="checkbox" {{ $user->hasRole('Admin') ? 'checked' : '' }} name="role_admin" style="height: 20px; width:20px;"></td>
                                    <td style="padding-left: 10px;"><input type="checkbox" {{ $user->hasRole('Ingineri') ? 'checked' : '' }} name="role_ingineri" style="height: 20px; width:20px;"></td>
                                    <td style="padding-left: 10px;"><input type="checkbox" {{ $user->hasRole('HR') ? 'checked' : '' }} name="role_hr" style="height: 20px; width:20px;"></td>
                                    <td style="padding-left: 10px;"><input type="checkbox" {{ $user->hasRole('Magazie') ? 'checked' : '' }} name="role_magazie" style="height: 20px; width:20px;"></td>
                                    {{ csrf_field() }}
                                    <td style="padding-left: 10px;">
                                        <button type="submit" class="btn btn-primary btn-sm">Assign Roles</button>
                                        <a class="btn btn-warning btn-sm" href="{{ route('users.edit',$user->id) }}"><i class="fa fa-pencil"></i> Edit</a>
                                        <a class="btn btn-danger btn-sm" href="{{ route('password.request') }}">Reset password</a>
                                    </td>
                                </form>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

            </section>
        </div>
    </div>


@endsection

@section('scripts')

    <script type="text/javascript">
        $(document).ready(function () {
            $('#table-users-info').DataTable({

                dom: 'Blfrtip',
                buttons: [
                    {
                        extend: 'copy',
                        exportOptions: {
                            columns: [1,2]
                        }
                    },
                    {
                        extend: 'excel',
                        exportOptions: {
                            columns: [1,2]
                        }
                    },
                    {
                        extend: 'pdf',
                        exportOptions: {
                            columns: [1,2]
                        }
                    }
                ]
            } );

            $('#table-users-info_filter input').addClass('form-control');
        });
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
