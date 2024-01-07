<!DOCTYPE html>
<html>
<head>
    <style type="text/css">
        html,body {
            padding-top: 50px;
            margin: 0;
            width: 100%;
            background: #fff;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10px;
        }
        #email {
            width: 100%;
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
            <table style="font-size: 14px;">
                <tr>
                    @if($current_day == 'Monday')
                        <td>Produse finalizate vineri:</td>
                        <td><strong style="color: green">{{ count($friday_products) }} </strong></td>
                    @else
                        <td>Produse finalizate ieri:</td>
                        <td><strong style="color: green">{{ count($yesterday_products) }} </strong></td>
                    @endif
                </tr>
                <tr>
                    <td>Total produse finalizate:</td>
                    <td><strong style="color: green">{{ count($total_products) }}</strong></td>
                </tr>
                <tr>
                    <td>Produse expirate:</td>
                    <td><strong style="color: red">{{ count($overdue_products) }}</strong></td>
                </tr>
            </table>
            <br>
            @if(count($overdue_products))
                <h3 style="padding-bottom: 0; margin-bottom: 5px;">Detalii produse expirate</h3>

                <table id="email" style="font-size: 12px;">
                    <thead>
                    <tr>
                        <th class="th">Nume Produs</th>
                        <th class="th">Status</th>
                        <th class="th">Grup</th>
                        <th class="th">Responsabil</th>
                        <th class="th">Data Scadenta</th>
                        <th class="th">Decalaj</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($overdue_products as $overdue_product)
                        <tr>
                            <td class="line-row">{{ $overdue_product->article['name'] }}</td>
                            <td class="line-row">
                                @if ($overdue_product->status_id == 1)
                                    <span>lansat</span>
                                @elseif ($overdue_product->status_id == 2)
                                    <span>in lucru</span>
                                @endif
                            </td>
                            <td class="line-row">
                                @if($overdue_product->group['name'] === 'none')
                                    <span> - </span>
                                @else
                                    <span>{{ $overdue_product->group['name'] }}</span>
                                @endif
                            </td>
                            <td class="line-row">{{ $overdue_product->worker['first'] }} {{ $overdue_product->worker['last'] }}</td>
                            <td class="line-row">{{ Carbon\Carbon::parse($overdue_product->production_date)->format('d.m.Y') }}</td>
                            <td class="line-row">{{ $overdue_product->days }} (zile)</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
        </div>

    </div>
</div>
</body>
</html>