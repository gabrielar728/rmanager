<!DOCTYPE html>
<html>
<head>
    <title>Articol</title>
    <style type="text/css">
        html,body {
            padding-top: 50px;
            margin: 0;
            width: 100%;
            background: #fff;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10px;
        }
        table {
            width: 700px;
            margin: 0 auto;
            text-align: left;
            border-collapse: collapse;
        }
        th { padding-left: 2px; }
        td { padding: 2px; }

        .aeu {
            text-align: right;
            padding-right: 10px;
            font-family: Arial, Verdana, sans-serif;
        }
        .line-top {
            border-left: 1px solid #606060;
            padding-left: 10px;
            font-family: Arial, Verdana, sans-serif;
        }

        .imageAeu { width: auto; height: auto;}

        .th {
            background-color: #ddd;
            border: 1px solid;
            text-align: center;
        }
        .line-row
        {
            background-color: #fff;
            border: 1px solid;
            text-align: center;
        }
        #container {
            width: 100%;
            margin: 0 auto;
        }

        hr {
            width: 100%;
            margin-right: 0;
            margin-left: 0;
            margin-top: 35px;
            margin-bottom: 20px;
            border: 0 none;
            border-top: 1px dashed #322f32;
            background: none;
            height: 0;
        }

        .length-limit { max-height: 350px; min-height: 350px;}

        .footer {
            position: absolute;
            right: 0;
            bottom: 0;
            left: 0;
            padding: 1rem;
            text-align: center;
        }
    </style>
</head>
<body>
<div id="divide">
    <div id="container">
        <div class="length-limit">
            <table style="margin-bottom: 20px;">
                <tr>
                    <td style="padding-left: 40px;">
                        <img src="{{ asset('images/logo.jpg') }}" class="imageAeu">
                    </td>
                    <td class="aeu">
                        <span> </span><br>
                        <span>Cod Fiscal</span><br>
                        <span>Nr. Reg. Com.</span><br>
                        <span>Sediul</span><br>
                        <span>Judet</span><br>
                    </td>
                    <td class="line-top">
                        <strong>ARPLAMA SRL</strong><br>
                        <strong>13104658</strong><br>
                        <strong>J08/513/2000</strong><br>
                        <strong>Fagaras, Str. Tudor Vladimirescu, nr. 86</strong><br>
                        <strong>Brasov</strong><br>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align: right;"></td>
                    <td colspan="0" style="text-align: right; padding-left: 80px;">
                        <span>Data: </span><strong>{{ Carbon\Carbon::now()->format('d.m.Y') }}</strong><br>
                        <span>Ora: </span><strong>{{ Carbon\Carbon::now()->format('H:i:s') }}</strong>
                    </td>
                </tr>
            </table>

            <div style="width: 700px; margin: 0 auto; text-align: left; font-size: 14px; margin-bottom: 30px;">
                <strong>Nume: </strong><span>{{ $article->name }}</span><br>
                <strong>Categorie Articol: </strong><span>{{ $article->article_category['name'] }}</span><br>
                <strong>Client: </strong><span>{{ $article->client['name'] }}</span><br>
                <strong>Status: </strong><span>@if($article->status == 1)
                                                    <span>activ</span>
                                                @else
                                                    <span>inactiv</span>
                                                @endif</span><br>
                <strong>Creat la: </strong><span>{{ Carbon\Carbon::parse($article->created_at_article)->format('d.m.Y H:i:s') }}</span><br>
            </div>

            <table style="font-size: 12px;">
            <thead>
            <tr>
                <th class="th" style="width: 40px; font-size: 10px;">Nr. crt.</th>
                <th class="th">Material</th>
                <th class="th">Cantitate</th>
                <th class="th">U.M.</th>
                <th class="th">Proces</th>
{{--                <th class="th">Extra</th>--}}
            </tr>
            </thead>
            <tbody>
            <?php
            $i=0;
            ?>
            @foreach($article->articles_materials as $articles_material)
                <tr>
                    <td class="line-row">{{ ++$i }}</td>
                    <td class="line-row">
                        {{$articles_material->material['name']}}
                    </td>
                    <td class="line-row">{{$articles_material->quantity}}</td>
                    <td class="line-row">{{ $articles_material->material['unit'] }}</td>
                    <td class="line-row">{{$articles_material->process['name']}}</td>
                    {{--<td class="line-row">
                        @if($articles_material->extra === 0)
                            <span>Nu</span>
                        @else
                           <span>Da</span>
                        @endif
                    </td>--}}
                </tr>
            @endforeach
            </tbody>
            </table>
        </div>

        <div class="footer">
            <table>
                <tr>
                    <td style="font-size: 10px; text-align: center;">
                        Str. Tudor Vladimirescu nr. 86, 505200 Fagaras, RO
                    </td>
                </tr>
                <tr>
                    <td style="font-size: 10px; text-align: center;">
                        Mobil: +40 (732) 310 968 | Telefon: +40(0) 268 280 322 | Fax: +40(0) 268 280 327 | E-mail: info@arplama.ro
                    </td>
                </tr>
            </table>
        </div>

    </div>
</div>
</body>
</html>