<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoice</title>
</head>
<body>
    <p>Hello {{ $invoice->quote->client_name }},</p>

    <p>Please find attached your invoice <strong>#{{ $invoice->invoice_number }}</strong> dated <strong>{{ \Carbon\Carbon::parse($invoice->invoice_date)->toFormattedDateString() }}</strong>.</p>

    <p>Thanks for doing business with us.</p>

    <p>Regards,<br>Laravel</p>
</body>
</html>
