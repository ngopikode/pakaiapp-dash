<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        /* Base styles */
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f8fafc;
            margin: 0;
            padding: 0;
            color: #334155;
            -webkit-font-smoothing: antialiased;
        }
        
        .wrapper {
            width: 100%;
            table-layout: fixed;
            background-color: #f8fafc;
            padding: 40px 0;
        }

        .main {
            background-color: #ffffff;
            margin: 0 auto;
            width: 100%;
            max-width: 600px;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        /* Header */
        .header {
            background: linear-gradient(135deg, #B67332 0%, #d98a3c 100%);
            padding: 40px 30px;
            text-align: center;
        }

        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 28px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        /* Content */
        .content {
            padding: 40px 30px;
            background-color: #ffffff;
        }

        .message-body {
            font-size: 16px;
            line-height: 1.6;
            color: #475569;
            margin-bottom: 30px;
        }

        .message-body p {
            margin: 0 0 16px 0;
        }

        /* Button */
        .btn-wrapper {
            text-align: center;
            margin: 40px 0 20px;
        }

        .btn {
            display: inline-block;
            background-color: #B67332;
            color: #ffffff !important;
            text-decoration: none;
            padding: 14px 32px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.2s ease;
            box-shadow: 0 4px 6px rgba(182, 115, 50, 0.2);
        }

        /* Footer */
        .footer {
            background-color: #f1f5f9;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
        }

        .footer p {
            margin: 0 0 10px 0;
            font-size: 13px;
            color: #64748b;
            line-height: 1.5;
        }

        .footer-logo {
            font-weight: 800;
            color: #94a3b8;
            font-size: 18px;
            margin-bottom: 15px;
            letter-spacing: 1px;
        }
        
        /* Responsive */
        @media screen and (max-width: 600px) {
            .main {
                border-radius: 0;
            }
            .wrapper {
                padding: 0;
            }
            .header {
                padding: 30px 20px;
            }
            .content {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="main">
            <!-- Header -->
            <div class="header">
                <h1>{{ $title }}</h1>
            </div>

            <!-- Body -->
            <div class="content">
                <div class="message-body">
                    {!! nl2br(e($messageContent)) !!}
                </div>

                @if($callToActionUrl && $callToActionText)
                    <div class="btn-wrapper">
                        <a href="{{ $callToActionUrl }}" class="btn">{{ $callToActionText }}</a>
                    </div>
                @endif
            </div>

            <!-- Footer -->
            <div class="footer">
                <div class="footer-logo">PAKAIApp</div>
                <p>&copy; {{ date('Y') }} PT Sinergi Kode Kreatif. Hak Cipta Dilindungi.</p>
                <p>Pesan ini dikirim secara otomatis oleh sistem. Harap tidak membalas email ini.</p>
            </div>
        </div>
    </div>
</body>
</html>
