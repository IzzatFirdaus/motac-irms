<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TestMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Optional data passed to the email view.
     *
     * @var array
     */
    public $data;

    /**
     * Create a new message instance.
     *
     * @param array $data (optional) Data to be passed to the email view.
     */
    public function __construct($data = [])
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // Use the correct blade view: resources/views/emails/test-email.blade.php
        return $this->subject('Testing Papercut SMTP with Laravel')
            ->view('emails.test-email')
            ->with('data', $this->data);
    }
}
