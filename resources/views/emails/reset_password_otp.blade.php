<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kode OTP Reset Password</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7f6; margin: 0; padding: 40px 20px; }
        .container { max-width: 500px; margin: 0 auto; background-color: #ffffff; padding: 40px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .header { text-align: center; padding-bottom: 24px; border-bottom: 2px solid #f0f0f0; margin-bottom: 24px; }
        .logo { font-size: 24px; font-weight: 800; color: #10b981; letter-spacing: 1px; }
        .content { text-align: center; color: #4b5563; line-height: 1.6; font-size: 15px; }
        .otp-container { margin: 30px 0; }
        .otp-box { font-size: 36px; font-weight: bold; color: #111827; background-color: #f3f4f6; padding: 16px 32px; border-radius: 8px; letter-spacing: 8px; display: inline-block; border: 1px dashed #d1d5db; }
        .warning { color: #ef4444; font-size: 14px; font-weight: 600; margin-top: 20px; padding: 10px; background-color: #fef2f2; border-radius: 8px; }
        .footer { margin-top: 40px; text-align: center; font-size: 12px; color: #9ca3af; padding-top: 20px; border-top: 1px solid #f0f0f0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">HRIS SYSTEM</div>
        </div>
        <div class="content">
            <p>Halo,</p>
            <p>Kami menerima permintaan untuk mengatur ulang (*reset*) password akun Anda. Berikut adalah kode <i>One-Time Password</i> (OTP) Anda:</p>
            
            <div class="otp-container">
                <div class="otp-box">{{ $otpCode }}</div>
            </div>
            
            <p>Kode ini hanya berlaku selama <strong>5 menit</strong>.</p>
            
            <div class="warning">
                Jika Anda tidak merasa meminta reset password, abaikan email ini. Akun Anda tetap aman.
            </div>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} HRIS System. Hak Cipta Dilindungi.<br>
            Pesan ini dibuat secara otomatis, mohon tidak membalas email ini.
        </div>
    </div>
</body>
</html>