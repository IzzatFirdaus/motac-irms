<!DOCTYPE html>
<html lang="ms">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Notifikasi MOTAC')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .email-container {
            max-width: 700px;
            margin: 20px auto;
            background-color: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 0.5rem;
            overflow: hidden;
        }

        .email-header {
            background-color: #0055A4;
            /* MOTAC Blue */
            color: #ffffff;
            padding: 20px;
            text-align: center;
        }

        .email-header h1 {
            margin: 0;
            font-size: 24px;
        }

        .email-body {
            padding: 30px;
            line-height: 1.6;
            color: #495057;
        }

        .email-footer {
            background-color: #e9ecef;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
        }

        .btn-primary {
            background-color: #0055A4;
            border-color: #0055A4;
        }

        .card {
            border: 1px solid #dee2e6;
        }

        .card-header {
            background-color: #f8f9fa;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="email-container">
            <div class="email-header">
                <h1>{{ config('app.name', 'MOTAC IRMS') }}</h1>
            </div>
            <div class="email-body">
                @yield('content')
            </div>
            <div class="email-footer">
                <p>&copy; {{ date('Y') }} Kementerian Pelancongan, Seni dan Budaya. Hak Cipta Terpelihara.</p>
                <p>Ini adalah e-mel yang dijana secara automatik. Sila jangan balas e-mel ini.</p>
            </div>
        </div>
    </div>
</body>

</html>
