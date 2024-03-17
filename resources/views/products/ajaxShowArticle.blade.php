<div class="row">
    @foreach($articles as $article)
        <div class="col-lg-6">
            <strong>Categorie Articol</strong>
            <p>{{{$article->article_category['name']}}}</p>
        </div>
        <div class="col-lg-6">
            <strong>Nume Articol</strong>
            <p>{{{$article->article_name}}}</p>
        </div>
        <div class="col-lg-6">
            <strong>Lucratori</strong>
            <p>{{{$article->workers_required}}}</p>
        </div>
        <div class="col-lg-6">
            <strong>Client</strong>
            <p>{{{$article->client['name']}}}</p>
        </div>

    @endforeach
</div>

<div class="row">
    <table class="table table-bordered table-striped" style="margin-top: 10px;">
        <thead>
        <tr>
            <th style="width: 40px; text-align: center;">#</th>
            <th>Material</th>
            <th style="width: 15%;">Cantitate</th>
            <th>Proces</th>
{{--            <th style="width: 70px;">Extra</th>--}}
            <th style="width: 7%;">U.M.</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $n=0;
        ?>
        @foreach($material->articles_materials as $articles_material)
            <tr>
                <td style="text-align: center;">{{ ++$n}}</td>
                <td><span>{{$articles_material->material['name']}}</span></td>
                <td><span>{{$articles_material->quantity}}</span></td>
                <td><span>{{$articles_material->process['name']}}</span></td>
{{--                <td><span>{{$articles_material->extra === 0 ? 'Nu' : 'Da'}}</span></td>--}}
                <td><span>{{ $articles_material->material['unit'] }}</span></td>
        @endforeach
        </tbody>
    </table>
</div>
