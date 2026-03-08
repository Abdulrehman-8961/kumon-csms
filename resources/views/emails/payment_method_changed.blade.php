<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment Method Changed</title>
</head>

<body style="padding:0; background:#ffffff; font-family: Arial, sans-serif; color:#333;">
    <div style="padding:20px; background:#ffffff;">

        <p style="">Dear Kumon Administrator</p>

        <p>
            <strong>Client:</strong> {{ $client_name }}
        </p>

        <p style=" line-height:1.6;">
            This is a system generated notification that {{ $client_name }} has changed their payment notification from
            {{ $old_method }} to {{ $new_method }}
            performed by {{ $changed_by }} on {{ $changed_at }}.
        </p>

        @if(!empty($payment_change_id))
        <table cellpadding="0" cellspacing="0" style="margin:25px 0;">
            <tr>
                <td>
                    <a href="{{ url('/clients') }}"
                        style="
                        display:inline-block;
                        padding:14px 26px;
                        background:#263050;
                        color:#ffffff;
                        text-decoration:none;
                        border-radius:6px;
                        font-weight:bold;
                        font-size:14px;
                        ">
                        Confirm Change
                    </a>
                </td>
            </tr>
        </table>
        @endif

        <p style="">Kind regards,</p>

        <p style="">{{ $center_name }}</p>
    </div>
</body>

</html>
