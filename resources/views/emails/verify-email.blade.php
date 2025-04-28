<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verifikasi Email Anda</title>
    <style>
        /* Responsive Style */
        @media only screen and (max-width: 600px) {
            .container {
                padding: 20px 15px !important;
            }
            .header h1 {
                font-size: 20px !important;
            }
            .content h2 {
                font-size: 20px !important;
            }
            .button {
                padding: 10px 16px !important;
                font-size: 14px !important;
            }
        }

        /* Button Hover */
        .button:hover {
            background-color: #ea580c !important; /* Lebih gelap saat hover */
        }

        /* Dark Mode Support */
        @media (prefers-color-scheme: dark) {
            body {
                background-color: #0f172a !important;
            }
            .container {
                background: #1e293b !important;
                color: #cbd5e1 !important;
            }
            .footer {
                background: #334155 !important;
                color: #94a3b8 !important;
            }
            a {
                color: #2cffea !important;
            }
        }
    </style>
</head>
<body style="background-color: #f8fafc; font-family: Arial, sans-serif; padding: 20px; margin: 0;">
    <div class="container" style="max-width: 600px; margin: auto; background: white; padding: 0; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); overflow: hidden;">
        
        <!-- Header -->
        <div class="header" style="background-color: #4A102A; padding: 20px; text-align: center;">
            <img src="https://ruanganagata.id/assets/img/illustrations/logo-asn.png" alt="Company Logo" style="height: 50px; margin-bottom: 10px;">
            <h1 style="color: white; font-size: 24px; margin: 0;">Selamat Datang di LMS Kami</h1>
        </div>

        <!-- Body -->
        <div class="content" style="padding: 30px;">
            <h2 style="color: #2cffea;">Halo, {{ $name }} 👋</h2>
            <p style="font-size: 16px; color: #475569; margin-bottom: 20px;">
                Terima kasih telah bergabung dengan platform kami! Klik tombol di bawah untuk memverifikasi email Anda.
            </p>
            <a href="{{ $url }}" class="button" style="display: inline-block; background-color: #2cffea; color: white; padding: 12px 20px; text-decoration: none; border-radius: 6px; font-weight: bold; transition: background-color 0.3s;">
                Verifikasi Email
            </a>
            <p style="font-size: 14px; color: #64748b; margin-top: 30px;">
                Jika Anda tidak mendaftar akun, Anda dapat mengabaikan email ini.
            </p>
        </div>

        <!-- Footer -->
        <div class="footer" style="background-color: #f1f5f9; padding: 20px; text-align: center; font-size: 12px; color: #94a3b8;">
            &copy; {{ date('Y') }} ruanganagata.id. Semua hak dilindungi.
            <br>
            <a href="https://ruanganagata.id" style="color: #2cffea; text-decoration: none;">Kunjungi Website Kami</a>
        </div>
    </div>
</body>
</html>
