<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title ?? config('app.name') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            width: 100% !important;
            height: 100% !important;
            background-color: #f8fafc;
            font-family: 'Tajawal', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        table {
            border-spacing: 0;
            border-collapse: collapse;
        }

        td {
            padding: 0;
        }

        img {
            border: 0;
        }

        .wrapper {
            width: 100%;
            table-layout: fixed;
            background-color: #f8fafc;
            padding-bottom: 40px;
        }

        .main {
            background-color: #ffffff;
            margin: 0 auto;
            width: 100%;
            max-width: 600px;
            border-spacing: 0;
            color: #334155;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .header {
            background-color: #0f172a;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            color: #10b981;
            margin: 0;
            font-size: 24px;
            font-weight: 700;
        }

        .content {
            padding: 40px 30px;
            line-height: 1.6;
            text-align: right;
        }

        .footer {
            padding: 30px;
            text-align: center;
            color: #64748b;
            font-size: 13px;
        }

        .btn {
            display: inline-block;
            padding: 12px 30px;
            background-color: #10b981;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            margin: 20px 0;
        }

        .otp-card {
            background-color: #f1f5f9;
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            margin: 25px 0;
        }

        .otp-code {
            font-size: 32px;
            font-weight: 700;
            letter-spacing: 8px;
            color: #0f172a;
            margin: 0;
        }

        .divider {
            height: 1px;
            background-color: #e2e8f0;
            margin: 30px 0;
        }

        .vision-logo {
            margin-top: 15px;
            opacity: 0.7;
        }

        @media only screen and (max-width: 600px) {
            .main {
                width: 95% !important;
            }
        }
    </style>
</head>

<body>
    <center class="wrapper">
        <table class="main" width="100%">
            <!-- Header -->
            <tr>
                <td class="header">
                    <h1>أقران | Aqran</h1>
                </td>
            </tr>

            <!-- Body -->
            <tr>
                <td class="content">
                    @yield('content')
                </td>
            </tr>

            <!-- Footer -->
            <tr>
                <td class="footer">
                    <p>© {{ date('y') }} أقران. جميع الحقوق محفوظة.</p>
                    <p>إذا كان لديك أي استفسار، تواصل معنا عبر: <a href="mailto:support@aqran.sa"
                            style="color: #10b981; text-decoration: none;">support@aqran.sa</a></p>
                    <div class="divider"></div>
                    <p style="font-weight: 500; color: #475569;">التميز التشغيلي في التعليم الجامعي</p>
                    <div class="vision-logo">
                        <small style="color: #94a3b8;">رؤية المملكة 2030</small>
                    </div>
                </td>
            </tr>
        </table>
    </center>
</body>

</html>