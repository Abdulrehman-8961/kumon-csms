<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StatusChangeMail extends Mailable
{
    public $client;
    public $newStatus;

    public function __construct($client, $newStatus)
    {
        $this->client = $client;
        $this->newStatus = $newStatus;
    }

    public function build()
    {
        return $this->from('support@consultationamaltitek.com')
            ->subject('Contract Changed Status')
            ->view('emails.status_change');
    }
}
