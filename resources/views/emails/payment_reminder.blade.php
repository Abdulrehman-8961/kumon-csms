<div style="font-family: Arial; padding:20px; background:#ffffff;">
    <div style="max-width:650px;">
        <p>Dear {{ $client->firstname .' '. $client->lastname }}</p>

        <p>
            This is a friendly reminder to make a payment for the month of
            {{ \Carbon\Carbon::now()->format('F Y') }}
        </p>

        <div>
            @foreach($students as $student)
                <div>{{ $student->student_name }} ${{ number_format($student->amount, 2) }}</div>
            @endforeach
        </div>

        @php
            $totalAmount = $students->sum('amount');
        @endphp
        <p>Total Due: <strong>${{ number_format($totalAmount, 2) }}</strong></p>

        <p>Kind regards,</p>
        <p>{{ center_settings()->center_name ?? 'Kumon csms' }}</p>
    </div>
</div>

