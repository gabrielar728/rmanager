<!DOCTYPE html>
<html>
<head>
    <style>
        .content, small, h2, table {
            color: #262626 !important;
        }

        table {
            font-family: arial, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }

        td, th {
            text-align: left;
            padding: 8px;
        }

        .dosages {
            border: 1px solid #dddddd;
        }

    </style>
</head>
<body>
<div class="content">
    <small>This message was automatically generated. Please do not reply.</small>
    <h2>Finished product:</h2>

    <table>
        <tr>
            <th style="width: 25%">Product Name:</th>
            <td>{{ $product->article['name'] }}</td>
        </tr>
        <tr style="width: 25%">
            <th>Product Flowopt Name:</th>
            <td>{{ $product->product }}</td>
        </tr>
        <tr>
            <th>Serial Number:</th>
            <td>{{ $product->serial_no }}</td>
        </tr>
        <tr>
            <th>Sales Order:</th>
            <td>{{ $product->sales_order }}</td>
        </tr>
        <tr>
            <th>Worker Name:</th>
            <td>{{ $product->worker['first'] }} {{ $product->worker['last'] }}</td>
        </tr>
        <tr>
            <th>Start Date:</th>
            <td>{{ $product->start_date }}</td>
        </tr>
        <tr>
            <th>End Date:</th>
            <td>{{ $product->finished_at }}</td>
        </tr>
        <tr>
            <th>Execution Time:</th>
            <td>{{ $product->exec_time }}</td>
        </tr>
    </table>

    <table style="margin-top: 2%; width: 50%;">
        <tr>
            <th class="dosages" style="width: 20px;">#</th>
            <th class="dosages">Material</th>
            <th class="dosages">U.M.</th>
            <th class="dosages">Pump</th>
            <th class="dosages">Quantity</th>
        </tr>

        @foreach($product->dosages as $key => $dosage)
            <tr>
                <td class="dosages">{{ ++$key }}</td>
                <td class="dosages">{{ $dosage->material['name'] }}</td>
                <td class="dosages">{{ $dosage->material['unit'] }}</td>
                <td class="dosages">{{ $dosage->pump['name'] }}</td>
                <td class="dosages">{{ $dosage->quantity }}</td>
            </tr>
        @endforeach
        <tr>
            <td colspan="4" style="text-align: right;"><b>TOTAL: </b></td>
            <td style="text-align: left;"><b>{{ $product->total_resin }}</b></td>
        </tr>
    </table>
</div>
</body>
</html>
