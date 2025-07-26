<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }
        .header, .footer {
            text-align: center;
            color: #333;
        }
        .company-info, .client-info {
            margin-bottom: 20px;
        }
        .company-info h2, .client-info h3 {
            margin: 0 0 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }
        table th, table td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: left;
        }
        .summary {
            margin-top: 30px;
            width: 100%;
            text-align: right;
        }
        .summary td {
            padding: 5px 10px;
        }
        .notes {
            margin-top: 30px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>INVOICE</h1>
        <p>Invoice #: {{ $invoice->invoice_number }}<br>
        Date: {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d M Y') }}<br>
        Due: {{ \Carbon\Carbon::parse($invoice->due_date)->format('d M Y') }}</p>
    </div>

    <div class="company-info">
        <h2>{{ config('app.name') }}</h2>
        <p>{{ config('settings.company_address') ?? 'Company Address' }}<br>
        {{ config('settings.company_email') ?? 'company@example.com' }}</p>
    </div>

    <div class="client-info">
        <h3>Billed To:</h3>
        <p>{{ $invoice->quote->client_name }}<br>
        {{ $invoice->quote->client_email }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Description</th>
                <th style="text-align:right">Qty</th>
                <th style="text-align:right">Unit Price</th>
                <th style="text-align:right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoice->items as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->description }}</td>
                <td style="text-align:right">{{ $item->quantity }}</td>
                <td style="text-align:right">R {{ number_format($item->unit_price, 2) }}</td>
                <td style="text-align:right">R {{ number_format($item->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="summary">
        <tr>
            <td><strong>Subtotal:</strong></td>
            <td style="text-align:right">R {{ number_format($invoice->subtotal, 2) }}</td>
        </tr>
        <tr>
            <td><strong>VAT:</strong></td>
            <td style="text-align:right">R {{ number_format($invoice->vat ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td><strong>Total:</strong></td>
            <td style="text-align:right"><strong>R {{ number_format($invoice->total, 2) }}</strong></td>
        </tr>
    </table>

    @if ($invoice->notes)
    <div class="notes">
        <p><strong>Notes:</strong><br>{{ $invoice->notes }}</p>
    </div>
    @endif

    <div class="footer">
        <p>Thank you for your business!</p>
    </div>

</body>
</html>
