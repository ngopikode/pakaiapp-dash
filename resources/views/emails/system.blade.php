<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        /* Base styles */
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
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
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.04);
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }

        /* Glowing Top Accent */
        .top-accent {
            height: 6px;
            background: linear-gradient(90deg, #F97316 0%, #ff8c3a 100%);
        }

        /* Header */
        .header {
            background-color: #0F172A;
            padding: 35px 40px;
            text-align: center;
        }

        .header .logo {
            font-size: 24px;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.5px;
            margin-bottom: 8px;
        }

        .header .logo span {
            color: #F97316;
        }

        .header p {
            color: #94a3b8;
            margin: 0;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        /* Content */
        .content {
            padding: 40px;
            background-color: #ffffff;
        }

        .email-title {
            font-size: 22px;
            font-weight: 800;
            color: #0F172A;
            margin-top: 0;
            margin-bottom: 24px;
            line-height: 1.3;
            letter-spacing: -0.5px;
        }

        .message-body {
            font-size: 15px;
            line-height: 1.8;
            color: #475569;
        }
        
        .message-body p {
            margin: 0 0 16px 0;
        }

        /* Button */
        .btn-wrapper {
            text-align: center;
            margin: 35px 0 15px;
        }

        .btn {
            display: inline-block;
            background-color: #F97316;
            color: #ffffff !important;
            text-decoration: none;
            padding: 14px 36px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 15px;
            transition: all 0.2s ease;
            box-shadow: 0 6px 18px rgba(249, 115, 22, 0.25);
            letter-spacing: -0.2px;
        }

        /* Footer */
        .footer {
            background-color: #f8fafc;
            padding: 35px 40px;
            text-align: center;
            border-top: 1px solid #f1f5f9;
        }

        .footer p {
            margin: 0 0 10px 0;
            font-size: 12px;
            color: #64748b;
            line-height: 1.6;
        }

        .footer-logo {
            font-weight: 800;
            color: #94a3b8;
            font-size: 16px;
            margin-bottom: 12px;
            letter-spacing: 0.8px;
        }
        
        /* Responsive */
        @media screen and (max-width: 600px) {
            .main {
                border-radius: 0;
                border: none;
            }
            .wrapper {
                padding: 0;
            }
            .header {
                padding: 30px 24px;
            }
            .content {
                padding: 30px 24px;
            }
            .footer {
                padding: 30px 24px;
            }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="main">
            <!-- Top Orange Accent Bar -->
            <div class="top-accent"></div>

            <!-- Header -->
            <div class="header">
                <div class="logo">PAKAI<span>App</span></div>
                <p>Sistem Kasir Modern</p>
            </div>

            <!-- Body -->
            <div class="content">
                <h2 class="email-title">{{ $title }}</h2>
                
                <div class="message-body">
                    <?php
                    $body = e($messageContent);

                    // 1. Style OTP Code (a single line containing 4 to 6 digits)
                    $body = preg_replace(
                        '/^\s*([0-9]{4,6})\s*$/m',
                        '<div style="text-align: center; margin: 32px 0;"><div style="display: inline-block; font-size: 36px; font-weight: 800; color: #F97316; letter-spacing: 8px; padding: 16px 32px; background-color: #F8FAFC; border: 2px dashed #E2E8F0; border-radius: 12px; font-family: \'Courier New\', Courier, monospace; text-shadow: 0 1px 0 #fff; box-shadow: inset 0 1px 2px rgba(0,0,0,0.02);">$1</div></div>',
                        $body
                    );

                    // 2. Style Key-Value pairs
                    $body = preg_replace(
                        '/^(URL Dashboard|Email|Password|Nomor Tagihan|Total Tagihan|Metode):\s*(.+)$/mi',
                        '<div style="padding: 12px 16px; background-color: #F8FAFC; border-left: 4px solid #F97316; margin: 10px 0; border-radius: 0 8px 8px 0; text-align: left; box-shadow: 0 1px 2px rgba(0,0,0,0.01);"><strong style="color: #64748B; font-size: 11px; text-transform: uppercase; letter-spacing: 0.8px; display: block; margin-bottom: 4px;">$1</strong><span style="color: #0F172A; font-weight: 600; font-size: 15px; word-break: break-all;">$2</span></div>',
                        $body
                    );

                    // 3. Render
                    echo nl2br($body);
                    ?>
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
