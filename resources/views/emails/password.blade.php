<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kumon Portal Invitation</title>
</head>

<body style="padding:0; background:#ffffff; font-family: Arial, sans-serif; color:#333;">
    <div style="padding:20px; background:#ffffff;">

        <p>Dear {{ $data['name'] ?? 'User' }},</p>

        <p style="line-height:1.6;">
            Your account invitation for the Kumon CSMS Portal has been created.
        </p>

        <div style="background:#f6f8fb; border:1px solid #e3e8ef; padding:14px; margin:0 0 18px;">
            <div style="font-size:13px; color:#333; margin-bottom:8px;">
                Use this temporary password to sign in:
            </div>
            <div style="font-size:16px; font-weight:700; color:#263050; margin-bottom:12px;">
                {{ $data['password'] ?? '' }}
            </div>
            <a href="https://kumon-csms.com" target="_blank" rel="noopener"
                style="display:inline-block; background:#263050; color:#ffffff; text-decoration:none; padding:10px 14px; font-size:14px; font-weight:700; border-radius:6px;">
                Open Kumon CSMS Portal
            </a>
        </div>

        <p style="line-height:1.6;">
            For security, you will be prompted to change your password at next login.
        </p>

        <p>Kind regards,</p>
        <p>Kumon CSMS</p>
    </div>
</body>

</html>
