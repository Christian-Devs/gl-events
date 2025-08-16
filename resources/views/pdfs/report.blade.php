<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>{{ $title }} Report</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        h2 {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        th {
            background: #f2f2f2;
        }
    </style>
</head>

<body>
    <h2>{{ $title }} Report</h2>

    <h4>Summary</h4>
    <ul>
        @foreach ($summary as $key => $value)
            <li>{{ ucfirst(str_replace('_', ' ', $key)) }}:
                @if (is_numeric($value))
                    {{ is_float($value) ? 'R ' . number_format($value, 2) : $value }}
                @else
                    {{ $value }}
                @endif
            </li>
        @endforeach
    </ul>

    <h4>Monthly Overview</h4>
    <table>
        <thead>
            <tr>
                <th>Month</th>
                <th>Count</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($monthly as $month)
                <tr>
                    <td>{{ $month['month'] }}</td>
                    <td>{{ $month['count'] }}</td>
                    <td>R {{ number_format($month['total'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>