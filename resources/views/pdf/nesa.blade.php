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
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td style="text-align:center">{{ $key }}</td>
                    <td style="padding: 0; margin: 0;">
                        <div>
                            @foreach ($value as $subitem)
                                <div
                                    style="padding: 2px; @if (!$loop->first) border-top: 1px solid rgb(99, 99, 99); @endif">
                                    {{ $subitem->name }}
                                </div>
                            @endforeach
                        </div>
                    </td>
                    <td style="padding: 0; margin: 0;">
                        <div>
                            @foreach ($value as $subitem)
                                <div
                                    style="padding: 2px; @if (!$loop->first) border-top: 1px solid rgb(99, 99, 99); @endif">
                                    {{ $subitem->description }}</div>
                            @endforeach
                        </div>
                    </td>
                    <td style="padding: 0; margin: 0; text-align:center">
                        <div>
                            @foreach ($value as $subitem)
                                <div
                                    style="padding: 2px; @if (!$loop->first) border-top: 1px solid rgb(99, 99, 99); @endif">
                                    {{ $subitem->uom }}
                                </div>
                            @endforeach
                        </div>
                    </td>
                    <td style="padding: 0; margin: 0; text-align:center">
                        <div>
                            @foreach ($value as $subitem)
                                <div
                                    style="padding: 2px; @if (!$loop->first) border-top: 1px solid rgb(99, 99, 99); @endif">
                                    {{ $subitem->quantity }}pcs</div>
                            @endforeach
                        </div>
                    </td>
                    <td style="padding: 0; margin: 0; text-align:center">
                        <div>
                            @foreach ($value as $subitem)
                                <div
                                    style="padding: 2px; @if (!$loop->first) border-top: 1px solid rgb(99, 99, 99); @endif">
                                    {{ \Carbon\Carbon::parse($subitem->expiry)->format('F d, Y') }}</div>
                            @endforeach
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
