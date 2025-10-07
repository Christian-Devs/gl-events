<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 12px;
            color: #333
        }

        .header {
            text-align: center;
            margin-bottom: 18px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table th,
        table td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        .right {
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>{{ config('app.name') }}</h2>
        <h4>Quote: {{ $quote->quote_number ?? $quote->id }}</h4>
    </div>

    <div>
        <strong>Client:</strong> {{ $quote->client_name ?? '-' }}<br>
        <strong>Email:</strong> {{ $quote->client_email ?? '-' }}<br>
        <strong>Date:</strong> {{ $quote->quote_date ?? $quote->created_at->format('Y-m-d') ?? '' }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th class="right">Qty</th>
                <th class="right">Unit</th>
                <th class="right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($quote->items ?? [] as $item)
            <tr>
                <td>{{ $item->description }}</td>
                <td class="right">{{ $item->quantity }}</td>
                <td class="right">{{ number_format($item->unit_price, 2) }}</td>
                <td class="right">{{ number_format($item->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 10px; text-align: right;">
        <div>Subtotal: R {{ number_format($quote->subtotal ?? 0, 2) }}</div>
        <div>VAT: R {{ number_format($quote->vat ?? 0, 2) }}</div>
        <div><strong>Total: R {{ number_format($quote->total ?? 0, 2) }}</strong></div>
    </div>

    <div style="margin-top: 20px;">
        <strong>Notes:</strong><br>
        {!! nl2br(e($quote->notes ?? '')) !!}
    </div>
</body>

</html>