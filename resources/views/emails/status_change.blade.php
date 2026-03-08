<div style="font-family: Arial; background:#f5f5f5; padding:20px;">
    <div style="max-width:650px; margin:auto; background:white; padding:25px; border-radius:8px;">

        {{-- <p><strong>From:</strong> vcm-noreply &lt;support@consultationamalitek.com&gt;</p>
        <p><strong>Subject:</strong> Upcoming Contract Renewal Notification!</p>

        <hr>

        <p><strong>Body:</strong></p> --}}
        <p>A Client has changed status from <strong>{{ @$client->is_active == 1 ? 'Actice' : 'Inactive' }}</strong> to
            <strong>{{ @$newStatus }}</strong></p>

        <p><strong>Client:</strong> {{ @$client->salutation }} {{ @$client->client_display_name }}</p>
        <p><strong>Primary Email:</strong> {{ @$client->email_address }}</p>
        <p><strong>Phone:</strong> {{ @$client->work_phone }}</p>
        <br>
        <p>For more details, please use this link <a href="{{ url('/clients') }}"
                target="_blank">here</a></p>

        <br>
        <br>
        <p>Consultation AmaltiTEK</p>

    </div>
</div>
