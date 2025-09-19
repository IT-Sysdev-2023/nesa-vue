<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Supplier Report - {{ $supplier }}</title>
    <style>
        body {
            font-family: "Times-Roman", Times, serif;
            font-size: 12px;
        }


        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th {
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 4px;
            text-align: left;
            background: #f0f0f0;
        }

        td {
            padding: 4px;
            margin-bottom: 5px
            vertical-align: top;
            font-size: 11px;
        }

        /* ✅ add bottom border for each row */
        tbody tr {
            border-bottom: 1px solid #ccc;
        }
    </style>
</head>

<body>
    <table style="width: 100%; border: none; border-collapse: collapse; margin-bottom: 10px;">
        <tr>
            <td style="vertical-align: top; border: none;">
                <p style="margin: 0;">Vendor Name: {{ $supp_code }}</p>
                <p style="margin: 0;">Vendor Code: {{ $supplier }}</p>
            </td>
            <td style="vertical-align: top; text-align: right; border: none;">
                <p style="margin: 0;">Date: {{ now()->format('F j, Y') }}</p>
            </td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Item Code</th>
                <th>Business Unit</th>
                <th>Item Description</th>
                <th>Unit</th>
                <th>Quantity</th>
                <th>Expiry Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $key => $value)
                <tr style="margin-bottom: 5px">
                    <td>{{ $loop->iteration }}</td>
                    <td style="text-align:center">{{ $key }}</td>

                    <td>
                        {!! implode('<br>', $value->pluck('name')->toArray()) !!}
                    </td>

                    <td>
                        {!! implode('<br>', $value->pluck('description')->toArray()) !!}
                    </td>

                    <td style="text-align:center">
                        {!! implode('<br>', $value->pluck('uom')->toArray()) !!}
                    </td>

                    <td style="text-align:center">
                        {!! implode('<br>', $value->map(fn($x) => $x->quantity . ' pcs')->toArray()) !!}
                    </td>

                    <td style="text-align:center">
                        {!! implode('<br>', $value->map(fn($x) => \Carbon\Carbon::parse($x->expiry)->format('F d, Y'))->toArray()) !!}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div>
        <p style="margin: 0; text-align: right; font-weight: bold; margin-top: 10px;">
            Total Quantity: {{ $totalQty }} pcs
        </p>
    </div>
</body>

</html>