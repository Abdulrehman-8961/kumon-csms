<?php

namespace App\Jobs;

use Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendAssetNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;

    /**
     * Create a new job instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        Mail::send('emails.renewal_email_asset', ['data' => $this->data], function ($message) {
            $message->to($this->data['emails']);
            $message->subject($this->data['subject']);
            $message->from('support@consultationamaltitek.com', $this->data['from_name']);
        });

        // Log the notification in the database
        DB::Table('notifications')->insert([
            'type' => 'Asset',
            'from_email' => $this->data['from_name'],
            'to_email' => implode(',', $this->data['emails']),
            'subject' => $this->data['subject']
        ]);
    }
}

