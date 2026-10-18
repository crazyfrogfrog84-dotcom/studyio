<!doctype html>
<html lang="en">
<body style="margin:0;background:#f3f1e8;color:#1e2924;font-family:Arial,sans-serif;padding:32px 16px;">
    <main style="max-width:560px;margin:0 auto;background:#fbfaf6;border:1px solid #d9ded5;border-radius:18px;padding:32px;">
        <p style="color:#315f49;font-size:13px;font-weight:bold;letter-spacing:.08em;text-transform:uppercase;">StuDyIo</p>
        <h1 style="font-size:26px;line-height:1.25;">Reset your password</h1>
        <p>We received a request to reset the password for your StuDyIo account.</p>
        <p style="margin:28px 0;"><a href="{{ $resetUrl }}" style="display:inline-block;background:#244a38;border-radius:8px;color:#fff;padding:13px 20px;text-decoration:none;font-weight:bold;">Choose a new password</a></p>
        <p>This link expires in {{ $expiresIn }} minutes. If you did not request a password reset, you can ignore this email.</p>
        <p style="color:#4e5c55;font-size:13px;overflow-wrap:anywhere;">If the button does not work, open this link:<br><a href="{{ $resetUrl }}" style="color:#315f49;">{{ $resetUrl }}</a></p>
    </main>
</body>
</html>
