<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Test Email</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f7f7f7;
            color: #333;
            padding: 20px;
        }
        .email-container {
            background: #fff;
            border-radius: 8px;
            max-width: 600px;
            margin: 0 auto;
            padding: 30px 40px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.07);
        }
        .header {
            border-bottom: 1px solid #ececec;
            margin-bottom: 20px;
            padding-bottom: 10px;
        }
        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #888;
            border-top: 1px solid #ececec;
            padding-top: 10px;
        }
        h1 {
            color: #0366d6;
        }
        p {
            line-height: 1.7;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>Test Email from MOTAC ICT LOAN HRMS</h1>
        </div>

        <p>
            Hello,<br><br>
            This is a <strong>test email</strong> sent from your Laravel system using Papercut SMTP.<br>
            If you are reading this, your email configuration is working correctly for development!
        </p>

        @if(isset($data) && count($data))
            <hr>
            <h3>Additional Data:</h3>
            <ul>
                @foreach($data as $key => $value)
                    <li><strong>{{ $key }}:</strong> {{ $value }}</li>
                @endforeach
            </ul>
        @endif

        <div class="footer">
            &copy; {{ date('Y') }} MOTAC ICT LOAN HRMS &mdash; This is a test email for development only.
        </div>
    </div>
</body>
</html>
