<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SystemEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $title;
    public $messageContent;
    public $callToActionText;
    public $callToActionUrl;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($title, $messageContent, $callToActionText = null, $callToActionUrl = null)
    {
        $this->title = $title;
        $this->messageContent = $messageContent;
        $this->callToActionText = $callToActionText;
        $this->callToActionUrl = $callToActionUrl;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->title)
                    ->view('emails.system');
    }
}
