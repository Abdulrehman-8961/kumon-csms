<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Mail\AssetNotificationMail;
use Illuminate\Support\Facades\Mail;

class SendEmail extends Command
{
    protected $signature = 'send:email {data}';
    protected $description = 'Send email in background';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $data = unserialize(base64_decode($this->argument('data')));
        Mail::send(new AssetNotificationMail($data));
    }
}

