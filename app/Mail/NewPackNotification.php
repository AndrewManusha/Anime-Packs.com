<?php

namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewPackNotification extends Mailable
{
    use Queueable, SerializesModels;
    public $pack;

    public function __construct($pack)
    {
        $this->pack = $pack;
    }

    public function build()
    {
        return $this->markdown('emails.new_pack')
                    ->subject('🌟 New ' . $this->pack->type . ': ' . $this->pack->title);
    }
}
