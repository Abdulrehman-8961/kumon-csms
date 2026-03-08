<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Receipt</title>
</head>
<body style="font-family: Arial, sans-serif; color: #3f3f3f;">
    @if(!empty($data['receipt_html']))
        {!! $data['receipt_html'] !!}
    @else
        <p>Dear {{ $data['client_name'] }},</p>
        <p>Here is your official receipt for payment:</p>
        <table cellpadding="6" cellspacing="0" border="0" style="border-collapse: collapse;">
            <tr>
                <td><strong>Payment Method</strong></td>
                <td>{{ $data['payment_type'] }}</td>
            </tr>
            <tr>
                <td><strong>Payment Date</strong></td>
                <td>{{ $data['payment_date'] }}</td>
            </tr>
            <tr>
                <td><strong>Payment Month</strong></td>
                <td>{{ $data['kumon_month'] }}</td>
            </tr>
            <tr>
                <td><strong>Amount</strong></td>
                <td>${{ number_format($data['amount'], 2) }}</td>
            </tr>
            <tr>
                <td><strong>Reference No.</strong></td>
                <td>{{ $data['reference_no'] }}</td>
            </tr>
        </table>
        <p>Kind regards,</p>
        <p>{{ $data['center_name'] ?? 'Kumon csms' }}</p>
    @endif
</body>
</html>
