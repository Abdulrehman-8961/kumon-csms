<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $client;
    public $students;

    public function __construct($client, $students)
    {
        $this->client   = $client;
        $this->students = $students;
    }

    public function build()
    {
        return $this->from('support@consultationamaltitek.com')
            ->subject('Upcoming Payment Reminder')
            ->view('emails.payment_reminder');
    }
}
    
