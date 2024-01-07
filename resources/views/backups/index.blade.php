@extends('layouts.master')

@section('content')
    <h3>Administer Database Backups</h3>
    <div class="row" style="margin-top: 20px;">
        <div class="col-xs-12">
            @if (count($backups))

                <table class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>File Name</th>
                        <th>Size</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($backups as $key => $backup)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>{{ $backup['file_name'] }}</td>
                            <td>{{ $backup['file_size'] }}</td>
                            <td>
                                {{ $backup['last_modified'] }}
                            </td>
                            <td style="width: 200px;">
                                <a class="btn btn-primary btn-xs"
                                   href="{{ route('backupDownload', $backup['file_name']) }}"><i class="fa fa-cloud-download"></i> Download</a>
                                <a class="btn btn-danger btn-xs" data-button-type="delete"
                                   href="{{ route('backupDelete', $backup['file_name']) }}"><i class="fa fa-trash-o"></i>
                                    Delete</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @else
                <div class="well">
                    <h4>There are no backups.</h4>
                </div>
            @endif
        </div>
    </div>
@endsection