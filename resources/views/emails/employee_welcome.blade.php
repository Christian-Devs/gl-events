<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #333;
            line-height: 1.4
        }

        .container {
            max-width: 680px;
            margin: 0 auto;
            padding: 20px;
        }

        .btn {
            display: inline-block;
            padding: 10px 16px;
            background: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
        }

        .muted {
            color: #666;
            font-size: 13px
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>{{ config('app.name') }} — Welcome</h2>

        <p>Hi {{ $employee->first_name ?? $employee->name ?? 'there' }},</p>

        <p>
            An account has been created for you on <strong>{{ config('app.name') }}</strong>.
            Use the credentials below to sign in for the first time:
        </p>

        <ul>
            <li><strong>Email:</strong> {{ $employee->email ?? ($employee->user->email ?? '') }}</li>
            <li><strong>Temporary password:</strong> {{ $password }}</li>
        </ul>

        <p class="muted">
            For security, you will be asked to change this password when you first sign in.
            After logging in, please choose a new password and keep it secret.
        </p>

        <p>
            <a class="btn" href="{{ config('app.url') }}/">Sign in</a>
        </p>

        <hr>
        <p class="muted">If you did not expect this email, please contact an administrator.</p>
    </div>
</body>

</html>