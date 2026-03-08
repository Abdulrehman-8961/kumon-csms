<div style="font-family: Arial, Helvetica, sans-serif; padding:20px; background:#ffffff; color:#222;">
    <div style="max-width:650px; margin:0 auto;">

        {{-- Header --}}
        <div style="padding:10px 0 18px; border-bottom:1px solid #e6e6e6;">
            <div style="font-size:18px; font-weight:700;">Vacation Reminder</div>
            <div style="font-size:13px; color:#666; margin-top:4px;">
                {{ \Carbon\Carbon::now()->format('F Y') }}
            </div>
        </div>

        {{-- Greeting --}}
        <p style="margin:18px 0 10px;">
            Dear {{ $client->client_display_name }},
        </p>

        {{-- Message --}}
        <p style="margin:0 0 14px; line-height:1.5;">
            This is a friendly reminder to enter your child(s) upcoming vacation.
        </p>

        {{-- CTA --}}
        <div style="background:#f6f8fb; border:1px solid #e3e8ef; padding:14px; margin:0 0 18px;">
            <div style="font-size:13px; color:#333; margin-bottom:8px;">
                Please use the link below to fill out the online vacation form:
            </div>
            <a href="{{ url('/vacations') }}" target="_blank" rel="noopener"
               style="display:inline-block; background:#1a73e8; color:#ffffff; text-decoration:none;
                      padding:10px 14px; font-size:14px; font-weight:700;">
                Open Vacation Form
            </a>
        </div>

        {{-- Footer --}}
        <p style="margin:18px 0 0;">Kind regards,</p>
        <p style="margin:6px 0 0; font-weight:700;">{{ center_settings()->center_name ?? 'Kumon csms' }}</p>

    </div>
</div>