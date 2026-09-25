<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Email Verification – SEDI Electro</title>
    <style>
        /* ── Reset ────────────────────────────────────────────── */
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto,
                         Helvetica, Arial, sans-serif;
            background-color: #f0f2f5;
            color: #1a1a2e;
        }

        /* ── Wrapper ──────────────────────────────────────────── */
        .wrapper {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
        }

        /* ── Header ───────────────────────────────────────────── */
        .header {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            padding: 40px 40px 32px;
            text-align: center;
        }

        .header .logo-text {
            font-size: 26px;
            font-weight: 700;
            color: #e94560;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .header .logo-sub {
            font-size: 12px;
            color: #a0aec0;
            letter-spacing: 4px;
            text-transform: uppercase;
            margin-top: 4px;
        }

        /* ── Body ─────────────────────────────────────────────── */
        .body {
            padding: 40px 40px 32px;
        }

        .body h1 {
            font-size: 22px;
            font-weight: 600;
            color: #1a1a2e;
            margin-bottom: 12px;
        }

        .body p {
            font-size: 15px;
            line-height: 1.7;
            color: #4a5568;
            margin-bottom: 12px;
        }

        /* ── Code Box ─────────────────────────────────────────── */
        .code-box {
            background: linear-gradient(135deg, #0f3460 0%, #1a1a2e 100%);
            border-radius: 10px;
            padding: 28px 20px;
            text-align: center;
            margin: 28px 0;
        }

        .code-box .label {
            font-size: 12px;
            color: #a0aec0;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-bottom: 10px;
        }

        .code-box .code {
            font-size: 46px;
            font-weight: 800;
            color: #e94560;
            letter-spacing: 12px;
            font-variant-numeric: tabular-nums;
        }

        .code-box .expiry {
            font-size: 13px;
            color: #718096;
            margin-top: 10px;
        }

        /* ── Security Note ────────────────────────────────────── */
        .security-note {
            background: #fff7ed;
            border-left: 4px solid #f6ad55;
            border-radius: 6px;
            padding: 14px 16px;
            margin: 20px 0;
        }

        .security-note p {
            font-size: 13px;
            color: #744210;
            margin-bottom: 0;
        }

        /* ── Footer ───────────────────────────────────────────── */
        .footer {
            background: #f7f8fc;
            padding: 24px 40px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
        }

        .footer p {
            font-size: 12px;
            color: #a0aec0;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="wrapper">

        <!-- Header -->
        <div class="header">
            <div class="logo-text">SEDI Electro</div>
            <div class="logo-sub">Secure Platform</div>
        </div>

        <!-- Body -->
        <div class="body">
            <h1>Verify Your Email Address</h1>

            <p>
                Thank you for registering with <strong>SEDI Electro</strong>.
                To complete your account setup, please use the verification code below.
            </p>

            <!-- Code Box -->
            <div class="code-box">
                <div class="label">Your Verification Code</div>
                <div class="code">{{ $code }}</div>
                <div class="expiry">
                    ⏱ This code expires in <strong>{{ $expiresInMinutes }} minutes</strong>
                </div>
            </div>

            <p>
                Enter this code on the verification page to activate your account.
                Once verified, you will be able to log in and access all features.
            </p>

            <!-- Security note -->
            <div class="security-note">
                <p>
                    🔒 <strong>Security notice:</strong> SEDI Electro will never ask
                    you for this code via phone, chat, or any other channel. Do not
                    share this code with anyone. If you did not request this, you can
                    safely ignore this email.
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>
                This email was sent automatically by SEDI Electro.<br />
                &copy; {{ date('Y') }} SEDI Electro. All rights reserved.
            </p>
        </div>

    </div>
</body>
</html>
