<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Invoice</title>
</head>

<body>
    <p>Hello {{ $quote->client_name }},</p>

    <p>Please find attached your invoice <strong>#{{ $quote->quote_number }}</strong> dated <strong>{{ \Carbon\Carbon::parse($quote->quote_date)->toFormattedDateString() }}</strong>.</p>

    <p>Thanks for doing business with us.</p>

    <p>Regards,<br>Laravel</p>
</body>

</html>