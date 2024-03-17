<!DOCTYPE html>
<html>
<head>
    <title>Produs</title>
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
                <strong>Nume: </strong><span>{{ $product->article['name'] }}</span><br>
                <strong>Nume Flowopt: </strong><span>{{ $product->product }}</span><br>
                <strong>Numar Serie: </strong><span>{{ $product->serial_no }}</span><br>
                <strong>Numar Comanda: </strong><span>{{ $product->sales_order }}</span><br>
                <strong>Categorie Articol: </strong><span>{{ $product->article->article_category['name'] }}</span><br>
                <strong>Client: </strong><span>{{ $product->article->client['name']}}</span><br>
                <strong>Status: </strong>
                    <span>
                        @if ($product->status_id == 1)
                            <span>lansat</span>
                        @elseif ($product->status_id == 2)
                            <span>in lucru</span>
                        @elseif ($product->status_id == 3)
                            <span>anulat</span>
                        @elseif ($product->status_id == 4)
                            <span>finalizat</span>
                        @endif
                    </span><br>
                <strong>Preluat la: </strong><span>@if($product->first_dosage == NULL) nepreluat @else {{ Carbon\Carbon::parse($product->first_dosage)->format('d.m.Y H:i:s') }} @endif</span><br>
                <strong>Terminat la: </strong><span>@if ($product->finished_at == NULL) neterminat @else {{ Carbon\Carbon::parse($product->finished_at)->format('d.m.Y H:i:s') }} @endif</span><br>
                <strong>Timp de executie (h:m:s): </strong><span>
                    @if($product->finished_at == NULL)
                        <span>00:00:00</span>
                    @else
                        {{ gmdate('H:i:s', \Carbon\Carbon::parse($product->first_dosage)->diffInSeconds(\Carbon\Carbon::parse($product->finished_at))) }}
                        {{--<span>{{ ((strtotime($report->finished_at)- strtotime($report->first_dosage))/(60*60*24))%365 }} zile {{ ((strtotime($report->finished_at)- strtotime($report->first_dosage))/(60*60))%24 }} h {{ ((strtotime($report->finished_at)- strtotime($report->first_dosage))/(60))%60}} m</span>--}}
                    @endif
                </span><br>
            </div>


            @if(count($dosages))
                <table style="font-size: 12px;">
                    <thead>
                    <tr>
                        <th class="th" style="width: 40px; font-size: 10px;">Nr. crt.</th>
                        <th class="th">Team Leader</th>
                        <th class="th">Material</th>
                        <th class="th">U.M.</th>
                        <th class="th">Extra</th>
                        <th class="th">Pompa</th>
                        <th class="th">Cantitate</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $i=0;
                    ?>
                    @foreach($dosages as $dosage)
                        @if($dosage->quantity > 0)
                            <tr>
                                <td class="line-row">{{ ++$i }}</td>
                                <td class="line-row">
                                    {{ $product->worker['first'] }} {{ $product->worker['last'] }}
                                </td>
                                <td class="line-row">{{$dosage->material['name']}}</td>
                                <td class="line-row">{{ $dosage->material['unit'] }}</td>
                                <td class="line-row">
                                    @if($i > count($product->articles_materials_rows))
                                        <span>Da</span>
                                    @else
                                        <span>Nu</span>
                                    @endif
                                </td>
                                <td class="line-row">{{ $dosage->pump['name'] }}</td>
                                <td class="line-row">{{ $dosage->quantity }}</td>
                            </tr>
                        @endif
                    @endforeach
                    <tr style="background-color: #e6f0ff;">
                        <td colspan="6" style="text-align: right;  border: 1px solid #000;"><b>TOTAL</b></td>
                        <td style="text-align: center;  border: 1px solid #000;"><b>{{ $dosages->sum('quantity') }}</b></td>
                    </tr>
                    </tbody>
                </table>
            @else
                <table>
                    <tr>
                        <td>Nu s-a realizat nicio etapa pentru acest produs.</td>
                    </tr>
                </table>
            @endif

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