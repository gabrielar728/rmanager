@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <h3 class="page-header"><i class="fa fa-file-text-o"></i> Articole</h3>
            <ol class="breadcrumb">
                <li><i class="fa fa-home"></i><a href="{{ route('home') }}">Acasa</a></li>
                <li><i class="icon_documents_alt"></i>Articole</li>
                <li><i class="fa fa-eye"></i>Afisare</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <span class="panel-title">Afisare Articol</span>
                    <div class="pull-right">
                        <a href="{{route('informatii-rapoarte-articole')}}" class="btn btn-default" style="margin-top: 2px;">Inapoi</a>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <strong>Nume</strong>
                                <p>{{ $article->name }}</p>
                            </div>
                            <div class="form-group">
                                <strong>Lucratori</strong>
                                <p>{{ $article->workers_required }}</p>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <strong>Categorie</strong>
                                <p>{{ $article->article_category['name'] }}</p>
                            </div>
                            <div class="form-group">
                                <strong>Client</strong>
                                <p>{{ $article->client['name'] }}</p>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="row">
                                <div class="col-sm-6">
                                    <strong>Creat la</strong>
                                    <p>{{ Carbon\Carbon::parse($article->created_at)->format('d.m.Y') }}</p>
                                </div>
                                <div class="col-sm-6">
                                    <strong>Modificat la</strong>
                                    <p>{{ Carbon\Carbon::parse($article->updated_at)->format('d.m.Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th style="width: 40px; text-align: center;">#</th>
                                <th>Material</th>
                                <th style="width: 15%;">Cantitate</th>
                                <th>Proces</th>
                                <th style="width: 70px;">Extra</th>
                                <th style="width: 7%;">U.M.</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $n=0;
                            ?>
                            @foreach($article->articles_materials as $articles_material)
                                <tr>
                                    <td style="text-align: center;">{{ ++$n}}</td>
                                    <td><span>{{$articles_material->material['name']}}</span></td>
                                    <td><span>{{$articles_material->quantity}}</span></td>
                                    <td><span>{{$articles_material->process['name']}}</span></td>
                                    <td style="text-align: center;">
                                        @if($articles_material->extra === 0)
                                            <input type="checkbox" class="input-control extra" value="0" id="extra" name="extra[]" style="height:20px; width:20px;" disabled>
                                            @else
                                            <input type="checkbox" class="input-control extra" value="1" checked id="extra" name="extra[]" style="height:20px; width:20px;" disabled>
                                        @endif
                                    </td>
                                    <td><span>{{ $articles_material->material['unit'] }}</span></td>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection

@section('scripts')

    <script type="text/javascript">

    </script>
@endsection

<style>
    .form-inline {
        display: inline-block;
    }
    strong {color: #333333;}
</style>