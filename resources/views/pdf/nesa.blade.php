<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Supplier Report - {{ $supplier }}</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 4px;
            text-align: left;
        }

        th {
            background: #f0f0f0;
        }
    </style>
</head>

<body>
    <div style="display: grid; grid-template-columns: 1fr auto; gap: 10px; align-items: center;">
        <div>
            <p>Vend142or Name: {{ $supp_code }}</p>
            <p>Vendor Code: {{ $supplier }}</p>
        </div>
        <div>
            <p style="text-align: right;">Date: {{ now()->format('F j, Y') }}</p>
        </div>
    </div>

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
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td style="text-align:center">{{ $key }}</td>
                    <td style="padding: 0; margin: 0;">
                        <div>
                            @foreach ($value as $subitem)
                                <div style="padding: 2px; border-bottom: 1px solid rgb(99, 99, 99);">{{ $subitem->name }}
                                </div>
                            @endforeach
                        </div>
                    </td>
                    <td style="padding: 0; margin: 0;">
                        <div>
                            @foreach ($value as $subitem)
                                <div style="padding: 2px; border-bottom: 1px solid rgb(99, 99, 99);">
                                    {{ $subitem->description }}</div>
                            @endforeach
                        </div>
                    </td>
                    <td style="padding: 0; margin: 0; text-align:center">
                        <div>
                            @foreach ($value as $subitem)
                                <div style="padding: 2px; border-bottom: 1px solid rgb(99, 99, 99);">{{ $subitem->uom }}
                                </div>
                            @endforeach
                        </div>
                    </td>
                    <td style="padding: 0; margin: 0; text-align:center">
                        <div>
                            @foreach ($value as $subitem)
                                <div style="padding: 2px; border-bottom: 1px solid rgb(99, 99, 99);">
                                    {{ $subitem->quantity }}pcs</div>
                            @endforeach
                        </div>
                    </td>
                    <td style="padding: 0; margin: 0; text-align:center">
                        <div>
                            @foreach ($value as $subitem)
                                <div style="padding: 2px; border-bottom: 1px solid rgb(99, 99, 99);">
                                    {{ $subitem->expiry }}</div>
                            @endforeach
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
