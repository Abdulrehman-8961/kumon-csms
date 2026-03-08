<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Vacation Requires Planning</title>
</head>

<body style="padding:0; background:#ffffff; font-family: Arial, sans-serif; color:#333;">
    <div style="padding:20px; background:#ffffff;">

        <p>Dear Kumon Administrator</p>

        <p style="line-height:1.6;">
            This is a system generated notification that {{ $student_name }} has an upcoming vacation on {{ $date_range }} that requires planning.
        </p>

        <div style="background:#f6f8fb; border:1px solid #e3e8ef; padding:14px; margin:0 0 18px;">
            <div style="font-size:13px; color:#333; margin-bottom:8px;">Open the vacation record to plan this student:</div>
            <a href="{{ $plan_student_url }}" target="_blank" rel="noopener"
               style="display:inline-block; background:#263050; color:#ffffff; text-decoration:none; padding:10px 14px; font-size:14px; font-weight:700; border-radius:6px;">
                Plan Student
            </a>
        </div>

        <p>Kind regards,</p>

        <p>{{ $center_name }}</p>
    </div>
</body>

</html>